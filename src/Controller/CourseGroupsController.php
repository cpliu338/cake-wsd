<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * CourseGroups Controller
 *
 * @property \App\Model\Table\CourseGroupsTable $CourseGroups
 * @method \App\Model\Entity\CourseGroup[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CourseGroupsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $courseGroups = $this->paginate($this->CourseGroups);

        $this->set(compact('courseGroups'));
    }

    /**
     * View method
     *
     * @param string|null $id Course Group id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $courseGroup = $this->CourseGroups->get($id, [
            'contain' => ['ApplicationForms', 'CourseInstances'],
        ]);

        $this->set(compact('courseGroup'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $courseGroup = $this->CourseGroups->newEmptyEntity();
        if ($this->request->is('post')) {
            $courseGroup = $this->CourseGroups->patchEntity($courseGroup, $this->request->getData());
            if ($this->CourseGroups->save($courseGroup)) {
                $this->Flash->success(__('The course group has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The course group could not be saved. Please, try again.'));
        }
        $this->set(compact('courseGroup'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Course Group id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $courseGroup = $this->CourseGroups->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $courseGroup = $this->CourseGroups->patchEntity($courseGroup, $this->request->getData());
            if ($this->CourseGroups->save($courseGroup)) {
                $this->Flash->success(__('The course group has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The course group could not be saved. Please, try again.'));
        }
        $this->set(compact('courseGroup'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Course Group id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $courseGroup = $this->CourseGroups->get($id);
        if ($this->CourseGroups->delete($courseGroup)) {
            $this->Flash->success(__('The course group has been deleted.'));
        } else {
            $this->Flash->error(__('The course group could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
