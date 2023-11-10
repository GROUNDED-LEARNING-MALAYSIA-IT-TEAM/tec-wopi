<?php

namespace EaglenavigatorSystem\Wopi\Interfaces;

interface ConfigRepositoryInterface
{
    /**
     * Get the WOPI discovery XML URL.
     *
     * @return string
     */
    public function getDiscoveryXmlUrl(): string;

    /**
     * Get the WOPI app endpoint URL.
     *
     * @return string
     */
    public function getAppEndpointUrl(): string;

    /**
     * Get the WOPI client ID.
     *
     * @return string
     */
    public function getClientId(): string;

    /**
     * Get the WOPI client secret.
     *
     * @return string
     */
    public function getClientSecret(): string;

    /**
     * Get the WOPI access token.
     *
     * @return string
     */
    public function getAccessToken(): string;

    /**
     * Set the WOPI access token.
     *
     * @param string $accessToken
     * @return void
     */
    public function setAccessToken(string $accessToken): void;
}
