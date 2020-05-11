<?php

namespace greppy\Http;

use Psr\Http\Message\StreamInterface;

class Response extends Message
{
    /**
     * @var int
     */
    private $statusCode;

    /**
     * @var string
     */
    private $reasonPhrase;

    public function __construct(
        StreamInterface $body,
        array $headers = [],
        int $statusCode = 200,
        string $protocolVersion = '',
        string $reasonPhrase =''
    ) {
        $this->statusCode= $statusCode;
        $this->reasonPhrase = $reasonPhrase;

        parent::__construct($protocolVersion, $headers, $body);
    }

    public function send(): void
    {
        $this->sendHeaders();
        $this->sendBody();
    }

    private function sendHeaders(): void
    {
        foreach ($this->getHeaders() as $key => $value){
            header($key. ": ". implode($value, ','));
        }
    }

    private function sendBody(): void
    {
        echo $this->getBody()->getContents();
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @param $code
     * @param string $reasonPhrase
     * @return Response
     */
    public function withStatus($code, $reasonPhrase = ''): self
    {
        $response = clone $this;
        $response->statusCode = $code;

        return $response;
    }

    /**
     * @return string
     */
    public function getReasonPhrase(): string
    {
        if (isset($this->reasonPhrase)) {
            return $this->reasonPhrase;
        }

        return "";
    }
}
