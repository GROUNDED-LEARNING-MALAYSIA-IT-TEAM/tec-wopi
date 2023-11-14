<?php

namespace EaglenavigatorSystem\Wopi\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cake\Cache\Cache;
use Cake\Http\Client;
use Cake\Log\Log;


/**
 * WopiDiscovery middleware
 */
class WopiDiscoveryMiddleware
{
    /**
     * Invoke method.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The request.
     * @param \Psr\Http\Message\ResponseInterface $response The response.
     * @param callable $next Callback to invoke the next middleware.
     * @return \Psr\Http\Message\ResponseInterface A response
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        // Check if discovery data is cached
        //if request not for wopi pathj. in url got '/wopi; ,then return next if no /wopi
        if (strpos($request->getUri()->getPath(), '/wopi/files') === false) {
            return $next($request, $response);
        }
        $discoveryData = Cache::read('wopiDiscoveryData', 'long_term');
        if (!$discoveryData) {
            // Fetch and parse discovery data
            $discoveryData = $this->fetchDiscoveryData();
            if ($discoveryData) {
                Cache::write('wopiDiscoveryData', $discoveryData, 'long_term');
            } else {
                // Handle the case where discovery data could not be fetched
                // This could involve logging the error and/or setting an error response
                Log::error(__FUNCTION__ . " : Unable to fetch discovery data");
            }
        }

        // Add discovery data to request attribute (optional)
        // Add discovery data to request attribute (optional)
        if ($discoveryData) {
            $request = $request->withAttribute('wopiDiscoveryData', $discoveryData);
        }

        return $next($request, $response);
    }

    private function fetchDiscoveryData()
    {
        // Implement the logic to fetch and parse the WOPI discovery XML
        // This might involve sending a GET request to the WOPI discovery URL
        // and then parsing the XML to extract the required information.

        $msDiscoveryEp = "https://onenote.officeapps.live.com/hosting/discovery";
        $httpClient = new Client();

        try {
            $response = $httpClient->get($msDiscoveryEp);

            if ($response->getStatusCode() == 200) {
                return $response->getStringBody();
            } else {
                Log::error(__FUNCTION__ . " : $msDiscoveryEp returned code " . $response->getStatusCode());
            }
        } catch (\Exception $e) {
            Log::error(__FUNCTION__ . " : " . $e->getMessage());
        }

        return null;
    }
}
