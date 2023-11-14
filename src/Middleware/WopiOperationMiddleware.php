<?php
namespace EaglenavigatorSystem\Wopi\Middleware;

use Cake\Http\ServerRequest;
use Cake\Http\Response;

/**
 * WopiOperation middleware
 */
class WopiOperationMiddleware
{
    /**
     * Invoke method.
     *
     * @param \Cake\Http\Request $request The request.
     * @param \Cake\Http\Response $response The response.
     * @param callable $next Callback to invoke the next middleware.
     * @return \Cake\Http\Response A response
     */
    public function __invoke(ServerRequest $request, Response $response, $next)
    {
        if (strpos($request->getUri()->getPath(), '/wopi/files') === false) {
            return $next($request, $response);
        }
        //check if controller request is for wopi operation
        $plugin = $request->getParam('plugin');

        if ($plugin === 'EaglenavigatorSystem/Wopi'){

            //load wopi component
            $request->loadComponent('EaglenavigatorSystem/Wopi.Wopi');
        }

        return $next($request, $response);
    }
}
