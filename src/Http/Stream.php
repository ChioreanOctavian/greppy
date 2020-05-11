<?php


namespace greppy\Http;


use Psr\Http\Message\StreamInterface;

class Stream implements StreamInterface
{
    CONST DEFAULT_MEMORY = 5 * 1024 * 1024;
    CONST DEFAULT_MODE = "r+";

    /**
     * @var
     */
    private $stream;

    /**
     * @var int|null
     */
    private $size;

    /**
     * @var bool
     */
    private $writable;

    /**
     * @var bool
     */
    private $readable;

    /**
     * @var bool
     */
    private $seekable;

    public function __construct($handler, ?int $size = null)
    {
        $this->stream = $handler;
        $this->size = $size;
        $this->writable = $this->readable = $this->seekable = true;
    }

    /**
     * @param string $content
     * @return static
     */
    public static function createFromString(string $content): self
    {
        $stream = fopen(sprintf("php://temp/maxmemory:%s", self::DEFAULT_MEMORY), self::DEFAULT_MODE);
        fwrite($stream, $content);
        return new self($stream, strlen($content));
    }

    /**
     * @return false|string
     */
    public function __toString()
    {
        return $this->getContents();
    }

    /**
     * @return bool|void
     */
    public function close()
    {
        return fclose($this->stream);
    }

    /**
     * @return Stream|resource|null
     */
    public function detach()
    {
        if (!isset($this->stream)) {
            return null;
        }

        $message = clone $this;
        unset($message->stream);
        $message->size = 0;
        $message->readable = $this->writable = $this->seekable = false;

        return $message;
    }

    /**
     * @return int|null
     */
    public function getSize(): ?int
    {
        return $this->size;
    }

    /**
     * @return false|int
     */
    public function tell()
    {
        return ftell($this->stream);
    }

    /**
     * @return bool
     */
    public function eof(): bool
    {
        if (!isset($this->stream)) {
            return true;
        }

        return feof($this->stream);
    }

    /**
     * @return bool
     */
    public function isSeekable(): bool
    {
        return $this->seekable;
    }

    /**
     * @param int $offset
     * @param int $whence
     * @return int
     */
    public function seek($offset, $whence = SEEK_SET): int
    {
        return fseek($this->stream, $offset);
    }

    /**
     * @return int
     */
    public function rewind(): int
    {
        return $this->seek(0);
    }

    /**
     * @return bool
     */
    public function isWritable(): bool
    {
        return $this->writable;
    }

    /**
     * @param string $string
     * @return false|int
     */
    public function write($string)
    {
        if (!isset($this->stream)) {
            return 0;
        }
        if (!$this->isWritable()) {
            return 0;
        }

        return fwrite($this->stream, $string);
    }

    /**
     * @return bool
     */
    public function isReadable(): bool
    {
        return $this->readable;
    }

    /**
     * @param int $length
     * @return false|string
     */
    public function read($length)
    {
        if (!isset($this->stream)) {
            return "";
        }
        if ($this->isReadable()) {
            return "";
        }

        return fread($this->stream, $length);
    }

    /**
     * @return false|string
     */
    public function getContents()
    {
        $this->rewind();
        return stream_get_contents($this->stream);
    }

    /**
     * @param null $key
     * @return array|mixed|null
     */
    public function getMetadata($key = null)
    {
        return stream_get_meta_data($this->stream);
    }
}