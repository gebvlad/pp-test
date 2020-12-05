<?php

declare(strict_types=1);


namespace App\Acl;

use App\Config;
use Symfony\Component\HttpFoundation\Request;

use function in_array;
use function str_replace;

/**
 * Class AccessControl
 *
 * @package Acl
 */
class AccessControl
{

    /**
     * @var
     */
    private $tokens;

    /**
     * AccessControl constructor.
     *
     * @param \App\Config $config
     */
    public function __construct(Config $config)
    {
        $this->tokens = $config->getTokens();
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return bool
     */
    public function checkToken(Request $request): bool
    {
        $token = '';

        if ($request->headers->has('Authorization')) {
            $token = $request->headers->get('Authorization');
            $token = str_replace('Bearer ', '', $token);
        }

        return in_array($token, $this->tokens, true);
    }
}
