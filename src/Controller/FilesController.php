<?php
declare(strict_types=1);

namespace EaglenavigatorSystem\Wopi\Controller;

use EaglenavigatorSystem\Wopi\Controller\AppController;

/**
 * Files Controller
 *
 */
class FilesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
      $this->loadComponent('Wopi');
      $this->request->allowMethod(['get', 'post']);
      $this->autoRender = false;

      $this->Wopi->processHeadersFromMicrosoftAndRedirect();

    }

}