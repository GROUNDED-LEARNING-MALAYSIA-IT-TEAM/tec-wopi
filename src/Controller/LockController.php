<?php

declare(strict_types=1);

namespace EaglenavigatorSystem\Wopi\Controller;

use EaglenavigatorSystem\Wopi\Controller\AppController;

/**
 * Lock Controller
 *
 */
class LockController extends AppController
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

        $response = $this->Wopi->lock($this->request, $fileId);
        return $response;
    }
}
