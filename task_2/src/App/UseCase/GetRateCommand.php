<?php

declare(strict_types=1);

namespace App\UseCase;

use Symfony\Component\HttpFoundation\Request;

use function array_filter;
use function array_map;
use function explode;
use function in_array;

/**
 * Class GetRateCommand
 *
 * @package App\UseCase
 */
class GetRateCommand extends Command
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     * @throws \HttpResponseException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function execute(Request $request): array
    {
        $data = $this->getClient()->getData();

        if ($request->query->has('currency')) {
            $currency = $request->query->get('currency');
            $currency = explode(',', $currency);

            $data = array_filter(
                $data,
                static function ($item) use ($currency) {
                    return in_array($item, $currency, true);
                },
                ARRAY_FILTER_USE_KEY
            );
        }

        $data = array_map(
            function ($item) {
                return $item['sell'] + $item['sell'] * $this->getConfig()['configuration']['commission'] / 100;
            },
            $data
        );

        return $data;
    }
}
