<?php

namespace HttpClient;

use Nyholm\Psr7\Factory\HttplugFactory;
use Psr\Http\Message\RequestInterface;

const AVAILABLE_METHODS = [
    'GET',
    'POST',
    'HEAD',
    'PUT',
    'DELETE',
    'CONNECT',
    'OPTIONS',
    'TRACE',
    'PATCH',
];

class CurlController
{
    private \CurlHandle $curlHandle;

    public function __construct()
    {
        $this->curlHandle = curl_init();
    }

    public function prepare(RequestInterface $request): static
    {
        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLINFO_HEADER_OUT => true,
            CURLOPT_HEADER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_AUTOREFERER => true,
            CURLOPT_CONNECTTIMEOUT => 120,
            CURLOPT_TIMEOUT => 120,
        ];

        curl_setopt_array($this->curlHandle, $options);
        $this->setHeaders($request);
        $this->setMethod($request);
        $this->setUrl($request);

        return $this;
    }

    public function execute(): \Psr\Http\Message\ResponseInterface
    {
        $result = curl_exec($this->curlHandle);
        $response = $this->buildResponse($result);
        curl_close($this->curlHandle);

        return $response;
    }

    private function buildResponse(string $result): \Psr\Http\Message\ResponseInterface
    {
        $errors = curl_error($this->curlHandle);
        $code = curl_getinfo($this->curlHandle, CURLINFO_HTTP_CODE) ?: 0;
        $headers = curl_getinfo($this->curlHandle, CURLINFO_HEADER_OUT);

        $headers = $this->headersStringToArray($headers);

        return (new HttplugFactory())->createResponse($code, null, $headers, $result ?: $errors);
    }

    public function headersStringToArray(string $headers): array
    {
        $outHeaders = explode("\n", $headers);
        $outHeaders = array_filter($outHeaders, function ($value) {
            return $value !== '' && $value !== ' ' && strlen($value) != 1;
        });

        $headers = [];
        foreach ($outHeaders as $header) {
            $header = explode(":", $header);
            if (!isset($header[1])) {
                continue;
            }

            $headerName = $header[0];
            unset($header[0]);

            $headers[$headerName] = trim(implode('', $header));
        }

        return $headers;
    }

    public function setHeaders(RequestInterface $request): static
    {
        $headers = [];
        foreach ($request->getHeaders() as $header => $values) {
            $headers[] = $request->getHeaderLine($header);
        }

        curl_setopt($this->curlHandle, CURLOPT_HTTPHEADER, $headers);

        return $this;
    }

    public function setMethod(RequestInterface $request): static
    {
        if (in_array($request->getMethod(), AVAILABLE_METHODS)) {
            curl_setopt($this->curlHandle, CURLOPT_CUSTOMREQUEST, $request->getMethod());
            curl_setopt($this->curlHandle, CURLOPT_POSTFIELDS, $request->getUri()->getQuery());
        }

        return $this;
    }

    public function setUrl(RequestInterface $request): static
    {
        curl_setopt($this->curlHandle, CURLOPT_URL, $request->getUri()->__toString());

        return $this;
    }
}