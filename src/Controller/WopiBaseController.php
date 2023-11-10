<?php
namespace EaglenavigatorSystem\Wopi\Controller;

use EaglenavigatorSystem\Wopi\Controller\AppController;

/**
 * WopiBase Controller
 *
 *
 * @method \EaglenavigatorSystem\Wopi\Model\Entity\WopiBase[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class WopiBaseController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $wopiBase = $this->paginate($this->WopiBase);

        $this->set(compact('wopiBase'));
    }

    /**
     * View method
     *
     * @param string|null $id Wopi Base id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $wopiBase = $this->WopiBase->get($id, [
            'contain' => [],
        ]);

        $this->set('wopiBase', $wopiBase);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $wopiBase = $this->WopiBase->newEntity();
        if ($this->request->is('post')) {
            $wopiBase = $this->WopiBase->patchEntity($wopiBase, $this->request->getData());
            if ($this->WopiBase->save($wopiBase)) {
                $this->Flash->success(__('The wopi base has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The wopi base could not be saved. Please, try again.'));
        }
        $this->set(compact('wopiBase'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Wopi Base id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $wopiBase = $this->WopiBase->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $wopiBase = $this->WopiBase->patchEntity($wopiBase, $this->request->getData());
            if ($this->WopiBase->save($wopiBase)) {
                $this->Flash->success(__('The wopi base has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The wopi base could not be saved. Please, try again.'));
        }
        $this->set(compact('wopiBase'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Wopi Base id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $wopiBase = $this->WopiBase->get($id);
        if ($this->WopiBase->delete($wopiBase)) {
            $this->Flash->success(__('The wopi base has been deleted.'));
        } else {
            $this->Flash->error(__('The wopi base could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
