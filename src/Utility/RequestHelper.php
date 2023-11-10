<?php

declare(strict_types=1);

namespace EaglenavigatorSystem\Wopi\Utility;

use Cake\Http\ServerRequest;

class RequestHelper
{
    /**
     * Extract full raw URL without any normalization.
     * This is needed to get the access_token from the URL.
     * \Cake\Http\ServerRequest::here() returns normalized URL
     */
    public static function parseUrl(ServerRequest $request): string
    {
        // CakePHP can access query string directly from the server params
        $rawQueryString = $request->getEnv('QUERY_STRING');


        return "{$request->getUri()->getPath()}?{$rawQueryString}";
    }

    /**
     * Alias for getAccessTokenFromUrl.
     */
    public static function parseAccessToken(ServerRequest $request): ?string
    {
        $url = static::parseUrl($request);

        return static::getAccessTokenFromUrl($url);
    }

    /**
     * Extract only access_token from URL.
     */
    public static function getAccessTokenFromUrl(string $url): ?string
    {
        preg_match("/\?access_token=\K[^&]+/", $url, $matches);

        return $matches[0] ?? null; // Use null coalescing operator for optional value
    }
}
