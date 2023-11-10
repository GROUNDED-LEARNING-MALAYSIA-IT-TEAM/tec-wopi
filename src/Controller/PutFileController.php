<?php
namespace EaglenavigatorSystem\Wopi\Controller;

use EaglenavigatorSystem\Wopi\Controller\AppController;

/**
 * PutFile Controller
 *
 *
 * @method \EaglenavigatorSystem\Wopi\Model\Entity\PutFile[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PutFileController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $putFile = $this->paginate($this->PutFile);

        $this->set(compact('putFile'));
    }

    /**
     * View method
     *
     * @param string|null $id Put File id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $putFile = $this->PutFile->get($id, [
            'contain' => [],
        ]);

        $this->set('putFile', $putFile);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $putFile = $this->PutFile->newEntity();
        if ($this->request->is('post')) {
            $putFile = $this->PutFile->patchEntity($putFile, $this->request->getData());
            if ($this->PutFile->save($putFile)) {
                $this->Flash->success(__('The put file has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The put file could not be saved. Please, try again.'));
        }
        $this->set(compact('putFile'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Put File id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $putFile = $this->PutFile->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $putFile = $this->PutFile->patchEntity($putFile, $this->request->getData());
            if ($this->PutFile->save($putFile)) {
                $this->Flash->success(__('The put file has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The put file could not be saved. Please, try again.'));
        }
        $this->set(compact('putFile'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Put File id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $putFile = $this->PutFile->get($id);
        if ($this->PutFile->delete($putFile)) {
            $this->Flash->success(__('The put file has been deleted.'));
        } else {
            $this->Flash->error(__('The put file could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
