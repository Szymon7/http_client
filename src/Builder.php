<?php

namespace HttpClient;

use HttpClient\Exceptions\FailureResponse;
use Psr\Http\Message\ResponseInterface;

class Builder extends Client
{
    /**
     * @throws FailureResponse
     */
    public function execute(): ResponseInterface
    {
        if (!empty($this->authorization)) {
            $this->setHeader('Authorization', $this->authorization);
        }

        $response = (new CurlController())->prepare($this->request)->execute();
        $this->validateResponse($response);

        return $response;
    }

    public function setHost(string $host): static
    {
        $uri = $this->request->getUri()->withHost($host);
        $this->request = $this->request->withUri($uri);
        return $this;
    }

    public function setScheme(string $scheme): static
    {
        $uri = $this->request->getUri()->withScheme($scheme);
        $this->request = $this->request->withUri($uri);
        return $this;
    }

    public function setPort(?int $port): static
    {
        $uri = $this->request->getUri()->withPort($port);
        $this->request = $this->request->withUri($uri);
        return $this;
    }

    public function setAuthenticationPass(string $user, string $pass): static
    {
        $uri = $this->request->getUri()->withUserInfo($user, $pass);
        $this->request = $this->request->withUri($uri);
        return $this;
    }

    public function setAuthenticationBearer(string $bearer): static
    {
        if (empty($bearer)) {
            $this->authorization = '';
            return $this;
        }

        $this->authorization = 'Bearer ' . $bearer;
        return $this;
    }

}