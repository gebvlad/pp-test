<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Container;
use Siler\Http\Response;
use Siler\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as ResponseHttp;

$request = Request::createFromGlobals();

/** @var \App\Acl\AccessControl $acl */
$acl = Container::get('acl');

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

# Обработка GET запросов
Route\get(
    '/api/v2',
    function () use ($request) {
        $result = [
            'status'  => 'error',
            'code'    => ResponseHttp::HTTP_BAD_REQUEST,
            'message' => "Unknown {$request->getMethod()} method"
        ];

        try {
            switch ($request->query->get('method')) {
                case 'rates':
                    /** @var \App\UseCase\GetRateCommand $command */
                    $command = Container::get('get_rate_command');

                    $result = [
                        'status' => 'success',
                        'code'   => ResponseHttp::HTTP_OK,
                        'data'   => $command->execute($request)
                    ];

                    break;
                // код обработки других методов
            }
        } catch (Exception $e) {
            $result = [
                'status'  => 'error',
                'code'    => ResponseHttp::HTTP_BAD_REQUEST,
                'message' => $e->getMessage()
            ];
        }

        return Response\json($result, $result['code']);
    }
);

# Обработка POST запросов
Route\post(
    '/api/v2',
    function () use ($request) {
        $result = [
            'status'  => 'error',
            'code'    => ResponseHttp::HTTP_BAD_REQUEST,
            'message' => "Unknown {$request->getMethod()} method"
        ];

        try {
            switch ($request->query->get('method')) {
                case 'convert':
                    /** @var \App\UseCase\MakeConvertCommand $command */
                    $command = Container::get('make_convert_command');

                    $result = [
                        'status' => 'success',
                        'code'   => ResponseHttp::HTTP_OK,
                        'data'   => $command->execute($request)
                    ];

                    break;
                // код обработки других методов
            }
        } catch (Exception $e) {
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
