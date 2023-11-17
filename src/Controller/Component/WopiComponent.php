<?php

namespace EaglenavigatorSystem\Wopi\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;
use Cake\ORM\Table;
use Cake\Routing\Route\Route;
use Cake\Routing\Router;
use Cake\Routing\RouteBuilder;

/**
 * Wopi component
 */
class WopiComponent extends Component
{
    private Table $UserSessions;
    //this component will have capability to add header to wopi requests
    public function processHeadersFromMicrosoftAndRedirect()
    {
        $header = $this->getController()->request->getHeaders();
        $header = $header['X-WOPI-Override'][0];
        switch ($header) {
            case 'CHECKFILEINFO':
                $this->getController()->request->params['action'] = 'index';
                $this->getController()->request->params['controller'] = 'CheckFileInfo';
                break;
            case 'GETFILE':
                $this->getController()->request->params['action'] = 'index';
                $this->getController()->request->params['controller'] = 'GetFile';
                break;
            case 'PUTFILE':
                $this->getController()->request->params['action'] = 'index';
                $this->getController()->request->params['controller'] = 'PutFile';
                break;
            case 'PUT_RELATIVE':
                $this->getController()->request->params['action'] = 'index';
                $this->getController()->request->params['controller'] = 'PutRelativeFile';
                break;
            case 'LOCK':
                $this->getController()->request->params['action'] = 'index';
                $this->getController()->request->params['controller'] = 'LockFile';
                break;
            case 'UNLOCK':
                $this->getController()->request->params['action'] = 'index';
                $this->getController()->request->params['controller'] = 'UnlockFile';
                break;
            case 'REFRESH_LOCK':
                $this->getController()->request->params['action'] = 'index';
                $this->getController()->request->params['controller'] = 'RefreshLock';
                break;
            case 'GET_LOCK':
                $this->getController()->request->params['action'] = 'index';
                $this->getController()->request->params['controller'] = 'GetFile';
                break;
            case 'DELETE':
                $this->getController()->request->params['action'] = 'index';
                $this->getController()->request->params['controller'] = 'DeleteFile';
                break;
            case 'PUT_USER_INFO':
                $this->getController()->request->params['action'] = 'index';
                $this->getController()->request->params['controller'] = 'PutUserInfo';
                break;
        }
    }

    public function generateAccessToken()
    {
        //this will generate access token for wopi requests and add it to response header to a user
        //this token will be used to authenticate user in wopi requests

        //1 - check if user is logged in
        $loggedIn = $this->getController()->Auth->user();

        //2 - generate token if no token present in request

        if($loggedIn) {
            if (!$this->checkAccessToken($this->getController()->request)) {
                //generate token
                $token = $this->getController()->Auth->user('id') . '-' . time();
                //add token to response header
                $this->addHeader(['access_token' => $token]);
            } else {
                //token already present in request
                //check if token is valid .
                $valid = $this->checkIfTokenIsExpired($this->getController()->request);
                //if token is not valid then generate new token
                if (!$valid) {
                    //generate token
                    $token = bin2hex(random_bytes(16));
                    //token generated will expire in 1 hour
                    $tokenExpire = time() + 3600;
                    $this->UserSessions = TableRegistry::getTableLocator()->get('UserSessions');

                    //add token to response header
                    $this->addHeader(['access_token' => $token]);
                    $this->addHeader(['access_token_ttl' => $tokenExpire]);

                    //update token in database
                    $this->UserSessions = TableRegistry::getTableLocator()->get('UserSessions');
                    $userSession = $this->UserSessions->find('all', [
                        'conditions' => [
                            'session_id' => $this->getController()->request->getSession()->id(),
                            'user_id' => $this->getController()->Auth->user('id'),
                        ],
                    ])->first();

                    $userSession->token = $token;
                    $userSession->token_expires = $tokenExpire;

                    $this->UserSessions->save($userSession);
                }
                //if token is valid then do nothing
            }
        }

        //3 - add token to response header
    }


    public function addHeader(array $data)
    {
        //add header to wopi requests
        // array in key  -> value format
        foreach ($data as $key => $value) {
            $this->getController()->response = $this->getController()->response->withHeader($key, $value);
        }
    }

    public function checkAccessToken(ServerRequest $request): bool
    {

        //read 'access_token'
        $accessToken = $request->getQuery('access_token');

        return !empty($accessToken) && $this->checkIfTokenIsExpired($request);
    }

    public function checkIfTokenIsExpired(ServerRequest $request): bool
    {
        //read 'access_token ttl in header'
        $tokenTime = $request->getHeader('access_token_ttl');

        $currentTime = time();

        return ($currentTime - $tokenTime) > 3600;
    }

    public function writeHeaderLock(string $lock)
    {

        $this->getController()->response = $this->getController()->response->withHeader('X-WOPI-Lock', $lock);
    }

    public function checkFileLock(ServerRequest $request)
    {

        //this function check file lock
        $lockId = $request->getHeader('X-WOPI-Lock');

        $fileId = $request->getPass(0);

        $this->Locks = TableRegistry::getTableLocator()->get('EaglenavigatorSystem/Wopi.Locks');

        return $this->Locks->checkWopiLock($lockId, $fileId);


    }

    #------------------ wopi responses

    public function responseGetFileInfo(ServerRequest $request, bool $operationSuccess = false)
    {

        /*

        */

    }

    public function getHostEditUrl(string $sessionId, string $fileId, int $userId)
    {
        //form edit url

        /**
         *  HostEditUrl A URI to a host page that loads the edit WOPI action.
         */
        $url = Router::url([
            'plugin' => 'EaglenavigatorSystem/Wopi',
            'controller' => 'EditFile',
            'action' => 'index',
            $fileId,
            '?',
            [
                'access_token' => $this->UserSessions->find('all', [
                    'conditions' => [
                        'user_id' => $userId,
                        'session_id' => $sessionId
                    ]
                ])->first()->token,
            ]

        ], true);

        return $url;
    }
}
