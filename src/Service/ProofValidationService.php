<?php

declare(strict_types=1);

namespace EaglenavigatorSystem\Wopi\Service;

use Cake\Http\ServerRequest;
use EaglenavigatorSystem\Wopi\Interfaces\WopiInterface;
use EaglenavigatorSystem\Wopi\Utility\RequestHelper;

/**
 * Class ProofValidationService
 * - used in wopi validation process for microsoft 365
 */
class ProofValidationService
{
    public string $accessToken;

    public string $timestamp;

    public string $url;

    public string $proof;

    public string $oldProof;

    public function __construct(
        ?string $accessToken,
        ?string $timestamp,
        ?string $url,
        ?string $proof,
        ?string $oldProof
    ) {
        $this->accessToken = is_null($accessToken) ? RequestHelper::getAccessTokenFromUrl($url) : $accessToken;
        $this->timestamp = $timestamp;
        $this->url = $url;
        $this->proof = $proof;
        $this->oldProof = $oldProof;

        return $this;
    }

    public static function fromRequest(ServerRequest $request)
    {

        $url = RequestHelper::parseUrl($request);
        $accessToken = RequestHelper::parseAccessToken($request);
        $timestamp = $request->getHeaderLine(WopiInterface::HEADER_TIMESTAMP);
        $proofHeader = $request->getHeaderLine(WopiInterface::HEADER_PROOF);
        $oldProofHeader = $request->getHeaderLine(WopiInterface::HEADER_PROOF_OLD);

        return new static($accessToken, $timestamp, $url, $proofHeader, $oldProofHeader);

    }

    public function toArray(): array
    {
        return [
          'access_token' => $this->accessToken,
          'url' => $this->url,
          WopiInterface::HEADER_TIMESTAMP => $this->timestamp,
          WopiInterface::HEADER_PROOF => $this->proof,
          WopiInterface::HEADER_PROOF_OLD => $this->oldProof,
        ];
    }
}
