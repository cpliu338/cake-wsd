<?php
declare(strict_types=1);

namespace App\Utils;

use Cake\ORM\Locator\TableLocator;
use Cake\I18n\FrozenTime;
use Cake\I18n\FrozenDate;
use Cake\Log\Log;
use Cake\Core\Configure;
use Cake\ORM\Query;
use Cake\Collection\Collection;
use App\Model\Entity\CourseGroup;

class CoursesUtils {

    use \Cake\Datasource\ModelAwareTrait;

    /* table objects 
    var $CourseGroups;
    var $CourseInstances;
    var $Users;
    var $ApplicationForms;

    public function __construct(TableLocator $tableLocator) {
        */
    public function __construct() {
        $this->loadModel('CourseGroups');
        $this->loadModel('CourseInstances');
        $this->loadModel('Users');
        $this->loadModel('ApplicationForms'); 
    }
    
    /**
     * Get a unique array of user ids to invite
     * @param mixed $invitees if an array, assume format same as PostsUtils::findSubordinates(),
     * if Query, extract to user ids
     * @return 
     */
    private function _cleanse ($invitees) {
        if (is_array($invitees)) {
            /*
            if (empty($invitees))
                return [];
            else
                $invitees = $posts_util->findSubordinates($posts, 'recommending');
            */
            return array_unique(array_map(function ($post) {return $post['user_id'];}, $invitees));
        }
        if ($invitees instanceof Query) {
            switch ($invitees->getRepository()->getAlias()) {
                case 'Users':
                    return $invitees->distinct('Users.id')->extract('id')->toArray();
                case 'Posts';
                    return $invitees->contain(['Users'])->distinct('Users.id')->extract('user.id')->toArray();
            }
        }
    }

    /**
     * Invite course, generate application forms, check instances, do not invite if none
     * @param int $cgid the course group id to invite
     * @param mixed $users to invite
     * @return array of id for application forms created
     */
    public function invite(int $cgid, /*Query|array*/ $invitees) : array {
        $result = [];
        $values_array = [
            'course_group_id' => $cgid,
            'choice' => '',
            'accept_other' => false,
            'status_priority' => 0,
            'recommendation_priority' => 0,
            'approval_priority' => 0,
            'nominaation_priority' => 0,
        ];
        $users = $this->_cleanse($invitees);
        foreach ($users as $user) {
            if (is_array($users))
                $values_array['user_id'] = $user;
            else
                $values_array['user_id'] = $user->id;
            $entity = $this->ApplicationForms->newEntity($values_array);
            if ($this->ApplicationForms->save($entity))
                $result[] = $entity->id;
            else
                throw new \Exception(var_export($entity->getErrors(), true));
        }
        return $result;
    }

    /**
     * Invite course, generate application forms, check instances, do not invite if none
     * @param CourseGroup $cg the course group to invite
     * @param array $invited_ranks to ranks to invite
     * @return array of id for application forms created
     */
    public function inviteByRanks(CourseGroup $cg, array $invited_ranks) : array {
        $users = $this->Users->find('ranksWithPosts', ['ranks'=>implode(',',$invited_ranks)])
            ->where(['Users.tree_code LIKE'=>$tree[$cg->division]]);
        $result = $this->invite($cg->id, $users);
        return $result;
    }

    public function addDummyInstance(CourseGroup $cg) {
        //$cg = $this->CourseGroups->get($cg);
        $nom_close = new FrozenTime($cg->nomination_close_at);
        $dummy_course_array = [
            'course_group_id' => $cg->id,
            'code' => $this->nextCourseCode($cg->division),
            'start_at' => $nom_close->hour(9)->addDays(10),
            'end_at' => $nom_close->hour(17)->addDays(10),
            'venue' => 'TBA',
            'remark' => ''
        ];//return $nom_close;
        $n_instances = $this->CourseInstances->find()->where(['course_group_id'=>$cg->id])->count();
        $ci = $this->CourseInstances->newEntity($dummy_course_array);
        $ci->start_at = $nom_close->hour(9)->addDays(10+$n_instances);
        $ci->end_at =  $nom_close->hour(17)->addDays(10);
        return $ci;
    }

    /**
     * Find the next available course code MEM23456 ...
     * @param $division the division: MEM | MEP | DES
     * @return the next CourseCode
     */
    public function nextCourseCode($division) : string {
        $last_code_num = 1;
        foreach ($this->CourseInstances->find()->where(['code LIKE'=> $division . '%'])->order('code desc')
                as $ci) {
            $n = substr($ci->code, strlen($division));
            if (preg_match('/^[0-9]+$/', $n)) {
                $last_code_num = $n+1;
                break;
            }
        }
        return sprintf("%s%05d", $division, $last_code_num);
    }

    public function DummyCourse(string $division) {
        $now = FrozenTime::now();
        $todayAt17 = (new FrozenTime())->hour(17)->minute(0)->second(0);        
        $dummy_course_array = [
            'title' => sprintf('Dummy course at %s', $now->i18nFormat()),
            'application_close_at' => $todayAt17->addDays(10)->i18nFormat(),
            'recommendation_close_at' => $todayAt17->addDays(11)->i18nFormat(),
            'approval_close_at' => $todayAt17->addDays(12)->i18nFormat(), 
            'nomination_close_at' => $todayAt17->addDays(13)->i18nFormat(),
            'remark' => 'TBA',
            'invited' => false,
            'invited_ranks' => json_encode([$division => []]),
            'nominated' => false,
            'confirmed' => false,
            'attachments' => 0,
            'require_attachment' => 1,
            'division' => $division,
            'type' => 0,
        ];
        return $this->CourseGroups->newEntity($dummy_course_array);
    }

}