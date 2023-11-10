<?php
namespace EaglenavigatorSystem\Wopi\Controller;

use EaglenavigatorSystem\Wopi\Controller\AppController;

/**
 * RefreshLock Controller
 *
 *
 * @method \EaglenavigatorSystem\Wopi\Model\Entity\RefreshLock[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class RefreshLockController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $refreshLock = $this->paginate($this->RefreshLock);

        $this->set(compact('refreshLock'));
    }

    /**
     * View method
     *
     * @param string|null $id Refresh Lock id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $refreshLock = $this->RefreshLock->get($id, [
            'contain' => [],
        ]);

        $this->set('refreshLock', $refreshLock);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $refreshLock = $this->RefreshLock->newEntity();
        if ($this->request->is('post')) {
            $refreshLock = $this->RefreshLock->patchEntity($refreshLock, $this->request->getData());
            if ($this->RefreshLock->save($refreshLock)) {
                $this->Flash->success(__('The refresh lock has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The refresh lock could not be saved. Please, try again.'));
        }
        $this->set(compact('refreshLock'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Refresh Lock id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $refreshLock = $this->RefreshLock->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $refreshLock = $this->RefreshLock->patchEntity($refreshLock, $this->request->getData());
            if ($this->RefreshLock->save($refreshLock)) {
                $this->Flash->success(__('The refresh lock has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The refresh lock could not be saved. Please, try again.'));
        }
        $this->set(compact('refreshLock'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Refresh Lock id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $refreshLock = $this->RefreshLock->get($id);
        if ($this->RefreshLock->delete($refreshLock)) {
            $this->Flash->success(__('The refresh lock has been deleted.'));
        } else {
            $this->Flash->error(__('The refresh lock could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
