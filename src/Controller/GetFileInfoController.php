<?php

namespace EaglenavigatorSystem\Wopi\Controller;

use EaglenavigatorSystem\Wopi\Controller\AppController;

/**
 * GetFile Controller
 *
 *
 * @method \EaglenavigatorSystem\Wopi\Model\Entity\GetFile[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class GetFileInfoController extends AppController
{

  public function initialize()
  {
    parent::initialize();
  }

  public function index(string $fileId)
  {
    $this->request->allowMethod(['get']);

    $userId = $this->Auth->user('id');

    $userName = $this->Auth->user('username');

    $token = $this->request->getQuery('access_token');

    if (!$token) {

      $this->Flash->error(__('Invalid access token'));
      return $this->redirect(['prefix' => false, 'plugin' => false, 'controller' => 'Index', 'action' => 'index']);
    }

    $userSession = $this->UserSessions->find('all', [
      'conditions' => [
        'user_id' => $userId,
        'token' => $token
      ]
    ])->first();

    if (!$userSession) {

      $this->Flash->error(__('Invalid access token'));
      return $this->redirect(['prefix' => false, 'plugin' => false, 'controller' => 'Index', 'action' => 'index']);
    }


    $file = $this->WopiFiles->find('all', [
      'conditions' => [
        'id' => $fileId,
        'user_id' => $userId
      ]
    ])->first();

    if (!$file) {

      $this->Flash->error(__('Invalid file id'));
      return $this->redirect(['prefix' => false, 'plugin' => false, 'controller' => 'Index', 'action' => 'index']);
    }

    $infoJson = array(
      "BaseFileName" => $file->getFile_uuid(),
      "OwnerId" => $userId,
      "UserId" => $userId,
      "UserFriendlyName" => $userName,
      "Size" => $file->getSize(),
      "Version" => $file->getVersion(),
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
      "BreadcrumbBrandName" => "The Eagle Navigator",
      "BreadcrumbBrandUrl" => "https://www.theeagle.center",
      "HostEditUrl" => $this->Wopi->getHostEditUrl(
        $this->request->getSession()->id(),
        $fileId,
        $userId
      )
    );

    $this->response = $this->response->withStringBody(json_encode($infoJson));

    $this->response = $this->response->withHeader('Content-Type', 'application/json');
  }
}
