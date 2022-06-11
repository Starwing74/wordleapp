<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class CallApiService
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getWordFromWordle(int $int): array
    {
        $response = $this->client->request(
            'GET',
            'http://alixgoguey.fr/words/wordapi.php?cmd=rand&size=' . $int
        );

        return $response->toArray();
    }
}