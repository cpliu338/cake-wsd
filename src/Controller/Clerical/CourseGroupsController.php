<?php
declare(strict_types=1);

namespace App\Controller\Clerical;
use App\Controller\AppController;
use App\Model\Entity\CourseGroup;
use Cake\Event\EventInterface;
use App\Utils\PostsUtils;
use App\Utils\UploadHandler;

/**
 * CourseGroups Controller
 *
 * @property \App\Model\Table\CourseGroupsTable $CourseGroups
 * @method \App\Model\Entity\CourseGroup[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CourseGroupsController extends AppController
{

    public function initialize(): void
    {
        parent::initialize();
    }
    
    public function beforeFilter(EventInterface $event) {
        parent::beforeFilter($event);
        if (!empty($this->identity)) {
            $user= $this->identity['staffid'];
            $posts = array_map(function($p) {return $p['title'];}, $this->identity['posts'] ?? []);
            $this->set(compact('user', 'posts'));
            if (!in_array($user, [3178])) {
                if (!in_array($this->request->getParam('action'), ['index'])) {
                    $this->render('/Pages/forbidden');
                    return;
                }
            }
        }
    }

    public function upload() {
        $upload_handler = new UploadHandler(['upload_dir' => '/var/www/html/uploads/',
            'accept_file_types' => '/\.(pdf|jpe?g|png)$/i',
            'user_specified_name' => 'course_details-001'
        ]);
        $result = $upload_handler->handle();
        //$result['error'] = 0;
        $response = $this->response->withType('application/json')
            ->withStringBody(json_encode($result, JSON_UNESCAPED_SLASHES));
        return $response;
    }

    public function edit() {
        /*
        $token = new \Alt3\CakeTokens\RandomBytesToken();
        $token->setCategory('password-reset');
        $token->setLifetime('+1 week');
      
        // save the token object
        $table = \Cake\Orm\TableRegistry::get('Alt3/CakeTokens.Tokens');
        $entity = $table->newEntity($token->toArray());
      
        if ($table->save($entity)) {
          $this->Flash->success('Successfully saved token with id ' . $entity->id);
        }
        */
        return;
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $courseGroups = $this->paginate($this->CourseGroups);
        $this->set(compact('courseGroups'));
        //debug($this->viewBuilder()->getVar('posts'));
    }

    public function countApplications($type, $user_id) {
        $util = new PostsUtils();
        $subs = $util->findSubordinates($this->viewBuilder()->getVar('posts'), $type); 
        $result = ['count'=>count($subs)];
        $this->set(compact('result'));
        $this->viewBuilder()->setOption('serialize', ['result']);
    }

}