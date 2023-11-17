<?php

namespace EaglenavigatorSystem\Wopi\Controller;

use EaglenavigatorSystem\Wopi\Controller\AppController;
use Exception;

/**
 * GetFile Controller
 *
 *
 * @method \EaglenavigatorSystem\Wopi\Model\Entity\GetFile[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class GetFileController extends AppController
{
    //this for /file/{fileId}/contents
    public function getFile(string $fileId)
    {
        $this->request->allowMethod(['get']);

        try {

            $userId = $this->Auth->user('id');


            $token = $this->request->getQuery('access_token');

            if (!$token) {

                // Return a JSON response with a 404 status code
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

                // Return a JSON response with a 404 status code
                $response = $this->response->withStatus(401);
                $response = $response->withType('application/json');
                $response = $response->withStringBody(json_encode(['error' => 'Invalid access token']));
                return $response;
            }

            $file = $this->WopiFiles->find('all', [
              'conditions' => [
                'id' => $fileId,
                'user_id' => $userId
              ]
            ])->first();

            if (!$file) {

                // Return a JSON response with a 404 status code
                $response = $this->response->withStatus(404);
                $response = $response->withType('application/json');
                $response = $response->withStringBody(json_encode(['error' => 'File not found']));
                return $response;
            }

            $fileContent = file_get_contents($file->file_path);

            //return response for wopi
            $response = $this->response->withType('application/octet-stream');
            $response = $response->withStringBody($fileContent);


            return $response;
        } catch (Exception $e) {
            $response = $this->response->withStatus(500);
            $response = $response->withType('application/json');
            $response = $response->withStringBody(json_encode(['error' => 'Error '. $e->getMessage()]));
            return $response;
        }

        //response for wopi
        $response =
        $response->withStatus(200);
        $response = $response->withHeader('X-WOPI-ItemVersion', (string) $file->getVersion());
        //WOPI_FILE_PATH
        $tempfile = WOPI_FILE_PATH . $file->file_name;
        if (is_resource($file->file_data)) {
            //IF LOCATION NOT EMPTY DELETE
            if (file_exists($tempfile)) {
                unlink($tempfile);
            }

            // If it's a stream, read the content and then write it to the temp file

            $streamContent = stream_get_contents($file->file_data);
            fwrite($tempfile, $streamContent);

            // Close the stream
            fclose($file->file_data);

            //response for wopi with file
            $response = $response->withFile($tempfile, ['download' => true, 'name' => $file->file_name]);
        }




    }
}
