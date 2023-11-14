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

        //check token validity
        $tokenValid = $this->Wopi->checkAccessToken($this->request);

        if(!$tokenValid){

            $this->response = $this->response->withStatus(401);
            $this->response = $this->response->withHeader('X-WOPI-LockFailureReason', 'Invalid access token');

            return $this->response->withHeader('Content-Type', 'application/json')
            ->withStringBody(json_encode([
                'message' => 'Invalid access token'
            ]));
        }

        if (!$fileId) {
            $message = 'File id not provided';
            $this->response = $this->response->withStatus(404);
            $this->response = $this->response->withHeader('X-WOPI-LockFailureReason', 'File not found');

            return $this->response->withHeader('Content-Type', 'application/json')
            ->withStringBody(json_encode([
                'message' => $message
            ]));
        }

        $file = $this->Wopi->getWopiFileById($fileId);

        if (!$file) {
            $message = 'File not found';
            $this->response = $this->response->withStatus(404);
            $this->response = $this->response->withHeader('X-WOPI-LockFailureReason', 'File not found');

            return $this->response->withHeader(
                'Content-Type',
                'application/json'
            )
                ->withStringBody(json_encode([
                    'message' => $message
                ]));
        }

        $lock = $this->Locks->lockFile($fileId, $userId);

        if ($lock) {
            //no need to include x-wopi-lock in the response header on success 200
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
