<?php

namespace EaglenavigatorSystem\Wopi\Controller;

use EaglenavigatorSystem\Wopi\Controller\AppController;
use Exception;

/**
 * PutFile Controller
 *
 */
class PutFileController extends AppController
{
    /**
     * Index method
     * - this for wopi PutFile
     *POST /wopi/files/(file_id)/contents
     * @return \Cake\Http\Response|null
     */
    public function index(string $fileId)
    {
        $this->request->allowMethod(['post']);

        $this->loadComponent('EaglenavigatorSystem/Wopi.Session', [
            'file' => $this->WopiFiles->get($fileId),
            'user' => $this->Auth->user(),
            'session' => $this->request->getSession()
        ]);
        $this->loadComponent('EaglenavigatorSystem/Wopi.Wopi');

        $this->Wopi->logPost($this->request);

        return $this->Wopi->putFile($this->request, $fileId);
    }
}
/**
 * ### notes
 *
 * Learn  File operations
PutFile
Article
03/04/2023
3 contributors
Online icon iOS and Android Desktop

The PutFile operation updates a file’s binary contents.

POST /wopi/files/(file_id)/contents
The PutFile operation updates a file’s binary contents.

WOPI clients usually make a Lock request to lock a file prior to calling this operation. The WOPI client passes the lock ID established by that previous Lock operation in the X-WOPI-Lock request header.

When a host receives a PutFile request on a file that's not locked, the host checks the current size of the file. If it's 0 bytes, the PutFile request should be considered valid and should proceed. If it's any value other than 0 bytes, or missing altogether, the host should respond with a 409 Conflict. For more information, see Creating new files using Office for the web.

If the file is currently locked and the X-WOPI-Lock value doesn't match the lock currently on the file, the host must

Return a lock mismatch response (409 Conflict)
Include an X-WOPI-Lock response header containing the value of the current lock on the file.
In cases where the file is unlocked, the host must set X-WOPI-Lock to the empty string.

In cases where the file is locked by someone other than a WOPI client, hosts should still always include the current lock ID in the X-WOPI-Lock response header. However, if the current lock ID isn't representable as a WOPI lock (for example, it's longer than the maximum lock length), the X-WOPI-Lock response header should be set to the empty string or omitted completely.

Parameters
file_id (string) – A string that specifies a file ID of a file managed by host. This string must be URL safe.
Query Parameters
access_token (string) – An access token that the host uses to determine whether the request is authorized.
Request headers
X-WOPI-Override – The string PUT. This header is required.

X-WOPI-Lock – A string provided by the WOPI client in a previous Lock request. This header isn't included during document creation.

X-WOPI-Editors – A comma-delimited string of UserId values representing all the users who contributed changes to the document in this PutFile request.

Request body
The request body must be the full binary contents of the file.

Response headers
X-WOPI-Lock – A string value identifying the current lock on the file. You must always include this header when responding to the request with 409 Conflict. It shouldn't be included when responding to the request with 200 OK.

X-WOPI-LockFailureReason – An optional string value indicating the cause of a lock failure. This header might be included when responding to the request with 409 Conflict. There's no standard for how this string is formatted, and it must only be used for logging purposes.

X-WOPI-LockedByOtherInterface – Deprecated: Deprecated since version 2015-12-15: This header is deprecated and should be ignored by WOPI clients.

X-WOPI-ItemVersion – An optional string value indicating the version of the file. Its value should be the same as Version value in CheckFileInfo.

 Tip

For PutFile responses, this should be the version of the file after the PutFile operation.

Status codes
200 OK – Success.

401 Unauthorized – Invalid access token.

404 Not Found – Resource not found or user unauthorized.

409 Conflict – Lock mismatch or locked by another interface. You must include an X-WOPI-Lock response header containing the value of the current lock on the file when using this response code.

413 Request Entity Too Large – File is too large. The maximum file size is host-specific.

500 Internal Server Error – Server error.

501 Not Implemented – Operation not supported.

In addition to the request and response headers listed here, this operation might also use the Standard WOPI request and response headers. For more information see Standard WOPI request and response headers.
 */
