<?php

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Noweh\TwitterApi\Client as TwitterClient;
use Noweh\MarvelMemories\MarvelService;
use Noweh\MarvelMemories\MarvelClient;
use Noweh\MarvelMemories\DBAdapter;

// Only allowed for cli
if (PHP_SAPI !== 'cli') {
    die('Not allowed');
}

$start = microtime(true);

// Load .env data
$dotenv = Dotenv::createUnsafeImmutable(__DIR__.'/config', '.env');
$dotenv->safeLoad();

try {
    // Retrieve Twitter and Marvel env data and load Clients
    $marvelSettings = [];
    $twitterSettings = [];
    foreach (getenv() as $settingKey => $settingValue) {
        if (str_starts_with($settingKey, 'MARVEL_')) {
            $marvelSettings[str_replace('marvel_', '', mb_strtolower($settingKey))] = $settingValue;
        }
        if (str_starts_with($settingKey, 'TWITTER_')) {
            $twitterSettings[str_replace('twitter_', '', mb_strtolower($settingKey))] = $settingValue;
        }
    }
    
    $dbAdapter = new DBAdapter(__DIR__ . '//database//db.sqlite');
    $marvelClient = new MarvelClient($marvelSettings['private_key'], $marvelSettings['public_key']);
    // Tweet a random comic details
    $return = (new TwitterClient($twitterSettings))->tweet()->performRequest('POST', [
        'text' => (new MarvelService($dbAdapter, $marvelClient))
            ->findRandomComicFormattedForTweet()
    ]);

    echo "script completed without error\r\n";
} catch (Exception | \GuzzleHttp\Exception\GuzzleException $e) {
    echo "error in script: " . $e->getMessage() . "\r\n";
}

echo 'execution time ' . round(microtime(true) - $start, 2) . ' seconds';
