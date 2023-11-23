<?php
namespace EaglenavigatorSystem\Wopi\Controller;

use EaglenavigatorSystem\Wopi\Controller\AppController;

/**
 * PutRelativeFile Controller
 *
 */
class GetLockController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index(string $fileId)
    {
      $this->loadComponent('Wopi');
      $this->request->allowMethod(['post']);
      $this->autoRender = false;
      $this->Wopi->logPost($this->request);

      $response = $this->Wopi->getLock($this->request, $fileId);

      return $response;
    }
}
