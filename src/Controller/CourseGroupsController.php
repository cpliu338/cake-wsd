<?php
declare(strict_types=1);

namespace App\Controller;
use ChunkedFileUpload\Handler\UploadTrait;

/**
 * CourseGroups Controller
 *
 * @property \App\Model\Table\CourseGroupsTable $CourseGroups
 * @method \App\Model\Entity\CourseGroup[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CourseGroupsController extends AppController
{
    use UploadTrait;
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $courseGroups = $this->paginate($this->CourseGroups);

        $courseGroup = $this->CourseGroups->newEntity(['title' =>"select Task.time_id time_id,
        Task.task_time_id from sm_task TASK left join dl_work on task.id=dl_work.dl_id where ",
        'attachments'=>'/select(.+)from(.+)(where.+)/is'
        ]);

        $this->set(compact('courseGroups', 'courseGroup'));

    }

    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        //$this->Authentication->allowUnauthenticated(['uploadComplete']);
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
        if ($this->request->is('post')) {
        /* "select Task.time_id time_id,
        Task.task_time_id from sm_task TASK left join dl_work on task.id=dl_work.dl_id where "
         */
            $url = $this->request->getData('title');
            // select_pattern /select(.+)from(.+)(where.+)/is
            $dt_column = $this->request->getData('attachments');
            $full_pattern = '/select(.+)from(.+)(where.+)/is';
            $short_pattern = '/select(.+)from(.+)/is';
            if (preg_match($full_pattern, $url, $matches)) {
                $result['fields'] = $this->parseFields($matches[1]);
                $result['tables'] = $matches[2];
                $result['criteria'] = $this->parseCriteria(substr($matches[3],5), $dt_column, $result['fields']);
            }
            else if (preg_match($short_pattern, $url, $matches)) {
                $result['fields'] = $this->parseFields($matches[1]);
                $result['tables'] = $matches[2];
                $result['criteria'] = '';
            }
            $courseGroup = var_export($result, true);

        }
        else {
            $courseGroup = $this->CourseGroups->get($id, [
                'contain' => ['ApplicationForms', 'CourseInstances'],
            ]);
        }
        
        $this->set(compact('courseGroup'));
        $this->viewBuilder()->setOption('serialize', ['courseGroup']);
    }

    private function parseCriteria($raw, $dt_column, $fields) {
        $crit = trim($raw);
        $col = strtoupper(trim($dt_column));
        if (empty($crit))
            return '';
        if (in_array($col, $fields)) {
            $now = new \Cake\I18n\FrozenDate("now");
            return sprintf("WHERE (%s) AND %s < TO_DATE('%s', 'yyyy-mm-dd')", $raw, $col,
            $now->subYears(3)->i18nFormat('yyyy-MM-dd'));
            
        }
        else 
            return "WHERE $raw";
    }

    private function parseFields($fields) {
        $parsed = array_map(function ($token) {
            $field = preg_split('/\s+/', trim($token));
            switch (count($field)) {
                case 2: return strtoupper(trim($field[1]));
                default: return strtoupper(trim($token));
            }
        }, preg_split('/\s*,\s*/', trim($fields)));
        return $parsed;
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
