<?php

namespace Test;

use PHPUnit\Framework\TestCase;
use Noweh\MarvelMemories\MarvelService;
use Noweh\MarvelMemories\MarvelClient;
use Noweh\MarvelMemories\DBAdapter;

class MarvelServiceTest extends TestCase
{
    public function testFindRandomComic(): void
    {
        $comicData = new \stdClass;
        $comicData->id = 123;
        $comicData->title = 'test title';
        $comicData->thumbnail = null;
        $comicData->urls = [];
        $comicData->creators = new \stdClass;
        $comicData->creators->items = [];
        $comicDataDate = new \stdClass;
        $comicDataDate->type = 'onsaleDate';
        $comicDataDate->date = '2022-01-28';
        $comicData->dates = [
            $comicDataDate
        ];

        $dbAdapterMock = $this->createMock(DBAdapter::class);
        $marvelClientMock = $this->createMock(MarvelClient::class);
        $marvelClientMock->expects($this->once())
            ->method('performRequest')
            ->with('GET', 'comics', $this->anything())
            ->willReturn([$comicData]);
        $service = new MarvelService($dbAdapterMock, $marvelClientMock);
        $result = $service->findRandomComic();
        $this->assertInstanceOf(\stdClass::class, $result);
        $this->assertEquals($comicData->id, $result->id);
    }
}
