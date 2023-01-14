<?php

namespace HttpClient;

class Uri implements \Psr\Http\Message\UriInterface
{
    private string $scheme;
    private string $user;
    /**
     * @var mixed|string|null
     */
    private mixed $password;
    private string $host;
    private int $port;
    private string $path;
    private string $query;
    private string $fragment;

    public function __construct(string $url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException();
        }

        $this->withScheme(parse_url($url, PHP_URL_SCHEME));
        $this->withUserInfo(
            parse_url($url, PHP_URL_USER),
            parse_url($url, PHP_URL_PASS)
        );
        $this->withHost(parse_url($url, PHP_URL_HOST));
        $this->withPort(parse_url($url, PHP_URL_PORT));
        $this->withPath(parse_url($url, PHP_URL_PATH));
        $this->withQuery(parse_url($url, PHP_URL_QUERY));
        $this->withFragment(parse_url($url, PHP_URL_FRAGMENT));
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function getAuthority(): string
    {
        return
            ($this->user ? ($this->user . ':' . $this->password . '@') : '')
            . $this->host
            . ($this->port ? ':' . $this->port : '');
    }

    public function getUserInfo(): string
    {
        return $this->user ? ($this->user . ':' . $this->password) : '';
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getFragment(): string
    {
        return $this->fragment;
    }

    /**
     * @param string $scheme
     */
    public function withScheme($scheme): Uri|static
    {
        $this->scheme = $scheme;
        return $this;
    }

    /**
     * @param string $user
     * @param string $password
     */
    public function withUserInfo($user, $password = null): Uri|static
    {
        $this->user = $user;
        $this->password = $password;
        return $this;
    }

    /**
     * @param string $host
     */
    public function withHost($host): Uri|static
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @param int|null $port
     */
    public function withPort($port): Uri|static
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @param string $path
     */
    public function withPath($path): Uri|static
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @param string $query
     */
    public function withQuery($query): Uri|static
    {
        $this->query = $query;
        return $this;
    }

    /**
     * @param string $fragment
     */
    public function withFragment($fragment): Uri|static
    {
        $this->fragment = str_replace('#', '', $fragment);
        return $this;
    }

    private function preparePath(): string
    {
        $explodedPath = explode("/", $this->path);
        if (($explodedPath[0] === '' && !isset($explodedPath[1]))) {
            return '';
        }

        $prefix = $explodedPath[0] === '' ? '' : '/';

        return $prefix . $this->path;
    }

    public function __toString(): string
    {
        return $this->scheme
            . '://' . $this->getAuthority()
            . $this->preparePath()
            . '?' . $this->query
            . '#' . $this->fragment;
    }
}