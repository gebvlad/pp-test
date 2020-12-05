<?php

declare(strict_types=1);

namespace App\Client;

use App\Config;
use GuzzleHttp\Client;
use GuzzleHttp\Utils;
use HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BlockchainComClient
 *
 * @package App\Client
 */
class BlockchainComClient
{
    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * @var string
     */
    private $tickerMethod;

    /**
     * Client constructor.
     *
     * @param \App\Config $config
     */
    public function __construct(Config $config)
    {
        $this->client = new Client(
            [
                'base_uri' => $config->getSourceApi(),
                'timeout'  => 2.0,
            ]
        );

        $this->tickerMethod = $config->getTickerMethod();
    }

    /**
     * Получить информацию о валютах
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \HttpResponseException
     */
    public function getData(): array
    {
        $response = $this->client->get($this->tickerMethod);

        if ($response->getStatusCode() !== Response::HTTP_OK) {
            throw new HttpResponseException('Incorrect responce code', $response->getStatusCode());
        }

        $content = $response->getBody()->getContents();
        $content = Utils::jsonDecode($content, true);

        return $content;
    }
}
