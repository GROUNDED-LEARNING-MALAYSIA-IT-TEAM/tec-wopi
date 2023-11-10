<?php
namespace EaglenavigatorSystem\Wopi\Controller;

use EaglenavigatorSystem\Wopi\Controller\AppController;

/**
 * PutUserInfo Controller
 *
 *
 * @method \EaglenavigatorSystem\Wopi\Model\Entity\PutUserInfo[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PutUserInfoController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $putUserInfo = $this->paginate($this->PutUserInfo);

        $this->set(compact('putUserInfo'));
    }

    /**
     * View method
     *
     * @param string|null $id Put User Info id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $putUserInfo = $this->PutUserInfo->get($id, [
            'contain' => [],
        ]);

        $this->set('putUserInfo', $putUserInfo);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $putUserInfo = $this->PutUserInfo->newEntity();
        if ($this->request->is('post')) {
            $putUserInfo = $this->PutUserInfo->patchEntity($putUserInfo, $this->request->getData());
            if ($this->PutUserInfo->save($putUserInfo)) {
                $this->Flash->success(__('The put user info has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The put user info could not be saved. Please, try again.'));
        }
        $this->set(compact('putUserInfo'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Put User Info id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $putUserInfo = $this->PutUserInfo->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $putUserInfo = $this->PutUserInfo->patchEntity($putUserInfo, $this->request->getData());
            if ($this->PutUserInfo->save($putUserInfo)) {
                $this->Flash->success(__('The put user info has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The put user info could not be saved. Please, try again.'));
        }
        $this->set(compact('putUserInfo'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Put User Info id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $putUserInfo = $this->PutUserInfo->get($id);
        if ($this->PutUserInfo->delete($putUserInfo)) {
            $this->Flash->success(__('The put user info has been deleted.'));
        } else {
            $this->Flash->error(__('The put user info could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
