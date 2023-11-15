<?php

namespace EaglenavigatorSystem\Wopi\Controller\Component;

use App\Model\Entity\UserManagement;
use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Http\Session;
use Cake\Routing\Route\Route;
use Cake\Routing\Router;
use EaglenavigatorSystem\Wopi\Model\Entity\WopiFile;
use EaglenavigatorSystem\Wopi\Interfaces\SessionInterface;

/**
 * Session component
 */
class SessionComponent extends Component implements SessionInterface
{
    //session
    protected $session;

    //file entity
    protected $file;

    //user entity
    protected UserManagement $user;

    /**
     * Magic method constructor
     *
     * @param WopiFile $file
     * @param UserManagement $user
     * @param Session $session
     * @return void
     */
    public function __construct(WopiFile $file, UserManagement $user, Session $session)
    {
        $this->file = $file;
        $this->user = $user;
        $this->session = $session;
    }

    /**
     * Get user
     *
     * @return UserManagement
     */
    public function getUser(): UserManagement
    {
        return $this->user;
    }

    /**
     * G
     * @return WopiFile
     */
    public function getFile(): WopiFile
    {
        return $this->file;
    }

    public function getAccessToken(): string
    {
        return $this->session->read('Wopi.AccessToken');
    }

    public function getAccessTokenTTl(): int
    {
        return $this->session->read('Wopi.AccessTokenTTL');
    }

    public function getUrl(): string
    {
        //get url from url

        $url = Router::url([
            'plugin' => 'EaglenavigatorSystem/Wopi',
            'controller' => 'Files',
            'action' => 'index',
            '?', [
                'access_token' => $this->getAccessToken(),
                'access_token_ttl' => $this->getAccessTokenTTl()
            ],
        ], true);

        return Router::fullBaseUrl($url);
    }

    public function getAttributes(): array
    {
        $attributes = [
            'AllowExternalMarketplace' => false,
            'BaseFileName' => $this->file->getName(),
            'DisablePrint' => false,
            'DisableTranslation' => false,
            'FileVersionPostMessage' => true,
            'PostMessageOrigin' => $this->session['post_message_origin'] ?? 'http://localhost',
            'OwnerId' => (string) $this->file->getOwner(),
            'ReadOnly' => $this->file->isReadonly(),
            'RestrictedWebViewOnly' => false,
            'LastModifiedTime' => $attrs['changed']->toDateTime()->format('c'),
            'Size' => $this->file->getSize(),
            'SupportsCobalt' => false,
            'SupportsFolders' => true,
            'SupportsLocks' => true,
            'SupportsGetLock' => true,
            'SupportsExtendedLockLength' => true,
            'SupportsUserInfo' => false,
            'SupportsDeleteFile' => true,
            'SupportsUpdate' => true,
            'SupportsRename' => true,
            'FileNameMaxLength' => AbstractNode::MAX_NAME_LENGTH,
            'UserCanAttend' => false,
            'UserCanNotWriteRelative' => false,
            'UserCanPresent' => false,
            'UserCanRename' => true,
            'UserCanWrite' => $this->file->mayWrite(),
            'UserFriendlyName' => $this->user->getUsername(),
            'UserId' => (string) $this->user->getId(),
            'Version' => (string) $this->file->getVersion(),
        ];

        return $attributes;

    }
}
