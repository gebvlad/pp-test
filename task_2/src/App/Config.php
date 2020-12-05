<?php

declare(strict_types=1);

namespace App;

use Symfony\Component\Yaml\Yaml;

/**
 * Class Config
 *
 * @package App
 */
class Config
{
    /**
     * @var \App\Config
     */
    private static $config;

    /**
     * @var string
     */
    private $sourceApi;

    /**
     * @var string
     */
    private $tickerMethod;

    /**
     * @var array
     */
    private $tokens;

    /**
     * @var int
     */
    private $commission;

    /**
     * @var float
     */
    private $minimalValueFromAnyCurrency;

    /**
     * Config constructor.
     *
     */
    private function __construct()
    {
    }

    /**
     * @return \App\Config
     */
    public static function getInstance(string $path): Config
    {
        if (null === self::$config) {
            $params = Yaml::parseFile(__DIR__ . $path);

            self::$config = new Config();
            self::$config->setSourceApi($params['source_api'])
                ->setTickerMethod($params['ticker_method'])
                ->setTokens($params['tokens'])
                ->setCommission($params['commission'])
                ->setMinimalValueFromAnyCurrency($params['minimal_value_from_any_currency']);
        }

        return self::$config;
    }

    /**
     * @param float $minimalValueFromAnyCurrency
     *
     * @return Config
     */
    private function setMinimalValueFromAnyCurrency(float $minimalValueFromAnyCurrency): Config
    {
        $this->minimalValueFromAnyCurrency = $minimalValueFromAnyCurrency;

        return $this;
    }

    /**
     * @param mixed $commission
     *
     * @return Config
     */
    private function setCommission(int $commission): Config
    {
        $this->commission = $commission;

        return $this;
    }

    /**
     * @param mixed $tokens
     *
     * @return Config
     */
    private function setTokens(array $tokens): Config
    {
        $this->tokens = $tokens;

        return $this;
    }

    /**
     * @param mixed $tickerMethod
     *
     * @return Config
     */
    private function setTickerMethod(string $tickerMethod): Config
    {
        $this->tickerMethod = $tickerMethod;

        return $this;
    }

    /**
     * @param mixed $sourceApi
     *
     * @return Config
     */
    private function setSourceApi(string $sourceApi): Config
    {
        $this->sourceApi = $sourceApi;

        return $this;
    }

    /**
     * @return string
     */
    public function getSourceApi(): string
    {
        return $this->sourceApi;
    }

    /**
     * @return string
     */
    public function getTickerMethod(): string
    {
        return $this->tickerMethod;
    }

    /**
     * @return array
     */
    public function getTokens(): array
    {
        return $this->tokens;
    }

    /**
     * @return int
     */
    public function getCommission(): int
    {
        return $this->commission;
    }

    /**
     * @return float
     */
    public function getMinimalValueFromAnyCurrency(): float
    {
        return $this->minimalValueFromAnyCurrency;
    }
}
