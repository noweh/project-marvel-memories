<?php

namespace Noweh\MarvelMemories;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
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
        try {
            $client = new Client();
            $response = $client->get('https://tinyurl.com/api-create.php?url=' . $url);

            if ($response->getStatusCode() >= 400) {
                $error = new \stdClass();
                $error->message = 'cURL error with tinyurl call';
                throw new \RuntimeException(
                    json_encode($error, JSON_THROW_ON_ERROR),
                    $response->getStatusCode()
                );
            }

            return $response->getBody()->getContents();
        } catch (ClientException | ServerException | GuzzleException $e) {
            throw new \RuntimeException(json_encode($e->getMessage(), JSON_THROW_ON_ERROR));
        }
    }
}
