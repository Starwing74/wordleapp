<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CallApiService
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    private $test = 0;

    public function getWordFromWordle(int $int): array
    {
        $response = $this->client->request(
            'GET',
            'http://alixgoguey.fr/words/wordapi.php?cmd=rand&size=' . $int
        );

        $check = $response->toArray();

        $response2 = $this->client->request(
            'GET',
            'https://api.dictionaryapi.dev/api/v2/entries/en/' . $check["word"]
        );

        echo $check["word"] . " ";

        $checkWord = $response2->getContent(false);

        if(str_contains($checkWord,"No Definitions Found")){
            $this->getWordFromWordle($int);
            $this->test += 1;
        }else{
            return $response->toArray();
        }
        return $this->getWordFromWordle($int);
    }

    public function checkWordExist(string $word): bool
    {

        $response = $this->client->request(
            'GET',
            'https://api.dictionaryapi.dev/api/v2/entries/en/' . $word
        );

        $checkWord = $response->getContent(false);

        if(str_contains($checkWord,"Sorry pal, we couldn't find definitions for the word you were looking for.")){
            $exist = false;
        } else {
            $exist = true;
        }

        return $exist;
    }

}