<?php

namespace EaglenavigatorSystem\Wopi\Controller;

use EaglenavigatorSystem\Wopi\Controller\AppController;
use EaglenavigatorSystem\WOpi\Interfaces\CheckFileInfoInterface;

/**
 * CheckFileInfo Controller
 *
 *
 * @method \EaglenavigatorSystem\Wopi\Model\Entity\CheckFileInfo[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CheckFileInfoController extends AppController implements CheckFileInfoInterface
{
    /**
     * Index method
     * url : /wopi/files/{fileId} - get request .
     * - routing in router , user prefix `wopi` and controller `CheckFileInfo` and action `index`
     *  GET /wopi/files/(file_id)
     *  - CheckFileInfo
     *
     * @return \Cake\Http\Response|null
     */
    public function index(string $fileId)
    {
        $this->request->allowMethod(['get']);
        $this->loadComponent('EaglenavigatorSystem/Wopi.Session', [
            'file' => $this->WopiFiles->get($fileId),
            'user' => $this->Auth->user(),
            'session' => $this->request->getSession()
        ]);
        $this->loadComponent('EaglenavigatorSystem/Wopi.Wopi');
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

        $response = $this->response->withType('application/json');
        $response = $this->response->withStatus(200);
        $response = $this->response->withStringBody(json_encode(
            $this->Session->getAttributes()
        ));

        return $response;

    }
}
