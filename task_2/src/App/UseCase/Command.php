<?php

declare(strict_types=1);


namespace App\UseCase;


use App\BlockchainComClient;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interface Command
 *
 * @package App\UseCase
 */
abstract class Command
{
    /**
     * @var \App\BlockchainComClient
     */
    private $client;

    /**
     * @var array
     */
    private $config;

    /**
     * MakeConvertCommand constructor.
     *
     * @param \App\BlockchainComClient $client
     * @param array                    $config
     */
    public function __construct(BlockchainComClient $client, array $config)
    {
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return mixed
     */
    abstract public function execute(Request $request): array;

    /**
     * @return \App\BlockchainComClient
     */
    public function getClient(): BlockchainComClient
    {
        return $this->client;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }
}