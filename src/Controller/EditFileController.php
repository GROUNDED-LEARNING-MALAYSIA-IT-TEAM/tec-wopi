<?php
namespace EaglenavigatorSystem\Wopi\Controller;

use EaglenavigatorSystem\Wopi\Controller\AppController;

/**
 * DeleteFile Controller
 */
class EditFileController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index(string $fileId)
    {
        $token = $this->request->getQuery('access_token');

        $this->loadComponent('EaglenavigatorSystem/Wopi.Wopi');

        $this->loadComponent('EaglenavigatorSystem/Wopi.Session', [
            'file' => $this->WopiFiles->get($fileId),
            'user' => $this->Auth->user(),
            'session' => $this->request->getSession()
        ]);

    }

}
