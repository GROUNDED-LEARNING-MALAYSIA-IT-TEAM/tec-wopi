<?php
namespace EaglenavigatorSystem\Wopi\Controller;

use EaglenavigatorSystem\Wopi\Controller\AppController;

/**
 * Unlock Controller
 *
 *
 * @method \EaglenavigatorSystem\Wopi\Model\Entity\Unlock[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UnlockController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $unlock = $this->paginate($this->Unlock);

        $this->set(compact('unlock'));
    }

    /**
     * View method
     *
     * @param string|null $id Unlock id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $unlock = $this->Unlock->get($id, [
            'contain' => [],
        ]);

        $this->set('unlock', $unlock);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $unlock = $this->Unlock->newEntity();
        if ($this->request->is('post')) {
            $unlock = $this->Unlock->patchEntity($unlock, $this->request->getData());
            if ($this->Unlock->save($unlock)) {
                $this->Flash->success(__('The unlock has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The unlock could not be saved. Please, try again.'));
        }
        $this->set(compact('unlock'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Unlock id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $unlock = $this->Unlock->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $unlock = $this->Unlock->patchEntity($unlock, $this->request->getData());
            if ($this->Unlock->save($unlock)) {
                $this->Flash->success(__('The unlock has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The unlock could not be saved. Please, try again.'));
        }
        $this->set(compact('unlock'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Unlock id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $unlock = $this->Unlock->get($id);
        if ($this->Unlock->delete($unlock)) {
            $this->Flash->success(__('The unlock has been deleted.'));
        } else {
            $this->Flash->error(__('The unlock could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
