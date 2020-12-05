<?php

declare(strict_types=1);


namespace App\UseCase;


use App\Client\BlockchainComClient;
use App\Config;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interface Command
 *
 * @package App\UseCase
 */
abstract class Command
{
    /**
     * @var \App\Client\BlockchainComClient
     */
    private $client;

    /**
     * @var \App\Config
     */
    private $config;

    /**
     * MakeConvertCommand constructor.
     *
     * @param \App\Client\BlockchainComClient $client
     * @param \App\Config                     $config
     */
    public function __construct(BlockchainComClient $client, Config $config)
    {
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * Выполнить команду
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return mixed
     */
    abstract public function execute(Request $request): array;

    /**
     * @return \App\Client\BlockchainComClient
     */
    public function getClient(): BlockchainComClient
    {
        return $this->client;
    }

    /**
     * @return \App\Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }
}