<?php
namespace EaglenavigatorSystem\Wopi\Controller;

use EaglenavigatorSystem\Wopi\Controller\AppController;

/**
 * DeleteFile Controller
 *
 *
 * @method \EaglenavigatorSystem\Wopi\Model\Entity\DeleteFile[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class DeleteFileController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $deleteFile = $this->paginate($this->DeleteFile);

        $this->set(compact('deleteFile'));
    }

    /**
     * View method
     *
     * @param string|null $id Delete File id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $deleteFile = $this->DeleteFile->get($id, [
            'contain' => [],
        ]);

        $this->set('deleteFile', $deleteFile);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $deleteFile = $this->DeleteFile->newEntity();
        if ($this->request->is('post')) {
            $deleteFile = $this->DeleteFile->patchEntity($deleteFile, $this->request->getData());
            if ($this->DeleteFile->save($deleteFile)) {
                $this->Flash->success(__('The delete file has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The delete file could not be saved. Please, try again.'));
        }
        $this->set(compact('deleteFile'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Delete File id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $deleteFile = $this->DeleteFile->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $deleteFile = $this->DeleteFile->patchEntity($deleteFile, $this->request->getData());
            if ($this->DeleteFile->save($deleteFile)) {
                $this->Flash->success(__('The delete file has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The delete file could not be saved. Please, try again.'));
        }
        $this->set(compact('deleteFile'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Delete File id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $deleteFile = $this->DeleteFile->get($id);
        if ($this->DeleteFile->delete($deleteFile)) {
            $this->Flash->success(__('The delete file has been deleted.'));
        } else {
            $this->Flash->error(__('The delete file could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
