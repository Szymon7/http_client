<?php

namespace HttpClient;

use Nyholm\Psr7\Factory\HttplugFactory;
use Nyholm\Psr7\Stream;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use HttpClient\Exceptions\FailureResponse;

class Client
{
    protected RequestInterface $request;
    protected string $authorization;

    public function __construct(string $method = Variables::METHOD_GET, $uri = '')
    {
        $this->request = (new HttplugFactory())->createRequest($method, $uri);
    }

    public function setMethod(string $method): static
    {
        $request = $this->request->withMethod($method);
        $this->request = $request;
        return $this;
    }

    public function setBody(string $body): static
    {
        $request = $this->request->withBody(Stream::create($body));
        $this->request = $request;
        return $this;
    }

    public function setHeader(string $header, string $value): static
    {
        $this->request = $this->request->withHeader($header, $value);
        return $this;
    }

    public function setQuery(array $params): static
    {
        $query = [];
        if (!empty($params)) {
            foreach ($params as $name => $param) {
                $query[] = $name . '=' . $param;
            }

            $query = implode('&', $query);
        }

        $uri = $this->request->getUri()->withQuery(!empty($params) ? $query : '');
        $this->request = $this->request->withUri($uri);
        return $this;
    }

    /**
     * @throws FailureResponse
     */
    protected function validateResponse(ResponseInterface $response): static
    {
        if ($response->getStatusCode() < 200 || $response->getStatusCode() > 299) {
            throw new FailureResponse($response->getStatusCode() . ' ' . $response->getReasonPhrase());
        }
        return $this;
    }
}