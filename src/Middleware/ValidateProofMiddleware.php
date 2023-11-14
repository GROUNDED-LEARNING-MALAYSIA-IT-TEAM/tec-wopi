<?php
namespace EaglenavigatorSystem\Wopi\Middleware;

use Cake\Core\Configure;
use Cake\Http\ServerRequest;
use Cake\Http\Response;
use EaglenavigatorSystem\Wopi\Service\ProofValidationService;
use EaglenavigatorSystem\Wopi\Service\ProofValidator;

/**
 * ValidateProof middleware
 */
class ValidateProofMiddleware
{
    private $proofValidator;
    /**
     * Invoke method.
     *
     * @param \Cake\Http\ServerRequest $request The request.
     * @param \Cake\Http\Response $response The response.
     * @param callable $next Callback to invoke the next middleware.
     * @return \Cake\Http\Response A response
     */
    public function __invoke(ServerRequest $request, Response $response, $next)
    {
        if (strpos($request->getUri()->getPath(), '/wopi/files') === false) {
            return $next($request, $response);
        }

        $proofValidationEnabled = Configure::read('Wopi.proof_validation_enabled');
        $this->proofValidator = new ProofValidator();
        if (!$proofValidationEnabled) {
            return $next($request);
        }

        if ($this->proofValidator->isValid(ProofValidationService::fromRequest($request))) {
            return $next($request);
        }

        //default throw 500
        return $response->withStatus(500);
    }
}
