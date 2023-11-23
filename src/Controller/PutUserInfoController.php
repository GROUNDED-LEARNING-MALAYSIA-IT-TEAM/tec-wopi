<?php
namespace EaglenavigatorSystem\Wopi\Controller;

use EaglenavigatorSystem\Wopi\Controller\AppController;

/**
 * PutUserInfo Controller
 */
class PutUserInfoController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index(string $fileId)
    {
        $this->request->allowMethod(['post']);

        //put user info here using $this->request->getData()

        $this->loadComponent('EaglenavigatorSystem/Wopi.Session', [
            'file' => $this->WopiFiles->get($fileId),
            'user' => $this->Auth->user(),
            'session' => $this->request->getSession()
        ]);

        $this->loadComponent('EaglenavigatorSystem/Wopi.Wopi');

        $this->Wopi->logPost($this->request);

        $headerOverride = $this->request->getHeader('X-WOPI-Override');
        if ($headerOverride && $headerOverride[0] == 'PUT_USER_INFO') {
            return $this->Wopi->putUserInfo($this->request);

        }
    }

}
