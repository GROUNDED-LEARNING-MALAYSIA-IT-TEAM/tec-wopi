<?php
namespace EaglenavigatorSystem\Wopi\Controller;

use EaglenavigatorSystem\Wopi\Controller\AppController;

/**
 * PutRelativeFile Controller
 *
 *
 * @method \EaglenavigatorSystem\Wopi\Model\Entity\PutRelativeFile[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PutRelativeFileController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $putRelativeFile = $this->paginate($this->PutRelativeFile);

        $this->set(compact('putRelativeFile'));
    }

    /**
     * View method
     *
     * @param string|null $id Put Relative File id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $putRelativeFile = $this->PutRelativeFile->get($id, [
            'contain' => [],
        ]);

        $this->set('putRelativeFile', $putRelativeFile);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $putRelativeFile = $this->PutRelativeFile->newEntity();
        if ($this->request->is('post')) {
            $putRelativeFile = $this->PutRelativeFile->patchEntity($putRelativeFile, $this->request->getData());
            if ($this->PutRelativeFile->save($putRelativeFile)) {
                $this->Flash->success(__('The put relative file has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The put relative file could not be saved. Please, try again.'));
        }
        $this->set(compact('putRelativeFile'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Put Relative File id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $putRelativeFile = $this->PutRelativeFile->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $putRelativeFile = $this->PutRelativeFile->patchEntity($putRelativeFile, $this->request->getData());
            if ($this->PutRelativeFile->save($putRelativeFile)) {
                $this->Flash->success(__('The put relative file has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The put relative file could not be saved. Please, try again.'));
        }
        $this->set(compact('putRelativeFile'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Put Relative File id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $putRelativeFile = $this->PutRelativeFile->get($id);
        if ($this->PutRelativeFile->delete($putRelativeFile)) {
            $this->Flash->success(__('The put relative file has been deleted.'));
        } else {
            $this->Flash->error(__('The put relative file could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
