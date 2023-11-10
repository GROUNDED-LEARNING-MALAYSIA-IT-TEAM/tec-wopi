<?php
namespace EaglenavigatorSystem\Wopi\Controller;

use EaglenavigatorSystem\Wopi\Controller\AppController;

/**
 * CheckFileInfo Controller
 *
 *
 * @method \EaglenavigatorSystem\Wopi\Model\Entity\CheckFileInfo[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CheckFileInfoController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $checkFileInfo = $this->paginate($this->CheckFileInfo);

        $this->set(compact('checkFileInfo'));
    }

    /**
     * View method
     *
     * @param string|null $id Check File Info id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $checkFileInfo = $this->CheckFileInfo->get($id, [
            'contain' => [],
        ]);

        $this->set('checkFileInfo', $checkFileInfo);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $checkFileInfo = $this->CheckFileInfo->newEntity();
        if ($this->request->is('post')) {
            $checkFileInfo = $this->CheckFileInfo->patchEntity($checkFileInfo, $this->request->getData());
            if ($this->CheckFileInfo->save($checkFileInfo)) {
                $this->Flash->success(__('The check file info has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The check file info could not be saved. Please, try again.'));
        }
        $this->set(compact('checkFileInfo'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Check File Info id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $checkFileInfo = $this->CheckFileInfo->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $checkFileInfo = $this->CheckFileInfo->patchEntity($checkFileInfo, $this->request->getData());
            if ($this->CheckFileInfo->save($checkFileInfo)) {
                $this->Flash->success(__('The check file info has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The check file info could not be saved. Please, try again.'));
        }
        $this->set(compact('checkFileInfo'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Check File Info id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $checkFileInfo = $this->CheckFileInfo->get($id);
        if ($this->CheckFileInfo->delete($checkFileInfo)) {
            $this->Flash->success(__('The check file info has been deleted.'));
        } else {
            $this->Flash->error(__('The check file info could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
