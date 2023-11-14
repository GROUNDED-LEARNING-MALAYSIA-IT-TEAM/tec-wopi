<?php

namespace EaglenavigatorSystem\Wopi\Controller;

use EaglenavigatorSystem\Wopi\Controller\AppController;

/**
 * CheckFileInfo Controller
 *
 *
 * @method \EaglenavigatorSystem\Wopi\Model\Entity\CheckFileInfo[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CheckFileInfoController extends AppController
{
    /**
     * Index method
     * url : /wopi/files/{fileId} - get request .
     * - routing in router , user prefix `wopi` and controller `CheckFileInfo` and action `index`
     *  GET /wopi/files/(file_id)
     *  - CheckFileInfo
     *
     * @return \Cake\Http\Response|null
     */
    public function index(string $fileId)
    {
        $this->request->allowMethod(['get']);

        $userId = $this->Auth->user('id');

        $userName = $this->Auth->user('username');


        $token = $this->request->getQuery('access_token');

        if (!$token) {


            $response = $this->response->withStatus(401);
            $response = $response->withType('application/json');
            $response = $response->withStringBody(json_encode(['error' => 'Invalid access token']));
            return $response;
        }

        $userSession = $this->UserSessions->find('all', [
            'conditions' => [
                'user_id' => $userId,
                'token' => $token
            ]
        ])->first();

        if (!$userSession) {

            $response = $this->response->withStatus(401);
            $response = $response->withType('application/json');
            $response = $response->withStringBody(json_encode(['error' => 'Invalid access token']));
            return $response;
        }

        if (!$fileId) {
            //return 404 json response
            // Return a JSON response with a 404 status code
            $response = $this->response->withStatus(404);
            $response = $response->withType('application/json');
            $response = $response->withStringBody(json_encode(['error' => 'File not found']));
            return $response;
        }

        $file = $this->WopiFiles->find('all', [
            'conditions' => [
                'id' => $fileId,
                'user_id' => $userId
            ]
        ])->first();

        if (!$file) {
            //return 404 json response
            // Return a JSON response with a 404 status code
            $response = $this->response->withStatus(404);
            $response = $response->withType('application/json');
            $response = $response->withStringBody(json_encode(['error' => 'File not found']));
            return $response;
        }

        $infoJson = array(
            "BaseFileName" => $file->getFile_uuid(),
            "OwnerId" => $userId,
            "UserId" => $userId,
            "UserFriendlyName" => $userName,
            "Size" => $file->getSize(),
            "Version" => $file->modified->format("c"),
            "LastModifiedTime" => $file->modified->format("c"),
            "UserCanWrite" => true,
            "RestrictedWebViewOnly" => false,
            "ReadOnly" => false,
            "SupportsUpdate" => true,
            "SupportsLocks" => true,
            "SupportsGetLock" => true,
            "SupportsExtendedLockLength" => true,
            "SupportsCobalt" => false,
            "SupportsFolders" => false,
            "SupportsDeleteFile" => true,
            "LicenseCheckForEditIsEnabled" => true,
            "UserCanNotWriteRelative" => false,
            "SupportsRename" => true,
            "BreadcrumbBrandName" =>"The Eagle Navigator",
            "BreadcrumbBrandUrl" => "https://www.theeagle.center",
            "HostEditUrl" => $this->Wopi->getHostEditUrl($file->id, $userId);
        );
    }
}
