<?php

namespace EaglenavigatorSystem\Wopi\Controller\Component;

use Cake\Core\Configure;
use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Http\ServerRequest;
use Cake\Log\LogTrait;
use Cake\ORM\TableRegistry;
use Cake\ORM\Table;
use Cake\Routing\Route\Route;
use Cake\Routing\Router;
use Cake\Routing\RouteBuilder;
use EaglenavigatorSystem\Wopi\mODEL\Entity\WopiFile;
use EaglenavigatorSystem\Wopi\Interfaces\WopiInterface;
use EaglenavigatorSystem\Wopi\Exception\WopiUnsupportedActionException;
use EaglenavigatorSystem\Wopi\Service\WopiDiscoveryService;
use Exception;

/**
 * Wopi component
 */
class WopiComponent extends Component implements WopiInterface
{

    use LogTrait;

    private Table $Locks;

    private Table $UserSessions;

    private Table $WopiFiles;

    ### CORE ###

    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->Locks = TableRegistry::getTableLocator()->get('EaglenavigatorSystem/Wopi.Locks');

        $this->UserSessions = TableRegistry::getTableLocator()->get('UserSessions');

        $this->WopiFiles = TableRegistry::getTableLocator()->get('EaglenavigatorSystem/Wopi.WopiFiles');
    }

    ##1 GET THE FILE

    /**
     * Get the file from database
     *
     * @param  int $fileId
     * @return \EaglenavigatorSystem\Wopi\Model\Entity\WopiFile
     */
    public function getTheFile(int $fileId)
    {

        return $this->WopiFiles->get($fileId);
    }

    /**
     * Get the lock from database
     *
     * @param  int $fileId
     * @return \EaglenavigatorSystem\Wopi\Model\Entity\Lock
     */
    public function getTheLock(int $fileId)
    {

        return $this->Locks->find('all', [
            'conditions' => [
                'file_id' => $fileId,
            ]
        ])->first();
    }


    ## LOCK OPERATION

    public function lock(ServerRequest $request, int $fileId)
    {
        $file = $this->getTheFile($fileId);
        $lock = $this->getTheLock($fileId);
        $tokenValid = $this->checkAccessToken($request);

        if (!$tokenValid) {

            return $this->responseAccessTokenInvalid();
        }
        if (!$file) {

            return $this->response404('File not found');
        }

        $documentVersion = $file->version;
        $lockHeader = $request->getHeader(WopiInterface::HEADER_LOCK);
        $lockHeaderOld = $request->getHeader('X-WOPI-OldLock');

        // If the file is currently locked and the X-WOPI-OldLock value does not
        // not match the lock currently on the file, or if the file is unlocked,
        // the host must return a 409 response include an X-WOPI-Lock response.

        if ($lock->locked && !empty($lockHeaderOld)) {
            $response =  $this->getController()->response->withHeader(WopiInterface::HEADER_LOCK, $lock->lock_id);
            $response =  $response->withHeader('X-WOPI-ItemVersion', $documentVersion);
            $response = $this->responseLockMismatch();

            return $response;
        }

        // If the file is currently locked and the X-WOPI-Lock value matches
        // the lock on the file, a host should treat the request as if it
        // a RefreshLock request. then the host should refresh the lock.

        if ($lock->locked && !empty($lockHeader)) {

            $lockId = $lockHeader[0];

            $userId = $this->getController()->Auth->user('id');

            $refreshLock = $this->Locks->refreshLock($fileId, $userId, $lockId);

            if ($refreshLock) {

                $response = $this->getController()->response->withHeader(WopiInterface::HEADER_LOCK, $lock->lock_id);
                $response = $response->withHeader('X-WOPI-ItemVersion', $documentVersion);
                $response = $response->withStatus(200);

                return $response;
            } else {

                $response = $this->getController()->response->withHeader(WopiInterface::HEADER_LOCK, $lock->lock_id);
                $response = $response->withHeader('X-WOPI-ItemVersion', $documentVersion);
                $response = $response->withHeader('X-WOPI-LockFailureReason', 'Lock mismatch');
                $response = $response->withStatus(409);

                return $response;
            }
        }
    }

    public function unlock(ServerRequest $request, int $fileId)
    {
        // Check if the access token is valid
        if (!$this->checkAccessToken($request)) {
            return $this->responseAccessTokenInvalid();
        }

        $lock = $this->getTheLock($fileId);
        $file = $this->getTheFile($fileId);

        // If no lock is found or if the file is not locked, return an error response
        if (!$lock || !$lock->locked) {
            //set header lock to current lock and add header for file version
            $this->getController()->response = $this->getController()
                ->response->withHeader(WopiInterface::HEADER_LOCK, $lock->lock_id);
            $this->getController()->response = $this->getController()
                ->response->withHeader('X-WOPI-ItemVersion', $file->version);

            $this->getController()->response =  $this->response409('No lock present or file is not locked');

            return $this->getController()->response;
        }

        // Remove the lock from the file
        $unlockSuccess = $this->Locks->removeLock($fileId);

        if ($unlockSuccess) {
            //set header lock to empty string
            $this->getController()->response = $this->getController()->response->withHeader(WopiInterface::HEADER_LOCK, '');
            $this->getController()->response = $this->response200('File unlocked successfully');
            return $this->getController()->response;
        } else {

            $this->getController()->response = $this->getController()->response->withHeader(WopiInterface::HEADER_LOCK, $lock->lock_id);
            $this->getController()->response = $this->getController()->response->withHeader('X-WOPI-ItemVersion', $file->version);
            $this->getController()->response = $this->response500('Error unlocking file');

            return $this->getController()->response;
        }
    }

    public function getLock(ServerRequest $request, int $fileId)
    {
        try {
            if (!$this->checkAccessToken($request)) {
                return $this->responseAccessTokenInvalid();
            }
            //get lock
            $this->Locks = TableRegistry::getTableLocator()
                ->get('EaglenavigatorSystem/Wopi.Locks');

            //retrieve lock
            $lock = $this->getTheLock($fileId);
            $file  = $this->getTheFile($fileId);

            if ($lock) {
                //write to header
                $this->getController()->response = $this->getController()->response->withHeader(WopiInterface::HEADER_LOCK, $lock->lock_id);

                $this->getController()->response = $this->getController()->response->withHeader('X-WOPI-ItemVersion', $file->version);

                $this->getController()->response = $this->response200('Lock retrieved successfully');
            } else {

                $this->getController()->response = $this->getController()->response->withHeader(WopiInterface::HEADER_LOCK, '');

                $this->getController()->response = $this->getController()->response->withHeader('X-WOPI-ItemVersion', $file->version);

                $this->getController()->response = $this->response404('Error ,No lock present');
            }
        } catch (Exception $e) {

            $this->getController()->response = $this->getController()->response->withHeader(WopiInterface::HEADER_LOCK, '');

            $this->getController()->response = $this->getController()->response->withHeader('X-WOPI-ItemVersion', $file->version);

            $this->getController()->response = $this->response500('Error' . $e->getMessage());
        }
    }

    public function refreshLock(ServerRequest $request, int $fileId)
    {
        $this->Locks = TableRegistry::getTableLocator()
            ->get('EaglenavigatorSystem/Wopi.Locks');

        $this->WopiFiles = TableRegistry::getTableLocator()
            ->get('EaglenavigatorSystem/Wopi.WopiFiles');

        if (!$this->checkAccessToken($request)) {

            return $this->responseAccessTokenInvalid();
        }
    }

    public function unlockAndRelock(ServerRequest $request, int $fileId)
    {
        //unlock and relock

        try {

            if (!$this->checkAccessToken($request)) {
                return $this->responseAccessTokenInvalid();
            }
            //rename file
            $lockHeader = $request->getHeader(WopiInterface::HEADER_LOCK);
            if (!$lockHeader) {
                $this->getController()->response =  $this->getController()->response->withHeader('X-WOPI-LockFailureReason', 'X-WOPI-Lock header not provided');

                return  $this->getController()->response->withHeader('Content-Type', 'application/json')
                    ->withStringBody(json_encode([
                        'message' => 'X-WOPI-Lock header not provided'
                    ]));
            }

            $file = $this->getTheFile($fileId);
            $lock = $this->getTheLock($fileId);

            $userId  = $this->getController()->Auth->user('id');

            if (!$file) {

                $this->getController()->response = $this->getController()->response
                    ->withHeader(WopiInterface::HEADER_LOCK, $lock->lock_id);
                $this->getController()->response = $this->response404('File not found');

                return $this->getController()->response;
            }

            if (!$lock) {

                $this->getController()->response = $this->getController()->response
                    ->withHeader(WopiInterface::HEADER_LOCK, $lock->lock_id);

                $this->getController()->response = $this->response404('Lock not found');

                return $this->getController()->response;
            }

            $lockValid = $this->compareLockId($lock->lock_id, $lockHeader[0]);

            if (!$lockValid) {
                $this->getController()->response = $this->getController()->response
                    ->withHeader(WopiInterface::HEADER_LOCK, $lock->lock_id);

                $this->getController()->response = $this->response409('Lock mismatch');

                return $this->getController()->response;
            }

            $this->WopiFiles = TableRegistry::getTableLocator()
                ->get('EaglenavigatorSystem/Wopi.WopiFiles');

            //unlock
            $unlockSuccess = $this->Locks->unlockFile($fileId, $userId, $lock->lock_id);

            if (!$unlockSuccess) {

                $this->getController()->response = $this->getController()->response
                    ->withHeader(WopiInterface::HEADER_LOCK, $lock->lock_id);

                $this->getController()->response = $this->response500('Error unlocking file');

                return $this->getController()->response;
            }

            //relock
            $lock = $this->Locks->lockFile($fileId, $userId);

            if (!$lock) {

                $this->getController()->response = $this->getController()->response
                    ->withHeader(WopiInterface::HEADER_LOCK, $lock->lock_id);

                $this->getController()->response = $this->response500('Error relocking file');

                return $this->getController()->response;
            }

            $this->getController()->response = $this->getController()->response->withHeader(WopiInterface::HEADER_LOCK, $lock->lock_id);

            $this->getController()->response = $this->getController()->response->withHeader('X-WOPI-ItemVersion', $file->version);

            $this->getController()->response = $this->response200('File unlocked and relocked successfully');
        } catch (Exception $e) {

            $this->getController()->response = $this->getController()->response
                ->withHeader(WopiInterface::HEADER_LOCK, $lock->lock_id);

            $this->getController()->response = $this->response500('Error unlock and relock file');

            return $this->getController()->response;
        }
    }

    public function deleteFile(ServerRequest $request, int $fileId)
    {
        //delete file
    }

    public function renameFile(ServerRequest $request, int $fileId)
    {

        try {

            if (!$this->checkAccessToken($request)) {
                return $this->responseAccessTokenInvalid();
            }
            //rename file
            $lockHeader = $request->getHeader(WopiInterface::HEADER_LOCK);

            $requestedName = $request->getHeader('X-WOPI-RequestedName')[0] ?? null;

            $file = $this->getTheFile($fileId);
            $lock = $this->getTheLock($fileId);

            if (!$file) {

                $this->getController()->response = $this->getController()->response
                    ->withHeader(WopiInterface::HEADER_LOCK, $lock->lock_id);
                $this->getController()->response = $this->response404('File not found');

                return $this->getController()->response;
            }

            if (!$lock) {

                $this->getController()->response = $this->getController()->response
                    ->withHeader(WopiInterface::HEADER_LOCK, $lock->lock_id);

                $this->getController()->response = $this->response404('Lock not found');

                return $this->getController()->response;
            }

            $lockValid = $this->compareLockId($lock->lock_id, $lockHeader[0]);

            if (!$lockValid) {
                $this->getController()->response = $this->getController()->response
                    ->withHeader(WopiInterface::HEADER_LOCK, $lock->lock_id);

                $this->getController()->response = $this->response409('Lock mismatch');

                return $this->getController()->response;
            }

            $this->WopiFiles = TableRegistry::getTableLocator()
                ->get('EaglenavigatorSystem/Wopi.WopiFiles');

            $validName = $this->WopiFiles->validateName($requestedName);

            if (!$validName) {

                $this->getController()->response = $this->getController()->response
                    ->withHeader(WopiInterface::HEADER_LOCK, $lock->lock_id);

                $this->getController()->response = $this->response400('Invalid file name');

                return $this->getController()->response;
            }

            $file = $this->WopiFiles->renameFile($file->id, $requestedName);

            if ($file instanceof \EaglenavigatorSystem\Wopi\Model\Entity\WopiFile) {

                $this->getController()->response = $this->getController()->response
                    ->withHeader(WopiInterface::HEADER_LOCK, $lock->lock_id);

                $this->getController()->response = $this->response200('File renamed successfully');

                return $this->getController()->response;
            } else {

                $this->getController()->response = $this->getController()->response
                    ->withHeader(WopiInterface::HEADER_LOCK, $lock->lock_id);

                $this->getController()->response = $this->response500('Error renaming file');

                return $this->getController()->response;
            }
        } catch (Exception $e) {

            $this->getController()->response = $this->getController()->response
                ->withHeader(WopiInterface::HEADER_LOCK, $lock->lock_id);

            $this->getController()->response = $this->response500('Error renaming file');

            return $this->getController()->response;
        }
    }

    ## GET FILE OPERATION
    public function getFile(ServerRequest $request, int $fileId)
    {
    }

    public function checkFileInfo(ServerRequest $request, int $fileId)
    {
        if (!$this->checkAccessToken($request)) {
            return $this->responseAccessTokenInvalid();
        }

        $userId = $this->getController()->Auth->user('id');
        $file = $this->WopiFiles->find('all', [
            'conditions' => [
                'id' => $fileId,
                'user_id' => $userId
            ]
        ])->first();

        if (!$file) {
            return $this->response404('File not found');
        }

        // Load the Session component with necessary data
        $this->getController()->loadComponent('EaglenavigatorSystem/Wopi.Session', [
            'file' => $file,
            'user' => $this->getController()->Auth->user(),
            'session' => $request->getSession()
        ]);

        // Fetch file information using the Session component
        $fileInfo = $this->getController()->Session->getAttributes();

        return $this->response200('', $fileInfo);
    }

    public function putRelativeFile(ServerRequest $request, int $fileId)
    {
        // Retrieve necessary headers
        $suggested = $request->getHeader('X-WOPI-SuggestedTarget')[0] ?? null;
        $relative = $request->getHeader('X-WOPI-RelativeTarget')[0] ?? null;
        $overwrite = $request->getHeader('X-WOPI-OverwriteRelativeTarget')[0] ?? false;
        $overwrite = ($overwrite === 'False' || $overwrite === false) ? false : true;

        // Fetch the current file
        $currentFile = $this->getTheFile($fileId);
        if (!$currentFile) {
            return $this->response404('File not found');
        }

        $parentFolder = $currentFile->getParent(); // You still need to implement this
        $content = file_get_contents('php://input');

        // Placeholder for new file entity
        $newFileName = null;

        // Assume generateUrl() method is implemented
        $url = $this->generateUrl();

        try {
            if ($suggested !== null) {
                $newFileName = $this->processSuggestedTarget($suggested, $currentFile); // Still need to implement this
                $newFilePath = $parentFolder . DIRECTORY_SEPARATOR . $newFileName;

                if (!$overwrite && $this->FileManagement->createFile($parentFolder, $newFileName)) {
                    $this->FileManagement->writeFile($newFilePath, $content);
                } elseif ($overwrite) {
                    $this->FileManagement->writeFile($newFilePath, $content);
                } else {
                    return $this->response409('File already exists and overwrite is false');
                }
            } elseif ($relative !== null) {
                $newFileName = mb_convert_encoding($relative, 'UTF-8', 'UTF-7');
                $newFilePath = $parentFolder . DIRECTORY_SEPARATOR . $newFileName;

                if ($this->FileManagement->createFile($parentFolder, $newFileName) || $overwrite) {
                    $this->FileManagement->writeFile($newFilePath, $content);
                } else {
                    return $this->response409('File already exists and overwrite is false');
                }
            } else {
                return $this->response400('Invalid request parameters');
            }
        } catch (Exception $e) {
            // Handle exceptions
            return $this->handlePutRelativeExceptions($e, $newFileName, $url); // Implement this
        }

        // Return successful response with new file info
        return $this->response200(
            'File processed succesfully',
            [
                'Name' => $newFileName,
                'Url' => $this->generateUrlForFile((string) $newFilePath),
            ]
        );
    }



    public function enumerateAncestors(ServerRequest $request, int $fileId)
    {
    }
    public function putUserInfo(ServerRequest $request, int $fileId)
    {

        try {
            $this->UserSessions = TableRegistry::getTableLocator()
                ->get('UserSessions');

            $this->WopiFiles = TableRegistry::getTableLocator()
                ->get('EaglenavigatorSystem/Wopi.WopiFiles');


            $this->UserSessions->updateAll([
                'last_activity' => time(),
            ], [
                'session_id' => $request->getSession()->id(),
                'user_id' => $this->getController()->Auth->user('id'),
            ]);

            if (!$this->checkAccessToken($request)) {

                return $this->responseAccessTokenInvalid();
            }


            //user info is in string body
            $userInfo = $request->getBody()->getContents();

            $data['user_info'] = $userInfo;

            $wopiFile = $this->WopiFiles->putUserInfo($fileId, $data);

            if ($wopiFile instanceof \EaglenavigatorSystem\Wopi\Model\Entity\WopiFile) {
                $this->getController()->response = $this->getController()
                    ->response->withStatus(200);
                $this->getController()->response = $this->getController()
                    ->response->withType('application/json');
                $this->getController()->response = $this->getController()
                    ->response->withStringBody(
                        json_encode(['status' => 'success'])
                    );
            } else {
                $this->getController()->response = $this->getController()
                    ->response->withStatus(500);
                $this->getController()->response = $this->getController()
                    ->response->withType('application/json');
                $this->getController()->response = $this->getController()
                    ->response->withStringBody(
                        json_encode(['status' => 'error'])
                    );
            }
            return $this->getController()->response;
        } catch (Exception $e) {

            $this->getController()->response = $this->getController()
                ->response->withStatus(500);
            $this->getController()->response = $this->getController()
                ->response->withType('application/json');
            $this->getController()->response = $this->getController()
                ->response->withStringBody(
                    json_encode(['status' => 'error'])
                );
            return $this->getController()->response;
        }
    }
    ## PUT FILE OPERATION

    public function putFile(ServerRequest $request, int $fileId)
    {
        try {

            $this->UserSessions = TableRegistry::getTableLocator()
                ->get('UserSessions');

            $this->WopiFiles = TableRegistry::getTableLocator()
                ->get('EaglenavigatorSystem/Wopi.WopiFiles');

            $this->Locks = TableRegistry::getTableLocator()
                ->get('EaglenavigatorSystem/Wopi.Locks');

            $this->UserSessions->updateAll([
                'last_activity' => time(),
            ], [
                'session_id' => $request->getSession()->id(),
                'user_id' => $this->getController()->Auth->user('id'),
            ]);

            if (!$this->checkAccessToken($request)) {

                return $this->responseAccessTokenInvalid();
            }

            $file = $this->WopiFiles->get($fileId);

            $locks = $this->Locks->find('all', [
                'conditions' => [
                    'file_id' => $fileId,
                ]
            ])->first();

            $lockHeader = $request->getHeader(WopiInterface::HEADER_LOCK);

            $userId = $this->getController()->Auth->user('id');

            $token = $request->getQuery('access_token');

            $userSession = $this->UserSessions->find('all', [
                'conditions' => [
                    'user_id' => $userId,
                    'token' => $token
                ]
            ])->first();

            if (!$userSession) {

                $response = $this->response401('Invalid access token');

                return $response;
            }

            if (!$file) {
                //return 404 json response
                // Return a JSON response with a 404 status code
                $response = $this->response404('File not found');
                return $response;
            }

            $validLock = $this->compareLockId($locks->lock_id, $lockHeader[0]);

            $checkForPutOperation = $this->checkIfPutOperationCanProceed($file->id, $request->getBody()->getSize());

            if (!$validLock) {
                //return 409 json response
                // Return a JSON response with a 409 status code
                $response = $this->response409('Lock mismatch');
                return $response;
            }

            if (!$checkForPutOperation) {
                //return 409 json response
                // Return a JSON response with a 409 status code
                $response = $this->response409('Lock mismatch');
                return $response;
            }

            $data = [
                'file_data' => $request->getController()->getBody()->getContents(),
                'size' => $request->getBody()->getSize(),
            ];
            $result = $this->WopiFiles->updateFile($file, $data);

            if ($result) {
                //return 200 json response
                // Return a JSON response with a 200 status code

                //set header
                $this->getController()->response =  $this->getController()
                    ->response->withHeader('X-WOPI-Lock', $locks->lock_id);
                $this->getController()->response =  $this->getController()
                    ->response->withHeader('X-WOPI-ItemVersion', $file->version);
                $this->getController()->response =  $this->getController()
                    ->response->withHeader('X-WOPI-Editors', $this->Auth->user('id'));

                $response =  $this->getController()->response->withStatus(200);
                $response = $response->withType('application/json');
                $response = $response->withStringBody(json_encode(['success' => 'File updated successfully']));
                return $response;
            }
        } catch (Exception $e) {

            $response = $this->response500('Internal server error');
            return $response;
        }
    }




    ##RESPONSES

    /**
     * Return a JSON response with a 404 status code
     *
     * @param  string $message
     * @return \Cake\Http\Response
     */
    public function response404(string $message)
    {

        $this->getController()->response = $this->getController()
            ->response->withStatus(404);
        $this->getController()->response = $this->getController()
            ->response->withType('application/json');
        $this->getController()->response = $this->getController()
            ->response->withStringBody(json_encode(['message' => $message]));

        return $this->getController()->response;
    }

    /**
     * Return a JSON response with a 409 status code
     *
     * @param  string $message
     * @return \Cake\Http\Response
     */
    public function response409(string $message)
    {

        $this->getController()->response = $this->getController()
            ->response->withStatus(409);
        $this->getController()->response = $this->getController()
            ->response->withType('application/json');
        $this->getController()->response = $this->getController()
            ->response->withStringBody(json_encode(['message' => $message]));

        return $this->getController()->response;
    }

    /**
     * Return a JSON response with a 401 status code
     *
     * @param  string $message
     * @return \Cake\Http\Response
     */
    public function response401(string $message)
    {

        $this->getController()->response = $this->getController()
            ->response->withStatus(401);
        $this->getController()->response = $this->getController()
            ->response->withType('application/json');
        $this->getController()->response = $this->getController()
            ->response->withStringBody(json_encode(['message' => $message]));

        return $this->getController()->response;
    }

    /**
     * Return a JSON response with a 200 status code
     *
     * @param  string $message
     * @return \Cake\Http\Response
     */
    public function response200(string $message, array $data = [])
    {

        if (empty($message)) {

            $this->getController()->response = $this->getController()
                ->response->withStatus(200);
            $this->getController()->response = $this->getController()
                ->response->withType('application/json');
        } else {

            $this->getController()->response = $this->getController()
                ->response->withStatus(200);
            $this->getController()->response = $this->getController()
                ->response->withType('application/json');
            $this->getController()->response = $this->getController()
                ->response->withStringBody(json_encode(['message' => $message]));
        }

        if (!empty($data)) {

            $this->getController()->response = $this->getController()
                ->response->withStringBody(json_encode($data));
        }

        return $this->getController()->response;
    }

    /**
     * Return a JSON response with a 500 status code
     *
     * @param  string $message
     * @return \Cake\Http\Response
     */
    public function response500(string $message)
    {

        $this->getController()->response = $this->getController()
            ->response->withStatus(500);
        $this->getController()->response = $this->getController()
            ->response->withType('application/json');
        $this->getController()->response = $this->getController()
            ->response->withStringBody(json_encode(['message' => $message]));

        return $this->getController()->response;
    }

    /**
     * Return a JSON response with a 400 status code
     *
     * @param  string $message
     * @return \Cake\Http\Response
     */
    public function response400(string $message)
    {

        $this->getController()->response = $this->getController()
            ->response->withStatus(400);
        $this->getController()->response = $this->getController()
            ->response->withType('application/json');
        $this->getController()->response = $this->getController()
            ->response->withStringBody(json_encode(['message' => $message]));

        return $this->getController()->response;
    }


    public function response501(string $message)
    {

        $this->getController()->response = $this->getController()
            ->response->withStatus(501);
        $this->getController()->response = $this->getController()
            ->response->withType('application/json');
        $this->getController()->response = $this->getController()
            ->response->withStringBody(json_encode(['message' => $message]));

        return $this->getController()->response;
    }




    public function compareLockId(string $lockId, string $lockIdFromHeader)
    {
        return $lockId === $lockIdFromHeader;
    }

    public function checkIfPutOperationCanProceed(string $fileId, int $size)
    {

        $this->Locks = TableRegistry::getTableLocator()->get('EaglenavigatorSystem/Wopi.Locks');

        return $this->Locks->checkIfPutFileOperationCanContinue($fileId, $size);
    }

    public function logPost(ServerRequest $request)
    {

        if ($request->is('post')) {

            $op = $request->getHeader('X-WOPI-OVERRIDE');
            $identifier = $request->getHeader(WopiInterface::HEADER_LOCK);
            $previous = $request->getHeader('X-WOPI-OLDLOCK');
            $this->logger->info('incoming POST wopi operation [{operation}] with id [{identifier}]', [
                'category' => static::class,
                'operation' => $op,
                'identifier' => $identifier,
                'previous' => $previous,
            ]);
        }
    }

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

        if ($loggedIn) {
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

    public function responseAccessTokenInvalid()
    {
        //return 401 json response
        $response = $this->response401('Invalid access token');

        return $response;
    }

    public function responseInvalidAccess()
    {
        //return 401 json response
        // Return a JSON response with a 401 status code
        $response = $this->getController()->response->withStatus(401);
        $response = $response->withType('application/json');
        $response = $response->withStringBody(json_encode(['error' => 'Invalid access']));
        return $response;
    }

    //response for lock mismatch
    public function responseLockMismatch()
    {

        $response = $this->response409('Lock mismatch');

        return $response;
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

        $this->getController()->response = $this->getController()->response->withHeader(WopiInterface::HEADER_LOCK, $lock);

        return $this->getController()->response;
    }

    public function checkFileLock(ServerRequest $request)
    {

        //this function check file lock
        $lockId = $request->getHeader(WopiInterface::HEADER_LOCK);

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

    /**
     * Convenient method for getUrlForAction.
     */
    public function generateUrl(string $lang = 'en-US'): string
    {
        return $this->getUrlForAction('edit', $lang);
    }

    public function getUrlForAction(string $action, string $lang = 'en-US'): string
    {
        // Accessing configuration in CakePHP
        $config = Configure::read('Wopi');

        $lang = empty($lang) ? $config['defaultUiLang'] : $lang;

        // Handling extension
        $extension = method_exists($this, 'extension')
            ? substr($this->extension(), 1)
            : pathinfo($this->getController()->getRequest()->getParam('basename'), PATHINFO_EXTENSION);

        // Generating URL
        // You need to define a route named 'wopi.checkFileInfo' in your routes.php
        $url = Router::url([
            '_name' => 'wopi.checkFileInfo',
            'file_id' => $this->getController()->getRequest()->getParam('id')
        ], true);

        // Handling discovery and action URL
        // Assuming Discovery is a utility class you have
        $actionUrl = WopiDiscoveryService::discoverAction($extension, $action);

        if (empty($actionUrl['urlsrc'])) {
            throw new WopiUnsupportedActionException("Unsupported action \"{$action}\" for \"{$extension}\" extension.");
        }

        return "{$actionUrl['urlsrc']}lang={$lang}&WOPISrc={$url}";
    }

    public function extension()
    {
        $extension = pathinfo($this->getController()->getRequest()->getParam('basename'), PATHINFO_EXTENSION);

        return $extension;
    }

    private function getParent(File $currentFile)
    {
        // Implementation depends on how you're storing or handling files
        // Here's a simple example using PHP's built-in functions

        // Get the parent directory of the current file
        $parent = dirname($currentFile->path);

        return $parent;
    }

    /**
     * This method is expected to process and return a valid file name based on a suggested target. It might involve sanitizing the input, ensuring the filename is unique, or appending/prepending additional text based on certain logic.
     *
     * @param  string $suggested
     * @param  File   $currentFile
     * @return void
     */
    private function processSuggestedTarget($suggested, WopiFile $currentFile)
    {
        // Implementation depends on your specific logic for handling file names
        // Here's a simple example that appends a timestamp to the suggested name to make it unique

        $path = $currentFile->file_path;

        $fileName = $currentFile->file_name;

        //check if file exists in database
        $fileExists = $this->WopiFiles->find('all', [
            'conditions' => [
                'file_path' => $path,
                'file_name' => $fileName,
            ]
        ])->first();

        //check if file exists in the file path
        $fileExistsInPath = file_exists($path);





        $extension = pathinfo($suggested, PATHINFO_EXTENSION);
        $filename = pathinfo($suggested, PATHINFO_FILENAME);
        return $filename . '_' . time() . '.' . $extension;
    }

    private function handlePutRelativeExceptions(Exception $e, $newFileName, $url)
    {
        // Implementation depends on how you want to handle different types of exceptions
        // Here's a simple example that returns an error message with the exception message
        return $this->response500('An error occurred while processing the file ' . $newFileName . ' at ' . $url . ': ' . $e->getMessage());
    }

    private function generateUrlForFile($newFilePath)
    {
        // Implementation depends on your URL structure and how files are accessed
        // Here's a simple example that assumes files are served from a public directory
        $publicPath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $newFilePath);
        return 'http://' . $_SERVER['HTTP_HOST'] . $publicPath;
    }
}
