<?php
namespace EaglenavigatorSystem\Wopi\Controller;

use EaglenavigatorSystem\Wopi\Controller\AppController;

/**
 * RefreshLock Controller
 */
class RefreshLockController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index(string $fileId)
    {
        //method only accepts post requests
        $this->request->allowMethod(['post']);

        $this->loadComponent('EaglenavigatorSystem/Wopi.Session', [
            'file' => $this->WopiFiles->get($fileId),
            'user' => $this->Auth->user(),
            'session' => $this->request->getSession()
        ]);

        $this->loadComponent('EaglenavigatorSystem/Wopi.Wopi');

        $this->Wopi->logPost($this->request);

        return $this->Wopi->refreshLock($this->request, $fileId);

    }
}
