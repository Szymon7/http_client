<?php

namespace HttpClient;

use HttpClient\Exceptions\FailureResponse;
use Nyholm\Psr7\Uri;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

class Request extends Client
{
    /**
     * @throws FailureResponse
     */
    public function execute(string $method, $uri, array $options = []): ResponseInterface
    {
        if (!($uri instanceof UriInterface)) {
            $uri = new Uri($uri);
        }

        $this->request = $this->request->withUri($uri);

        foreach ($options['headers'] as $header => $headerBody) {
            $this->setHeader($header, $headerBody);
        }

        $this->setMethod($method);
        $this->setBody($options['body'] ?? null);

        $response = (new CurlController())
            ->prepare($this->request)
            ->execute();
        $this->validateResponse($response);

        return $response;
    }
}