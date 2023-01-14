<?php

namespace HttpClient;

class Request extends \HttpClient\Message implements \Psr\Http\Message\RequestInterface
{
    private string $Uri;

    public function __construct()
    {
    }

    public function getRequestTarget()
    {
        // TODO Implement getRequestTarget() method.
    }

    public function withRequestTarget($requestTarget)
    {
        // TODO: Implement withRequestTarget() method.
    }

    public function getMethod()
    {
        // TODO: Implement getMethod() method.
    }

    public function withMethod($method)
    {
        // TODO: Implement withMethod() method.
    }

    public function getUri()
    {
        // TODO: Implement getUri() method.
    }

    public function withUri(\Psr\Http\Message\UriInterface $uri, $preserveHost = false)
    {
        // TODO: Implement withUri() method.
    }
}