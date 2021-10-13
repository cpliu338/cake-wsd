<?php
declare(strict_types=1);

namespace App\Controller\Clerical;
use App\Controller\AppController;
use App\Model\Entity\CourseGroup;

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
        $user = $this->Authentication->getIdentity();
        $user= $user['staffid'];
        $this->set(compact('courseGroups', 'user'));
    }

}