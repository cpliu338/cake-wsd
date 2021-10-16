<?php
declare(strict_types=1);

namespace App\Controller\Clerical;
use App\Controller\AppController;
use App\Model\Entity\CourseGroup;
use Cake\Event\EventInterface;
use App\Utils\PostsUtils;

/**
 * CourseGroups Controller
 *
 * @property \App\Model\Table\CourseGroupsTable $CourseGroups
 * @method \App\Model\Entity\CourseGroup[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CourseGroupsController extends AppController
{
    public function beforeFilter(EventInterface $event) {
        $identity = $this->Authentication->getIdentity();
        $user= $identity['staffid'];
        $posts = array_map(function($p) {return $p['title'];}, $identity['posts']);
        $this->set(compact('user', 'posts'));
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