<?php

namespace HttpClient;

class Message implements \Psr\Http\Message\MessageInterface
{
    const Content_Type = 'Content-Type';
    const Content_Disposition = 'Content-Disposition';
    const Content_Length = 'Content-Length';
    const User_Agent = 'User-Agent';
    const Referer = 'Referer';
    const Host = 'Host';
    const Authorization = 'Authorization';
    const Proxy_Authorization = 'Proxy-Authorization';
    const If_Modified_Since = 'If-Modified-Since';
    const If_Unmodified_Since = 'If-Modified-Since';
    const From = 'From';
    const Location = 'Location';
    const Max_Forwards = 'Max-Forwards';

    private string $protocolVersion;
    private array $headers;
    private \Psr\Http\Message\StreamInterface $body;

    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    public function withProtocolVersion($version): Message|static
    {
        $this->protocolVersion = $version;
        return $this;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function hasHeader($name): bool
    {
        return $this->findHeader($name) !== null;
    }

    public function getHeader($name): ?array
    {
        $header = $this->findHeader($name);

        if ($header === null) {
            return null;
        }

        return $header['value'];
    }

    public function getHeaderLine($name): string
    {
        return implode(',', $this->getHeader($name));
    }

    public function withHeader($name, $value): Message|static
    {
        if (($header = $this->findHeader($name)) !== null) {
            unset($this->headers[$header['name']]);
        }

        $this->headers[$name] = [$value];
        return $this;
    }

    public function withAddedHeader($name, $value): Message|static
    {
        if (($header = $this->findHeader($name)) === null || $this->isSingletonHeader($name)) {
            throw new \InvalidArgumentException();
        }

        $this->headers[$header['name']] = [$value];
        return $this;
    }

    public function withoutHeader($name): Message|static
    {
        if (($header = $this->findHeader($name)) === null) {
            return $this;
        }

        unset($this->headers[$header['name']]);
        return $this;
    }

    public function getBody(): \Psr\Http\Message\StreamInterface
    {
        return $this->body;
    }

    public function withBody(\Psr\Http\Message\StreamInterface $body): Message|static
    {
        $this->body = $body;
        return $this;
    }

    private function findHeader($name): ?array
    {
        foreach ($this->headers as $header => $value) {
            if (strtolower($header) === strtolower($name)) {
                return [
                    'name' => $header,
                    'value' => $value,
                ];
            }
        }

        return null;
    }

    private function isSingletonHeader(string $header): bool
    {
        return $header === $this::Content_Type
            || $header === $this::Content_Disposition
            || $header === $this::Content_Length
            || $header === $this::User_Agent
            || $header === $this::Referer
            || $header === $this::Host
            || $header === $this::Authorization
            || $header === $this::Proxy_Authorization
            || $header === $this::If_Modified_Since
            || $header === $this::If_Unmodified_Since
            || $header === $this::From
            || $header === $this::Location
            || $header === $this::Max_Forwards;
    }
}