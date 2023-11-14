<?php
namespace EaglenavigatorSystem\Wopi\Middleware;

use Cake\Http\ServerRequest;
use Cake\Http\Response;

/**
 * ValidateProof middleware
 */
class ValidateProofMiddleware
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
        return $next($request, $response);
    }
}
