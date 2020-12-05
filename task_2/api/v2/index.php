<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Acl\AccessControl;
use App\BlockchainComClient;
use App\Config;
use App\UseCase\GetRateCommand;
use App\UseCase\MakeConvertCommand;
use Siler\Http\Response;
use Siler\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as ResponseHttp;

$request = Request::createFromGlobals();
$config = Config::getInstance();
$client = new BlockchainComClient($config);

$acl = new AccessControl($config);

# проверка токена
if (!$acl->checkToken($request)) {
    Route\any(
        '/api/v2',
        function () {
            return Response\json(
                [
                    'status'  => 'error',
                    'code'    => ResponseHttp::HTTP_UNAUTHORIZED,
                    'message' => 'Authorization required'
                ],
                ResponseHttp::HTTP_UNAUTHORIZED
            );
        }
    );
}

$result = Route\get(
    '/api/v2',
    function () use ($request, $client, $config) {
        switch ($request->query->get('method')) {
            case 'rates':
                $command = new GetRateCommand($client, $config);

                $result = [
                    'status' => 'success',
                    'code'   => ResponseHttp::HTTP_OK,
                    'data'   => $command->execute($request)
                ];

                break;

            // код обработки других методов

            default:
                $result = [
                    'status'  => 'error',
                    'code'    => ResponseHttp::HTTP_BAD_REQUEST,
                    'message' => 'Unknown GET method'
                ];
        }

        return Response\json($result, $result['code']);
    }
);

Route\post(
    '/api/v2',
    function () use ($request, $client, $config) {
        $result = [
            'status'  => 'error',
            'code'    => ResponseHttp::HTTP_BAD_REQUEST,
            'message' => 'Unknown POST method'
        ];

        try {
            switch ($request->query->get('method')) {
                case 'convert':
                    $command = new MakeConvertCommand($client, $config);

                    $result = [
                        'status' => 'success',
                        'code'   => ResponseHttp::HTTP_OK,
                        'data'   => $command->execute($request)
                    ];

                    break;
                // код обработки других методов
            }
        } catch (InvalidArgumentException $e) {
            $result = [
                'status'  => 'error',
                'code'    => ResponseHttp::HTTP_BAD_REQUEST,
                'message' => $e->getMessage()
            ];
        }

        return Response\json($result, $result['code']);
    }
);

# обработка прочих HTTP-методов
Route\any(
    '/api/v2',
    function () use ($request) {
        return Response\json(
            [
                'status'  => 'error',
                'code'    => ResponseHttp::HTTP_BAD_REQUEST,
                'message' => "Unknown {$request->getMethod()} method"
            ],
            ResponseHttp::HTTP_BAD_REQUEST
        );
    }
);
