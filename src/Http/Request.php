<?php

namespace greppy\Http;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class Request extends Message
{
    /**
     * @var string
     */
    private $method;

    /**
     * @var UriInterface
     */
    private $URI;

    /**
     * @var string
     */
    private $requestTarget;

    /**
     * @var array
     */
    private $parameter;

    /**
     * @var array
     */
    private $cookie;

    public function __construct(
        string $protocolVersion,
        array $headers,
        StreamInterface $body,
        string $method,
        UriInterface $uri,
        array $cookie,
        string $requestTarget,
        array $parameter
    ) {
        $this->method = $method;
        $this->URI = $uri;
        $this->cookie= $cookie;
        $this->requestTarget = $requestTarget;
        $this->parameter = $parameter;

        parent::__construct($protocolVersion, $headers, $body);
    }

    /**
     * @return static
     */
    public static function createFromGlobals(): self
    {
        $protocolVersion = $_SERVER['SERVER_PROTOCOL'];
        $protocolVersion = explode("/", $protocolVersion);
        $protocolVersion = $protocolVersion[1];

        foreach ($_SERVER as $item => $value){
            if( explode("_", $item)[0] == "HTTP")
                $headers[$item] =  $value;
        }
        $bodyStream = file_get_contents("php://input", "r+");
        $stream = Stream::createFromString($bodyStream);

        $uri = URI::createUriFromGlobals();

        $method = $_SERVER['REQUEST_METHOD'];

        $cookie = $_COOKIE;

        $requestTarget = $_SERVER['HTTP_HOST'];
        $parameter =[];
        if (!empty($_GET)){
            foreach ($_GET as $item => $value){
                $parameter[$item] = $value;
            }
        }
        if (!empty($_POST)){
            foreach ($_POST as $item => $value){
                $parameter[$item] = $value;
            }
        }

        return new self($protocolVersion, $headers, $stream, $method, $uri, $cookie, $requestTarget, $parameter);
    }

    /**
     * @return array
     */
    public function getAllParameter(): array
    {
        return $this->parameter;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getParameter(string $name)
    {
        return $this->parameter[$name];
    }

    /**
     * @param string $name
     * @return array|mixed
     */
    public function getCookie(string $name)
    {
        if (isset($this->cookie[$name])){
            return $this->cookie[$name];
        }

        return [];
    }

    /**
     * @param string $file
     * @param string $path
     * @return bool
     */
    public function moveUploadedFile(string $file, string $path): bool
    {
        return move_uploaded_file($file, $path);
    }

    /**
     * @return string
     */
    public function getPath(): string
    {

        return $this->URI->getPath();
    }

    /**
     * @return string
     */
    public function getRequestTarget(): string
    {
        return $this->requestTarget;
    }

    /**
     * @param $requestTarget
     * @return Request
     */
    public function withRequestTarget($requestTarget): self
    {
        $request = clone $this;
        $request->requestTarget = $requestTarget;

        return $request;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param $method
     * @return Request
     */
    public function withMethod($method): self
    {
        $request = clone $this;
        $request->method = $method;

        return $request;
    }

    /**
     * @return UriInterface
     */
    public function getUri(): UriInterface
    {
        return $this->URI;
    }

    /**
     * @param UriInterface $uri
     * @param bool $preserveHost
     * @return Request
     */
    public function withUri(UriInterface $uri, $preserveHost = false): self
    {
        $request = clone $this;
        $request->URI = $uri;

        return $request;
    }
}
