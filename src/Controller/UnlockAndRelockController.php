<?php
namespace EaglenavigatorSystem\Wopi\Controller;

use EaglenavigatorSystem\Wopi\Controller\AppController;

/**
 * UnlockAndRelock Controller
 *
 *
 * @method \EaglenavigatorSystem\Wopi\Model\Entity\UnlockAndRelock[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UnlockAndRelockController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $unlockAndRelock = $this->paginate($this->UnlockAndRelock);

        $this->set(compact('unlockAndRelock'));
    }

    /**
     * View method
     *
     * @param string|null $id Unlock And Relock id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $unlockAndRelock = $this->UnlockAndRelock->get($id, [
            'contain' => [],
        ]);

        $this->set('unlockAndRelock', $unlockAndRelock);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $unlockAndRelock = $this->UnlockAndRelock->newEntity();
        if ($this->request->is('post')) {
            $unlockAndRelock = $this->UnlockAndRelock->patchEntity($unlockAndRelock, $this->request->getData());
            if ($this->UnlockAndRelock->save($unlockAndRelock)) {
                $this->Flash->success(__('The unlock and relock has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The unlock and relock could not be saved. Please, try again.'));
        }
        $this->set(compact('unlockAndRelock'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Unlock And Relock id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $unlockAndRelock = $this->UnlockAndRelock->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $unlockAndRelock = $this->UnlockAndRelock->patchEntity($unlockAndRelock, $this->request->getData());
            if ($this->UnlockAndRelock->save($unlockAndRelock)) {
                $this->Flash->success(__('The unlock and relock has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The unlock and relock could not be saved. Please, try again.'));
        }
        $this->set(compact('unlockAndRelock'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Unlock And Relock id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $unlockAndRelock = $this->UnlockAndRelock->get($id);
        if ($this->UnlockAndRelock->delete($unlockAndRelock)) {
            $this->Flash->success(__('The unlock and relock has been deleted.'));
        } else {
            $this->Flash->error(__('The unlock and relock could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
