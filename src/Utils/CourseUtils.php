<?php
declare(strict_types=1);

namespace App\Utils;

use Cake\ORM\Locator\TableLocator;
use Cake\I18n\FrozenTime;
use Cake\I18n\FrozenDate;
use Cake\Log\Log;
use App\Model\Entity\CourseGroup;

class CourseUtils {

    /* table objects */
    var $CourseGroups;
    var $CourseInstances;
    var $Users;
    var $ApplicationForms;

    public function __construct(TableLocator $tableLocator) {
        $this->CourseGroups = $tableLocator->get('CourseGroups');
        $this->CourseInstances = $tableLocator->get('CourseInstances');
        $this->Users = $tableLocator->get('Users');
        $this->ApplicationForms = $tableLocator->get('ApplicationForms'); 
    }

    /**
     * Invite course, generate application forms, check instances, do not invite if none
     * @param CourseGroup $cg the course group to invite
     * @param array $invited_ranks to ranks to invite
     * @return number of application forms created
     */
    public function invite(CourseGroup $cg, array $invited_ranks) {//}: int {
        // the following to be moved to app.php
        $tree = ['MEM'=>'FB%', 'MEP'=>'FC%'];
        // ===================
        $num = $this->CourseInstances->find()->where(['course_group_id'=>$cg->id])->count();
        if (!$num)
            return $num;
        $values_array = [
            'course_group_id' => $cg->id,
            'choice' => '',
            'accept_other' => false,
            'status_priority' => 0,
            //'status' => 'Fresh',
            'recommendation_priority' => 0,
            'approval_priority' => 0,
            'nominaation_priority' => 0,
        ];
        $users = $this->Users->find('ranksWithPosts', ['ranks'=>implode(',',$invited_ranks)])
            ->where(['Users.tree_code LIKE'=>$tree[$cg->division]]);
        $result = 0;
        foreach ($users as $user) {
            $values_array['user_id'] = $user->id;
            $entity = $this->ApplicationForms->newEntity($values_array);
            Log::write('error', "R" . $result);
            if ($this->ApplicationForms->save($entity))
                $result ++;
            else
                Log::write('error', var_export($entity->getErrors(), true));
        }
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