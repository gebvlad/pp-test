<?php

declare(strict_types=1);

namespace App\UseCase;

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
    private const CURRENCY_BTC = 'BTC';

    /**
     *
     */
    private const SELL = 'sell';

    /**
     *
     */
    private const BUY = 'buy';

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
        #region проверка входных параметров
        if (!$request->request->has('currency_from')) {
            throw new InvalidArgumentException('Not found «currency_from» parameter');
        }

        if (!$request->request->has('currency_to')) {
            throw new InvalidArgumentException('Not found «currency_to» parameter');
        }

        if (!$request->request->has('value')) {
            throw new InvalidArgumentException('Not found «value» parameter');
        }

        if ($request->request->get('value') < 0) {
            throw new InvalidArgumentException('Parameter «value» must be greater than zero');
        }

        if ($request->request->get('value') < $this->getConfig()->getMinimalValueFromAnyCurrency()) {
            throw new InvalidArgumentException('Parameter «value» must be greater then or equal to 0.01');
        }

        $currencies = $this->getClient()->getData();

        if (
            !array_key_exists($request->request->get('currency_from'), $currencies)
            && $request->request->get('currency_from') !== self::CURRENCY_BTC
        ) {
            throw new InvalidArgumentException('Unknown currency in «currency_from» parameter');
        }

        if (
            !array_key_exists($request->request->get('currency_to'), $currencies)
            && $request->request->get('currency_to') !== self::CURRENCY_BTC
        ) {
            throw new InvalidArgumentException('Unknown currency in «currency_to» parameter');
        }

        if ($request->request->get('currency_to') === $request->request->get('currency_from')) {
            throw new InvalidArgumentException(
                'Conversion is not available for self currency'
            );
        }

        if (
            $request->request->get('currency_to') !== self::CURRENCY_BTC
            && $request->request->get('currency_from') !== self::CURRENCY_BTC
        ) {
            throw new InvalidArgumentException(
                'Conversion is available for ' . self::CURRENCY_BTC . ' and any other currency'
            );
        }

        #endregion проверка входных параметров

        return $this->convert(
            $request->request->get('currency_from'),
            $request->request->get('currency_to'),
            (float)$request->request->get('value'),
            $currencies
        );
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
            ? self::SELL
            : self::BUY;

        if (self::SELL === $type) {
            $rate = $currency[$type] + $this->calculateComission($currency, $type);
            $convertedValue = $value * $rate;
            $convertedValue = round($convertedValue, 2);
        } else {
            $rate = $currency[$type] - $this->calculateComission($currency, $type);
            $convertedValue = $value / $rate;
            $convertedValue = round($convertedValue, 10);
        }

        return [
            'currency_from'   => $from,
            'currency_to'     => $to,
            'value'           => $value,
            'converted_value' => $convertedValue,
            'rate'            => $rate
        ];
    }

    /**
     * Получение суммы комиссии в зависимости от типа операции (продажа, покупка)
     *
     * @param array  $currency
     * @param string $type
     *
     * @return float|int
     */
    private function calculateComission(array $currency, string $type)
    {
        return $currency[$type] * $this->getConfig()->getCommission() / 100;
    }
}
