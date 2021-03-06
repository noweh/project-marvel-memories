<?php

namespace Noweh\MarvelMemories;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use RuntimeException;

class MarvelClient
{
    private const API_BASE_URI = 'https://gateway.marvel.com/v1/public/';

    /**
     * Constructor
     * @param string $private_key
     * @param string $public_key
     */
    public function __construct(
        public readonly string $private_key,
        public readonly string $public_key
    ) {}

    /**
     * Perform the request to Marvel API
     * @param string $method
     * @param string $uri
     * @param array<string, int|string> $searchParams
     * @return array<int, \stdClass>
     * @throws \JsonException|\RuntimeException
     */
    public function performRequest(string $method, string $uri, array $searchParams = []): array
    {
        try {
            $timestamp = time();

            $client = new Client(['base_uri' => self::API_BASE_URI]);

            $response = $client->request(
                $method,
                $uri . '?ts=' . $timestamp .
                '&apikey=' . $this->public_key .
                '&hash=' . md5($timestamp . $this->private_key . $this->public_key) .
                '&' . http_build_query($searchParams, '', '&')
            );

            $body = json_decode($response->getBody()->getContents(), false, 512, JSON_THROW_ON_ERROR);

            if (!$body instanceof \stdClass || $response->getStatusCode() >= 400) {
                $error = new \stdClass();
                $error->message = 'cURL error';
                throw new RuntimeException(
                    json_encode($error, JSON_THROW_ON_ERROR),
                    $response->getStatusCode()
                );
            }

            return $body->data->results;
        } catch (ClientException | ServerException | GuzzleException $e) {
            throw new RuntimeException(json_encode($e->getMessage(), JSON_THROW_ON_ERROR));
        }
    }
}
