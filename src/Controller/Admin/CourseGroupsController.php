<?php
declare(strict_types=1);

namespace App\Controller\Admin;
use App\Controller\AppController;

/**
 * CourseGroups Controller
 *
 * @property \App\Model\Table\CourseGroupsTable $CourseGroups
 * @method \App\Model\Entity\CourseGroup[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CourseGroupsController extends AppController
{
    var $util;

    public function initialize(): void
    {
        parent::initialize();
        $this->util = new \App\Utils\CoursesUtils();
    }

    public function upload(): void
    {
        $ar = ['action'=>'upload'];
        $attachment = $this->request->getData('file');
        $ar['name'] = $attachment->getClientFilename();
        $ar['type'] = $attachment->getClientMediaType();
        $ar['size'] = $attachment->getSize();
        //$attachment->moveTo($target_path)
        $this->set('result', $ar);
        $this->viewBuilder()->setOption('serialize', ['result']);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $courseGroups = $this->paginate($this->CourseGroups);
        $courseGroup = $this->CourseGroups->newEntity(['division' => 'MEM']);
        $this->set(compact('courseGroups', 'courseGroup'));
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
     * Add a dummy course
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function addDummyCourse()
    {

        if ($this->request->is('post')) {
            $division = $this->request->getData('division');
            $courseGroup = $this->util->DummyCourse($division);
            if ($this->CourseGroups->save($courseGroup)) {
                $this->Flash->success(__('Added dummy:') . $courseGroup->title);
            }
            else
                $this->Flash->error(__('Failed to add dummy:') . $courseGroup->title);
        }
        return $this->redirect(['action' => 'index']);
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
