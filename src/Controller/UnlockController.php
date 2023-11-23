<?php

namespace EaglenavigatorSystem\Wopi\Controller;

use EaglenavigatorSystem\Wopi\Controller\AppController;
use EaglenavigatorSystem\Wopi\Exception\LockOperationFailedException;
use EaglenavigatorSystem\Wopi\Exception\LockMismatchException;
use Exception;

/**
 * Unlock Controller
 */
class UnlockController extends AppController
{
    /**
     * Index method for unlocking a file in wopi for microsoft 365
     *
     * @return \Cake\Http\Response|null
     */
    public function index(string $fileId)
    {
        try {
            //method only accepts post requests

            //read lock

            $this->request->allowMethod(['post']);
            $this->loadComponent('Wopi');
            $this->autoRender = false;
            $this->Wopi->logPost($this->request);
            $lockFromHeader = $this->request->getHeader('X-WOPI-Lock');
            if (!$lockFromHeader) {

                $this->response = $this->response->withStatus(400);
                $this->response = $this->response->withHeader('X-WOPI-LockFailureReason', 'X-WOPI-Lock header not provided');

                return $this->response->withHeader('Content-Type', 'application/json')
                    ->withStringBody(json_encode([
                        'message' => 'X-WOPI-Lock header not provided'
                    ]));
            }

            $userId = $this->Auth->user('id');
            //check token validity
            $tokenValid = $this->Wopi->checkAccessToken($this->request);

            if (!$tokenValid) {

                $this->response = $this->response->withStatus(401);
                $this->response = $this->response->withHeader('X-WOPI-LockFailureReason', 'Invalid access token');

                return $this->response->withHeader('Content-Type', 'application/json')
                    ->withStringBody(json_encode([
                        'message' => 'Invalid access token'
                    ]));
            }

            //lockId is the lock that was set when the file was locked
            $lockId = $this->request->getHeader('X-WOPI-Lock');

            if (!$fileId) {
                $message = 'File id not provided';
                $this->response = $this->response->withStatus(404);
                $this->response = $this->response->withHeader('X-WOPI-LockFailureReason', 'File not found');

                return $this->response->withHeader('Content-Type', 'application/json')
                    ->withStringBody(json_encode([
                        'message' => $message
                    ]));
            }

            $file = $this->Wopi->getWopiFileById($fileId);

            if (!$file) {
                $message = 'File not found';
                $this->response = $this->response->withStatus(404);
                $this->response = $this->response->withHeader('X-WOPI-LockFailureReason', 'File not found');

                return $this->response->withHeader(
                    'Content-Type',
                    'application/json'
                )
                    ->withStringBody(json_encode([
                        'message' => $message
                    ]));
            }

            $lockData = $this->Locks->getByFileId($fileId);

            if ($lockData->lock_id !== $lockId) {

                $this->response = $this->response->withHeader('X-WOPI-LockFailureReason', 'Lock mismatch');
                $this->response = $this->response->withStatus(409);

                return $this->response->withHeader(
                    'Content-Type',
                    'application/json'
                )
                    ->withStringBody(json_encode([
                        'message' => 'Lock mismatch'
                    ]));
            }

            $lock = $this->Locks->unlockFile($fileId, $userId, $lockId);

            $file = $this->WopiFiles->getById($fileId);

            if ($lock->locked === false) {
                $this->response = $this->response->withHeader('X-WOPI-Lock', '');
                $this->response = $this->response->withHeader('X-WOPI-ItemVersion', $file->version);
                $this->response = $this->response->withStatus(200);
            } else {
                $this->response = $this->response->withHeader('X-WOPI-LockFailureReason', 'File already unlocked');
                $this->response = $this->response->withStatus(409);

                $message = 'File already locked';

                $this->response = $this->response->withStringBody(json_encode([
                    'message' => $message
                ]));
            }
        } catch (LockMismatchException $e) {

            $this->response = $this->response->withStatus(409);
            $this->response = $this->response->withHeader('X-WOPI-LockFailureReason', 'Lock operation failed ' . $e->getMessage());
            $this->response = $this->response->withHeader('Content-Type', 'application/json');

            $this->response = $this->response->withStringBody(json_encode([
                'message' => $e->getMessage()
            ]));
        } catch (LockOperationFailedException $e) {

            $this->response = $this->response->withStatus(409);
            $this->response = $this->response->withHeader('X-WOPI-LockFailureReason', 'Lock operation failed ' . $e->getMessage());
            $this->response = $this->response->withHeader('Content-Type', 'application/json');

            $this->response = $this->response->withStringBody(json_encode([
                'message' => $e->getMessage()
            ]));
        } catch (Exception $e) {

            $this->response = $this->response->withStatus(500);
            $this->response = $this->response->withHeader('X-WOPI-LockFailureReason', 'Lock operation failed');
            $this->response = $this->response->withHeader('Content-Type', 'application/json');

            $this->response = $this->response->withStringBody(json_encode([
                'message' => $e->getMessage()
            ]));
        }

        return $this->response;
    }
}
/**
 * -----------------------------------
 * Notes
 * -----------------------------------
 * Unlock
 * Article
 * 03/04/2023
 * 3 contributors
 * Online icon iOS and Android Desktop
 *
 * The Unlock operation releases the lock on a file.
 *
 * POST /wopi/files/(file_id)
 * The Unlock operation releases the lock on a file.
 *
 * WOPI clients usually make a Lock request to lock a file prior to calling this operation. The WOPI client passes the lock ID established by that previous Lock operation in the X-WOPI-Lock request header.
 *
 * If the file is currently locked and the X-WOPI-Lock value doesn't match the lock currently on the file, or if the file is unlocked, the host must:
 *
 * Return a lock mismatch response (409 Conflict)
 * Include an X-WOPI-Lock response header containing the value of the current lock on the file.
 * In cases where the file is unlocked, the host must set X-WOPI-Lock to the empty string.
 *
 * In cases where the file is locked by someone other than a WOPI client, hosts should still always include the current lock ID in the X-WOPI-Lock response header. However, if the current lock ID isn't representable as a WOPI lock (for example, it's longer than the maximum lock length), the X-WOPI-Lock response header should be set to the empty string or omitted completely.
 *
 * For more general information about locks, see Lock.
 *
 * Parameters
 * file_id (string) – A string that specifies a file ID of a file managed by host. This string must be URL safe.
 * Query parameters
 * access_token (string) – An access token that the host uses to determine whether the request is authorized.
 * Request headers
 * X-WOPI-Override – The string UNLOCK. This string is required.
 *
 * X-WOPI-Lock – A string provided by the WOPI client that the host uses to identify the lock on the file. This string is required.
 *
 * Response headers
 * X-WOPI-Lock – A string value identifying the current lock on the file. You must always include this header when responding to the request with 409 Conflict. It shouldn't be included when responding to the request with 200 OK.
 *
 * X-WOPI-LockFailureReason – An optional string value indicating the cause of a lock failure. This header might be included when responding to the request with 409 Conflict. There's no standard for how this string is formatted, and it must only be used for logging purposes.
 *
 * X-WOPI-LockedByOtherInterface –
 *
 * Deprecated: Deprecated since version 2015-12-15: This header is deprecated and should be ignored by WOPI clients.
 *
 * X-WOPI-ItemVersion – An optional string value indicating the version of the file. Its value should be the same as Version value in CheckFileInfo.
 *
 * Status codes
 * 200 OK – Success.
 *
 * 400 Bad Request – X-WOPI-Lock wasn't provided or was empty.
 *
 * 401 Unauthorized – Invalid access token.
 *
 * 404 Not Found – Resource not found or user unauthorized.
 *
 * 409 Conflict – Lock mismatch or locked by another interface. You must include an X-WOPI-Lock response header containing the value of the current lock on the file when using this response code.
 *
 * 500 Internal Server Error – Server error.
 *
 * 501 Not Implemented – Operation not supported.
 *
 * In addition to the request and response headers listed here, this operation might also use the Standard WOPI request and response headers. For more information see Standard WOPI request and response headers.
 */
