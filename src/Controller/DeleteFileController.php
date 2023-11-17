<?php

namespace EaglenavigatorSystem\Wopi\Controller;

use EaglenavigatorSystem\Wopi\Controller\AppController;

use function PHPSTORM_META\map;

/**
 * DeleteFile Controller
 */
class DeleteFileController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index(string $fileId)
    {
        $this->request->allowMethod(['post']);

        $this->loadComponent('EaglenavigatorSystem/Wopi.Session', [
            'file' => $this->WopiFiles->get($fileId),
            'user' => $this->Auth->user(),
            'session' => $this->request->getSession()
        ]);
        $this->loadComponent('EaglenavigatorSystem/Wopi.Wopi');
        $this->loadModel('EaglenavigatorSystem/Wopi.WopiFiles');
        $userId = $this->Auth->user('id');



        $token = $this->request->getQuery('access_token');

        if (!$token || $this->Wopi->checkAccessToken($this->request)) {


            $response = $this->response->withStatus(401);
            $response = $response->withType('application/json');
            $response = $response->withStringBody(json_encode(['error' => 'Invalid access token']));
            return $response;
        }

        $userSession = $this->UserSessions->find('all', [
            'conditions' => [
                'user_id' => $userId,
                'token' => $token
            ]
        ])->first();

        if (!$userSession) {

            $response = $this->response->withStatus(401);
            $response = $response->withType('application/json');
            $response = $response->withStringBody(json_encode(['error' => 'Invalid access token']));
            return $response;
        }

        if (!$fileId) {
            //return 404 json response
            // Return a JSON response with a 404 status code
            $response = $this->response->withStatus(404);
            $response = $response->withType('application/json');
            $response = $response->withStringBody(json_encode(['error' => 'File not found']));
            return $response;
        }

        $file = $this->WopiFiles->find('all', [
            'conditions' => [
                'id' => $fileId,
                'user_id' => $userId
            ]
        ])->first();

        if (!$file) {
            //return 404 json response
            // Return a JSON response with a 404 status code
            $response = $this->response->withStatus(404);
            $response = $response->withType('application/json');
            $response = $response->withStringBody(json_encode(['error' => 'File not found']));
            return $response;
        }

        $response = $this->response->withHeader('X-WOPI-ItemVersion', $file->version);

        //delete file

        if ($this->WopiFiles->deleteFile($file->id)) {
            // return status compatible with MS Office
            $response = $this->response->withStatus(200);
            $response = $response->withType('application/json');
            $response = $response->withStringBody(json_encode(['success' => 'File deleted']));
            return $response;
        } else {
            $response = $this->response->withStatus(500);
            $response = $response->withType('application/json');
            $response = $response->withStringBody(json_encode(['error' => 'File not deleted']));
            return $response;
        }
    }

}
