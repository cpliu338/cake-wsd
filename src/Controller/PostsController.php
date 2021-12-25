<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Posts Controller
 *
 * @property \App\Model\Table\PostsTable $Posts
 * @method \App\Model\Entity\Post[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PostsController extends AppController
{
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Authentication->allowUnauthenticated(['upload', 'confirm']);
    }

    public function confirm() {
        return $this->response->withStringBody(json_encode(
            $this->request->getData('rows')));
    }

    public function upload() {
        $columns = ['title', 'division'];
        $post = $this->Posts->newEmptyEntity();
        if ($this->request->is('post')) {
            /*{"type":"text/csv","size":2573,"tmpName":"/tmp/phptiKNXv","error":0} */
            $file = $this->request->getData('file');
            $status = [];
            if ($file) {
                $status = array_merge($status, [
                'type' => $file->getClientMediaType(),
                'size' => $file->getSize(),
                'tmpName' => $file->getStream()->getMetadata('uri'),
                'error' => $file->getError(),
                ]);
            }
            $rows = [];
            if (($handle = fopen($file->getStream()->getMetadata('uri'), "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 5000, ",")) !== FALSE) {
                    array_push($rows, $data);
                }
                fclose($handle);
                //$status['rows'] = $rows;
            }
            else {
                $status['error'] = 'no handle';
            }
            array_shift($rows); //discard header
            $start_serial = ($this->request->getData('serial') ?? 0) + 1;
            $this->set(compact('status', 'rows', 'start_serial'));
            $this->render('upload_result');
            //$this->viewBuilder()->setOption('serialize', ['status', 'rows']);
            //return $this->response->withStringBody(json_encode($status, JSON_UNESCAPED_SLASHES));
        }
        $this->set(compact('columns', 'post'));
    }
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Users'],
        ];
        $posts = $this->paginate($this->Posts);

        $this->set(compact('posts'));
    }

    /**
     * View method
     *
     * @param string|null $id Post id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $post = $this->Posts->get($id, [
            'contain' => ['Users', 'RequiredCourses'],
        ]);

        $this->set(compact('post'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $post = $this->Posts->newEmptyEntity();
        if ($this->request->is('post')) {
            $post = $this->Posts->patchEntity($post, $this->request->getData());
            if ($this->Posts->save($post)) {
                $this->Flash->success(__('The post has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The post could not be saved. Please, try again.'));
        }
        $users = $this->Posts->Users->find('list', ['limit' => 200]);
        $requiredCourses = $this->Posts->RequiredCourses->find('list', ['limit' => 200]);
        $this->set(compact('post', 'users', 'requiredCourses'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Post id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $post = $this->Posts->get($id, [
            'contain' => ['RequiredCourses'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $post = $this->Posts->patchEntity($post, $this->request->getData());
            if ($this->Posts->save($post)) {
                $this->Flash->success(__('The post has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The post could not be saved. Please, try again.'));
        }
        $users = $this->Posts->Users->find('list', ['limit' => 200]);
        $requiredCourses = $this->Posts->RequiredCourses->find('list', ['limit' => 200]);
        $this->set(compact('post', 'users', 'requiredCourses'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Post id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $post = $this->Posts->get($id);
        if ($this->Posts->delete($post)) {
            $this->Flash->success(__('The post has been deleted.'));
        } else {
            $this->Flash->error(__('The post could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
