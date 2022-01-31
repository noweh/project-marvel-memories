<?php

namespace Noweh\MarvelMemories;

class MarvelService
{
    /**
     * Constructor
     * @param DBAdapter $dbAdapter
     * @param MarvelClient $marvelClient
     */
    public function __construct(
        private readonly DBAdapter $dbAdapter,
        private readonly MarvelClient $marvelClient
    ) {}

    /**
     * Find a random comic from 1960 to now,
     * with a formatted text for Tweet
     * @return string
     * @throws \JsonException
     */
    public function findRandomComicFormattedForTweet(): string
    {
        // Search for a comic that does not already exist in DB / already Tweeted
        do {
            $comicDetails = $this->findRandomComic();
        } while (!(array)$comicDetails || $this->dbAdapter->searchCoverId($comicDetails->id));

        // Add the comic ID in DB
        $this->dbAdapter->addCoverId($comicDetails->id);

        $text = "[COVER] Marvel Comics presents $comicDetails->title.\r\n" .
        "Sale dated from $comicDetails->saleDate ($comicDetails->url).\r\n";

        if ($comicDetails->image) {
            $text .= "Cover: $comicDetails->image";
        }

        foreach ($comicDetails->creators as $role => $name) {
            $text .= "\r\n$role: $name";
            // Add a control to prevent the tweet from being too long (280 chars)
            if (strlen($text) > 250) {
                break;
            }
        }

        $text .= "\r\n#marvel #comics";

        return $text;
    }

    /**
     * Find a random comic from 1960 to now
     * @return \stdClass
     * @throws \JsonException|\RuntimeException|\Exception
     */
    public function findRandomComic(): \stdClass
    {
        $referenceDate = new \DateTime(random_int(1960, (int) date('Y')) . '-' . random_int(1, 12) . '-01');
        $firstDay = $referenceDate->format('Y-m-d');
        $lastDay = $referenceDate->modify('last day of this month')->format('Y-m-d');

        $return = $this->marvelClient->performRequest(
            'GET',
            'comics',
            [
                'limit' => 100,
                'format' => 'comic',
                'formatType' => 'comic',
                'dateRange' => $firstDay . ',' . $lastDay
            ]
        );

        $comicDetails = new \stdClass();

        if ($return) {
            // Take a random item among 100
            shuffle($return);

            $randomComicData = $return[0];

            $comicDetails->id = $randomComicData->id;
            $comicDetails->title = $randomComicData->title;

            $comicDetails->saleDate = null;
            foreach ($randomComicData->dates as $dateData) {
                if ($dateData->type === 'onsaleDate') {
                    $comicDetails->saleDate = (new \DateTime($dateData->date))->format('F Y');
                }
            }

            $comicDetails->image = null;
            if ($randomComicData->thumbnail) {
                $comicDetails->image = Util::getTinyUrl($randomComicData->thumbnail->path . '.' . $randomComicData->thumbnail->extension);
            }

            $comicDetails->url = null;
            foreach ($randomComicData->urls as $url) {
                if ($url->type === 'detail') {
                    $comicDetails->url = Util::getTinyUrl($url->url);
                }
            }

            $comicDetails->creators = [];
            foreach ($randomComicData->creators->items as $creator) {
                $comicDetails->creators[ucfirst($creator->role)] = $creator->name;
            }
        }

        return $comicDetails;
    }
}
