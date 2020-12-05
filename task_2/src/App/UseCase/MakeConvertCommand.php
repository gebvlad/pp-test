<?php

declare(strict_types=1);

namespace App\UseCase;

use GuzzleHttp\Utils;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;

use function array_key_exists;
use function round;

/**
 * Class MakeConvertCommand
 *
 * @package App\UseCase
 */
class MakeConvertCommand extends Command
{
    /**
     *
     */
    const CURRENCY_BTC = 'BTC';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \HttpResponseException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function execute(Request $request): array
    {
        /** @var array $params */
        $params = Utils::jsonDecode($request->getContent(), true);

        if ('' === trim((string)$params['currency_from'])) {
            throw new InvalidArgumentException('Not found «currency_from» parameter');
        }

        if ('' === trim((string)$params['currency_to'])) {
            throw new InvalidArgumentException('Not found «currency_to» parameter');
        }

        if ('' === trim((string)$params['value'])) {
            throw new InvalidArgumentException('Not found «value» parameter');
        }

        if ($params['value'] < 0) {
            throw new InvalidArgumentException('Parameter «value» must be greater than zero');
        }

        $data = $this->getClient()->getData();

        if (!array_key_exists($params['currency_from'], $data)
            && $params['currency_from'] !== self::CURRENCY_BTC
        ) {
            throw new InvalidArgumentException('Unknown currency in «currency_from» parameter');
        }

        if (
            !array_key_exists($params['currency_to'], $data)
            && $params['currency_to'] !== self::CURRENCY_BTC
        ) {
            throw new InvalidArgumentException('Unknown currency in «currency_to» parameter');
        }

        if ($params['currency_to'] === $params['currency_from']) {
            throw new InvalidArgumentException(
                'Conversion is not available for self currency'
            );
        }

        if ($params['currency_to'] !== self::CURRENCY_BTC && $params['currency_from'] !== self::CURRENCY_BTC) {
            throw new InvalidArgumentException(
                'Conversion is available for ' . self::CURRENCY_BTC . ' and any other currency'
            );
        }

        $params['value'] = (float)$params['value'];


        return $this->convert($params['currency_from'], $params['currency_to'], $params['value'], $data);
    }

    /**
     * @param string $from
     * @param string $to
     * @param float  $value
     * @param array  $currencies
     *
     * @return array
     */
    private function convert(string $from, string $to, float $value, array $currencies): array
    {
        $currency = $from !== self::CURRENCY_BTC
            ? $currencies[$from]
            : $currencies[$to];

        $type = $from === self::CURRENCY_BTC
            ? 'sell'
            : 'buy';

        $rate = $currency[$type] - $currency[$type] * $this->getConfig()['configuration']['commission'] / 100;

        $convertedValue = 'sell' === $type ? $value * $rate : $value / $rate;

        return [
            'currency_from'   => $from,
            'currency_to'     => $to,
            'value'           => $value,
            'converted_value' => 'sell' === $type ? round($convertedValue, 10) : round($convertedValue, 2),
            'rate'            => $rate,
            'type'            => $type
        ];
    }
}
