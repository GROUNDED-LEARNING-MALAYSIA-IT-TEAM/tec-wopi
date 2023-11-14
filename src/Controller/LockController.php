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

        $userId = $this->Auth->user('id');

        if (!$filefileId) {
            $message = 'File id not provided';
            $this->response = $this->response->withStatus(404);
            $this->response = $this->response->withHeader('X-WOPI-LockFailureReason', 'File not found');

            return $this->response->withHeader('Content-Type', 'application/json')->withStringBody(json_encode([
               'message' => $message
           ]));
        }

         $file = $this->Wopi->getWopiFileById($fileId);

         if (!$file) {
             $message = 'File not found';
             $this->response = $this->response->withStatus(404);
             $this->response = $this->response->withHeader('X-WOPI-LockFailureReason', 'File not found');

             return $this->response->withHeader('Content-Type', 'application/json')->withStringBody(json_encode([
                'message' => $message
            ]));
         }

         $lock = $this->Locks->lockFile($fileId, $userId);

            if ($lock) {
                $this->response = $this->response->withHeader('X-WOPI-Lock', $lock->lock);
                $this->response = $this->response->withHeader('X-WOPI-ItemVersion', $file->version);
                $this->response = $this->response->withStatus(200);
            } else {
                $this->response = $this->response->withHeader('X-WOPI-LockFailureReason', 'File already locked');

                $this->response = $this->response->withStatus(409);
            }

            //return response as json

            return $this->response->withHeader('Content-Type', 'application/json');

    }
}