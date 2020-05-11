<?php


namespace greppy\Http;


use Psr\Http\Message\UriInterface;

class URI implements UriInterface
{
    /**
     * @var string
     */
    private $scheme;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $host;

    /**
     * @var int|null
     */
    private $port;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $query;

    /**
     * @var string
     */
    private $fragment;

    public function __construct(
        string $host,
        ?int $port = null,
        string $path = '',
        string $query = '',
        string $scheme = '',
        string $user = '',
        string $password = '',
        string $fragment = ''
    ) {
        $this->scheme = $scheme;
        $this->user = $user;
        $this->password = $password;
        $this->host = $host;
        $this->port = $port;
        $this->path = $path;
        $this->query = $query;
        $this->fragment = $fragment;
    }

    /**
     * @return URI
     */
    public static function createUriFromGlobals(): self
    {
        $host = $_SERVER['HTTP_HOST'];

        if (isset($_SERVER['SERVER_PORT'])) {
            $port = $_SERVER['SERVER_PORT'];
        }
        if (isset($_SERVER['REQUEST_URI'])) {
            $path = parse_url($_SERVER['REQUEST_URI'])['path'];
        }
        if (isset($_SERVER['QUERY_STRING'])) {
            $query = $_SERVER['QUERY_STRING'];
        }
        if (isset($_SERVER['REQUEST_SCHEME'])) {
            $scheme = $_SERVER['REQUEST_SCHEME'];
        }
        if (isset($_SERVER['REQUEST_SCHEME'])) {
            $scheme = $_SERVER['REQUEST_SCHEME'];
        }
        if (isset($_SERVER['REQUEST_SCHEME'])) {
            $scheme = $_SERVER['REQUEST_SCHEME'];
        }

        return new self($host, $port, $path, $query, $scheme);
    }

    /**
     * @return string
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * @return string
     */
    public function getAuthority(): string
    {
        return $this->getUserInfo() . "@" . $this->getHost() . ":" . $this->getPort();
    }

    /**
     * @return string
     */
    public function getUserInfo(): string
    {
        return $this->user . ":" . $this->password;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return int|null
     */
    public function getPort(): ?int
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * @return string
     */
    public function getFragment(): string
    {
        return $this->fragment;
    }

    /**
     * @param string $scheme
     * @return URI
     */
    public function withScheme($scheme): self
    {
        $uri = clone $this;
        $uri->scheme = $scheme;

        return $uri;
    }

    /**
     * @param string $user
     * @param null $password
     * @return URI
     */
    public function withUserInfo($user, $password = null): self
    {
        $uri = clone $this;
        $uri->user = $user;
        $uri->password = $password;

        return $uri;
    }

    /**
     * @param string $host
     * @return URI
     */
    public function withHost($host): self
    {
        $uri = clone $this;
        $uri->host = $host;

        return $uri;
    }

    /**
     * @param int|null $port
     * @return URI
     */
    public function withPort($port): self
    {
        $uri = clone $this;
        $uri->port = $port;

        return $uri;

    }

    /**
     * @param string $path
     * @return URI
     */
    public function withPath($path): self
    {
        $uri = clone $this;
        $uri->path = $path;

        return $uri;
    }

    /**
     * @param string $query
     * @return URI
     */
    public function withQuery($query): self
    {
        $uri = clone $this;
        $uri->query = $query;

        return $uri;
    }

    /**
     * @param string $fragment
     * @return URI
     */
    public function withFragment($fragment): self
    {
        $uri = clone $this;
        $uri->fragment = $fragment;

        return $uri;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getScheme() . "://" . $this->getAuthority() . $this->getPath() . "?" . $this->getQuery() . "#" . $this->getFragment();
    }
}