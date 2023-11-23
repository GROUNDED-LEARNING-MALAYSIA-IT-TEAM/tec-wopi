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
        $this->loadComponent('EaglenavigatorSystem/Wopi.Wopi');
        $this->Wopi->logPost($this->request);

        // Use the checkFileInfo method from the Wopi component
        $response = $this->Wopi->checkFileInfo($this->request, $fileId);

        // Return the response from the component
        return $response;

    }
}
