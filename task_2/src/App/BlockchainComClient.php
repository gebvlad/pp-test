<?php

namespace App;

use GuzzleHttp\Utils;
use Symfony\Component\HttpFoundation\Response;

class BlockchainComClient
{
    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    private $tickerMethod;

    /**
     * Client constructor.
     *
     * @param \GuzzleHttp\Client $client
     */
    public function __construct(array $config)
    {
        $this->client = new \GuzzleHttp\Client(
            [
                'base_uri' => $config['configuration']['source_api'],
                'timeout'  => 2.0,
            ]
        );

        $this->tickerMethod = $config['configuration']['ticker_method'];
    }

    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \HttpResponseException
     */
    public function getData(): array
    {
        $response = $this->client->get($this->tickerMethod);

        if ($response->getStatusCode() !== Response::HTTP_OK) {
            throw new \HttpResponseException('Incorrect responce code', $response->getStatusCode());
        }

        $content = $response->getBody()->getContents();
        $content = Utils::jsonDecode($content, true);

        return $content;
    }
}