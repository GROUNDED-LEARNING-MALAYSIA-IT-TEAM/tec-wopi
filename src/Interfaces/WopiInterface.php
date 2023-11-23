<?php

namespace EaglenavigatorSystem\Wopi\Interfaces;

use Cake\Http\ServerRequest;

interface WopiInterface
{
    public const HEADER_EDITORS = 'X-WOPI-Editors';

    public const HEADER_ITEM_VERSION = 'X-WOPI-ItemVersion';

    public const HEADER_LOCK = 'X-WOPI-Lock';

    public const HEADER_OLD_LOCK = 'X-WOPI-OldLock';

    public const HEADER_OVERRIDE = 'X-WOPI-Override';

    public const HEADER_OVERWRITE_RELATIVE_TARGET = 'X-WOPI-OverwriteRelativeTarget';

    public const HEADER_PROOF = 'X-WOPI-Proof';

    public const HEADER_PROOF_OLD = 'X-WOPI-ProofOld';

    public const HEADER_RELATIVE_TARGET = 'X-WOPI-RelativeTarget';

    public const HEADER_ServerRequestED_NAME = 'X-WOPI-ServerRequestedName';

    public const HEADER_SIZE = 'X-WOPI-Size';

    public const HEADER_SUGGESTED_TARGET = 'X-WOPI-SuggestedTarget';

    public const HEADER_TIMESTAMP = 'X-WOPI-Timestamp';

    public const HEADER_URL_TYPE = 'X-WOPI-UrlType';

    public const HEADER_VALID_RELATIVE_TARGET = 'X-WOPI-ValidRelativeTarget';

    public const HEADER_INVALID_FILE_NAME_ERROR = 'X-WOPI-InvalidFileNameError';

    /**
     * One of the most important WOPI operations. must be implemented for
     * all WOPI actions. it returns information about a file, a user’s
     * permissions on that file and influence the appearance of ui.
     *
     * @param string $accessToken raw access token
     *
     * @return \Illuminate\Http\JsonResponse must return json response.
     */
    public function checkFileInfo(ServerRequest $request,int $fileId);

    /**
     * Retrieve the binary content for the file. including
     * X-WOPI-ItemVersion header indicating the version
     * of the file. Its value should be the same
     * as Version value in CheckFileInfo.
     *
     * @param string $accessToken raw access token
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse must return binary response.
     */
    public function getFile(ServerRequest $request,int $fileId);

    /**
     * Updates a file’s binary contents.
     *
     * @param string $accessToken raw access token
     *
     * @return \Cake\Http\Response
     */
    public function putFile(ServerRequest $request,int $fileId);

    /**
     * Locks a file for editing by the WOPI client that ServerRequested the lock. To
     * support editing files, WOPI clients require that the WOPI host support
     * locking files. the file should not be writable by other applications.
     *
     * @param string $accessToken raw access token
     *
     * @return \Cake\Http\Response
     */
    public function lock(ServerRequest $request,int $fileId);

    /**
     * Releases the lock on a file.
     *
     * @param string $accessToken raw access token
     *
     * @return \Cake\Http\Response
     */
    public function unlock(ServerRequest $request,int $fileId);

    /**
     * Retrieves a lock on a file. It does not create a new lock. returns the current
     * lock value to the X-WOPI-Lock response header. If the file is currently not
     * locked, the host must include X-WOPI-Lock response with an empty string.
     *
     * @param string $accessToken raw access token
     *
     * @return \Cake\Http\Response
     */
    public function getLock(ServerRequest $request,int $fileId);

    /**
     * Refreshes the lock on a file by resetting its automatic expiration timer
     * to 30 minutes. The refreshed lock must expire automatically after 30
     * minutes unless it is modified by a subsequent WOPI operation.
     *
     * @param string $accessToken raw access token
     *
     * @return \Cake\Http\Response
     */
    public function refreshLock(ServerRequest $request,int $fileId);

    /**
     * Alias for refreshLock but with diffrent header X-WOPI-OldLock.
     *
     * @param string $accessToken raw access token
     *
     * @return \Cake\Http\Response
     */
    public function unlockAndRelock(ServerRequest $request,int $fileId);

    /**
     * Delete the file from the host.
     *
     * @param string $accessToken raw access token
     *
     * @return \Cake\Http\Response
     */
    public function deleteFile(ServerRequest $request,int $fileId);

    /**
     * Renames a file. It should not change file id.
     *
     * @param string $fileId
     * @param string $accessToken
     * @param \Cake\Http\ServerRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function renameFile(ServerRequest $request,int $fileId);

    /**
     * Creates a new file on the host based on the
     * current file. The host must use the content
     * in the POST body to create the new file.
     *
     * @param string $fileId
     * @param string $accessToken
     * @param \Cake\Http\ServerRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function putRelativeFile(ServerRequest $request,int $fileId);

    /**
     * Currently unimplemented.
     *
     * @param string $fileId
     * @param string $accessToken
     * @param \Cake\Http\ServerRequest $request
     *
     * @return \Cake\Http\Response
     */
    public function enumerateAncestors(ServerRequest $request,int $fileId);

    /**
     * Stores basic user information on the host. Hosts must store
     * the UserInfo string which is contained in the body of the
     * ServerRequest. The UserInfo string should be associated with a
     * particular user, and should be passed back to the WOPI.
     *
     * @param string $fileId
     * @param string $accessToken
     * @param \Cake\Http\ServerRequest $request Contains body has a maximum size of 1024 ASCII characters.
     *
     * @return \Cake\Http\Response
     */
    public function putUserInfo(ServerRequest $request,int $fileId);
}
