<?php
namespace EaglenavigatorSystem\Wopi\Controller;

use EaglenavigatorSystem\Wopi\Controller\AppController;

/**
 * UnlockAndRelock Controller
 *
 */
class UnlockAndRelockController extends AppController
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

         //put user info here using $this->request->getData()

         $this->loadComponent('EaglenavigatorSystem/Wopi.Session', [
            'file' => $this->WopiFiles->get($fileId),
            'user' => $this->Auth->user(),
            'session' => $this->request->getSession()
        ]);

        $this->loadComponent('EaglenavigatorSystem/Wopi.Wopi');

        $this->Wopi->logPost($this->request);

        $response = $this->Wopi->unlockAndRelock($this->request,$fileId);

        return $response;
    }
}
