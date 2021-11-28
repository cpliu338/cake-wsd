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

        $this->garbageCollectTmpfolders(TMP);
        $this->set(compact('courseGroups'));

    }

    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        //$this->Authentication->allowUnauthenticated(['uploadComplete']);
    }

    public function uploadComplete() {
        // Do things like persist an entity related to the upload
        //$upload_dir = TMP . $this->request->getData('tmpfolder');
        $body = [];
        //$body['upload_dir'] = $upload_dir;
        $result = $this->moveUploadedFile(TMP, '/var/www/html/uploads', 'file123');
        if (array_key_exists('exception', $result)) {
            $body['msg'] = var_export($result['exception'], true);
        }
        else {
            $body = array_merge($body, $result);
            $body['msg'] = sprintf("Uploaded %s, size %s", $body['name'], $body['size']);
            $body['redirect'] = \Cake\Routing\Router::url(['action'=>'index', 'controller'=>'CourseGroups', 'prefix'=>'Clerical'], true);
        }
        return $this->response->withStatus(200)->withType('application/json')
            ->withStringBody(json_encode($body, JSON_UNESCAPED_SLASHES));
    }

    public function upload() {
        //$upload_base = TMP;
        //$upload_dir = $upload_base . $this->request->getData('tmpfolder');
        // . $this->request->getData('tmpfolder') ?? $this->request->getSession()->id();
        $result = $this->handleUpload([
            //'upload_dir' => $upload_dir . DS,
            'upload_base' => TMP,
            'accept_file_types' => '/\.(pdf|jpe?g|png)$/i',
        ]);
/*        $result['tmpfolder'] = $this->request->getData('tmpfolder');
        $result['upload_name'] = $result['upload']['name'];*/
        return $this->response
            //->withType('application/json') done in $this->handleUpload
            ->withStringBody(json_encode($result, JSON_UNESCAPED_SLASHES));
        //return $response;
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
