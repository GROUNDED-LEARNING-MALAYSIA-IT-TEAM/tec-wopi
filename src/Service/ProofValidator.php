<?php

declare(strict_types=1);

namespace EaglenavigatorSystem\Wopi\Service;

use Cake\I18n\FrozenTime;
use EaglenavigatorSystem\Wopi\Utility\DotNetTimeConverter;
use EaglenavigatorSystem\Wopi\Service\ProofValidationService;
use EaglenavigatorSystem\Wopi\Service\WopiDiscoveryService;
use phpseclib\Crypt\RSA;
use phpseclib\Math\BigInteger;

class ProofValidator
{

  private ProofValidationService $proofValidationService;

  public function isValid(ProofValidationService $proofValidationService)
  {
    $this->proofValidationService = $proofValidationService;

      // Check if X-WOPI-PROOF header is present
      if (! $this->proofHeadersArePresent()) {
        return false;
    }

    // Making sure that timestamp header was sent within the last 20 minutes.
    if (! $this->verifyTimestamp()) {
        return false;
    }

    // Constructing the expected proof
    $expected = $this->calculateExpectedKey();

    // load (calculate) keys For hosts that don’t use the .NET framework,
    // Office/Libro for the web provides the RSA modulus and exponent
    // directly. the proof-key element in the WOPI discovery XML.
    $key = $this->getPublicKey();
    $keyOld = $this->getOldPublicKey();

    $wopiSignedProofHeader = $this->proofValidationService->proof;
    $oldWopiSignedProofHeader = $this->proofValidationService->oldProof;

    // Verifying the proof keys, check three combinations of proof
    // key values. If any one of the values is valid the request
    // was signed by Office/WOPI host for the web.
    return
        // The X-WOPI-Proof value using the current public key.
           $this->verify($expected, $wopiSignedProofHeader, $key)
        // The X-WOPI-ProofOld value using the current public key.
        || $this->verify($expected, $oldWopiSignedProofHeader, $key)
        // The X-WOPI-Proof value using the old public key.
        || $this->verify($expected, $wopiSignedProofHeader, $keyOld);
  }

  /**
   * Construct public key.
   */
  private function getPublicKey(): string
  {
    $modulus = WopiDiscoveryService::getProofModulus();
    $exponent = WopiDiscoveryService::getProofExponent();

    return $this->calculateRSAKey($modulus, $exponent);
  }

  /**
   * Construct old public key.
   */
  private function getOldPublicKey(): string
  {
    $modulus = WopiDiscoveryService::getOldProofModulus();
    $exponent = WopiDiscoveryService::getOldProofExponent();

    return $this->calculateRSAKey($modulus, $exponent);
  }

  /**
   * Construct the RSA public key from modulus and exponent.
   */
  private function calculateRSAKey(string $modulus, string $exponent): string
  {
    // Modulus and Exponent keys are in base64 encode
    $rsa = new RSA;

    $rsa->loadKey([
      'e' => new BigInteger(base64_decode($exponent, true), 256),
      'n' => new BigInteger(base64_decode($modulus, true), 256),
    ]);

    return (string) $rsa->__toString();
  }

  /**
   * Construct to be converted SHA256 key that will be compared
   * to X-WOPI-Proof and X-WOPI-ProofOld headers.
   */
  private function calculateExpectedKey(): string
  {
    $url = $this->proofValidationService->url;

    $accessToken = $this->proofValidationService->accessToken;

    // php utf-8 strings are already byte strings
    $accessTokenBytes = utf8_encode($accessToken);

    // url should be in uppercase
    $urlBytes = utf8_encode(strtoupper($url));

    // make sure to treat timestamp as longlong 64-bit big-endian
    $timestampBytes = pack('J', $this->proofValidationService->timestamp);

    return sprintf(
      // Template that will compain all of these bytes together
      // since php does not have proper byte support ie byte[]
      '%s%s%s%s%s%s',

      // 4 bytes that represent the length, in bytes, of the access_token on the request.
      // N in pack() stands for unsigned long (always 32 bit, big endian byte order).
      pack('N', strlen($accessTokenBytes)),

      // access_token bytes
      $accessTokenBytes,

      // 4 bytes that represent the length, in bytes, of the WOPI
      // request, including any query string parameters.
      pack('N', strlen($urlBytes)),

      // full url, to byte arrays. utf8 strings are byte arrays. The WOPI
      // request URL is in all uppercase. All query string parameters
      // on the request URL should be included. Raw is recommeded.
      $urlBytes,

      // 4 bytes that represent the length, in bytes, of the X-WOPI-TimeStamp value.
      pack('N', strlen($timestampBytes)),

      // The X-WOPI-TimeStamp value
      $timestampBytes
    );
  }

  private function proofHeadersArePresent(): bool
  {
    $hasHeaderProof = !empty($this->proofValidationService->proof);
    $hasHeaderProofOld = !empty($this->proofValidationService->oldProof);

    if ($hasHeaderProof && $hasHeaderProofOld) {
      return true;
    }

    return false;
  }

  /**
   * Verify X-WOPI-Timestamp header and make sure that it was sent within the last 20 minutes.
   */
  private function verifyTimestamp(): bool
  {
    $timestamp = $this->proofValidationService->timestamp;

    // Php uses Unix timestamps (time elapsed since 1/1/1970). and measured is seconds.
    // The WOPI protocol timestamp measures the time elapsed 1/1/0001. and measured
    // in the number of 100 nano-second units passed since January 1st 1 AD.
    $date = DotNetTimeConverter::toDatetime($timestamp);

    $timestampDiff = abs((FrozenTime::now()->getTimestamp() - $date->getTimestamp()));

    if ($timestampDiff > 20 * 60) {
      return false;
    }

    return true;
  }

  /**
   * Verifying the proof keys.
   */
  private function verify(string $expected, string $signedProof, string $key): bool
  {
    $rsa = new RSA();

    if (!$rsa->loadKey($key)) {
      return false;
    }

    $rsa->setSignatureMode(RSA::SIGNATURE_PKCS1);
    $rsa->setHash('sha256');

    return $rsa->verify($expected, (string) base64_decode($signedProof, true));
  }
}
