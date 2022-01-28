<?php

namespace Noweh\MarvelMemories;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Response;
use JsonException;

class Util
{
    /**
     * Transforms an url in tinyUrl to take up less space in a Tweet
     * @param string $url
     * @return string
     * @throws JsonException
     */
    public static function getTinyUrl(string $url): string
    {
        $response = self::executeRequest($url);
        self::validate($response);
        return $response->getBody()->getContents();
    }

    private static function executeRequest(string $url): Response
    {
        try {
            $client = new Client();
            return $client->get('https://tinyurl.com/api-create.php?url=' . $url);
        } catch (ClientException | ServerException | GuzzleException $e) {
            throw new \RuntimeException(json_encode($e->getMessage(), JSON_THROW_ON_ERROR));
        }
    }

    private static function validate(Response $response): void
    {
        if ($response->getStatusCode() >= 400) {
            throw new \RuntimeException(
                json_encode(['message' => 'cURL error with tinyurl call'], JSON_THROW_ON_ERROR),
                $response->getStatusCode()
            );
        }
    }
}
