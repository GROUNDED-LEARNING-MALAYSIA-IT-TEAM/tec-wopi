<?php

declare(strict_types=1);

namespace EaglenavigatorSystem\Wopi\Interfaces;

use App\Model\Entity\UserManagement;
use EaglenavigatorSystem\Wopi\Model\Entity\WopiFile;

interface SessionInterface
{
    /**
     * Get user.
     */
    public function getUser(): UserManagement;

    /**
     * Get File.
     */
    public function getFile(): WopiFile;

    /**
     * Get valid until.
     */
    public function getAccessTokenTTl(): int;

    /**
     * Get access token.
     */
    public function getAccessToken(): string;

    /**
     * Get session attributes.
     */
    public function getAttributes(): array;
}
