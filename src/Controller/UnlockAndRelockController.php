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
    public function index()
    {
        //method only accepts post requests
        $this->request->allowMethod(['post']);
    }
}
