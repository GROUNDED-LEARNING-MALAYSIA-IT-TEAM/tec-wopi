<?php
namespace EaglenavigatorSystem\Wopi\Controller;

use EaglenavigatorSystem\Wopi\Controller\AppController;

/**
 * RenameFile Controller
 *
 *
 * @method \EaglenavigatorSystem\Wopi\Model\Entity\RenameFile[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class RenameFileController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $renameFile = $this->paginate($this->RenameFile);

        $this->set(compact('renameFile'));
    }

    /**
     * View method
     *
     * @param string|null $id Rename File id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $renameFile = $this->RenameFile->get($id, [
            'contain' => [],
        ]);

        $this->set('renameFile', $renameFile);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $renameFile = $this->RenameFile->newEntity();
        if ($this->request->is('post')) {
            $renameFile = $this->RenameFile->patchEntity($renameFile, $this->request->getData());
            if ($this->RenameFile->save($renameFile)) {
                $this->Flash->success(__('The rename file has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The rename file could not be saved. Please, try again.'));
        }
        $this->set(compact('renameFile'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Rename File id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $renameFile = $this->RenameFile->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $renameFile = $this->RenameFile->patchEntity($renameFile, $this->request->getData());
            if ($this->RenameFile->save($renameFile)) {
                $this->Flash->success(__('The rename file has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The rename file could not be saved. Please, try again.'));
        }
        $this->set(compact('renameFile'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Rename File id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $renameFile = $this->RenameFile->get($id);
        if ($this->RenameFile->delete($renameFile)) {
            $this->Flash->success(__('The rename file has been deleted.'));
        } else {
            $this->Flash->error(__('The rename file could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
