<?php
declare(strict_types=1);

namespace App\Utils;

use Cake\ORM\Locator\TableLocator;
use Cake\I18n\FrozenTime;
use Cake\I18n\FrozenDate;
use App\Model\Entity\CourseGroup;

class CourseUtils {

    /* table objects */
    var $CourseGroups;
    var $CourseInstances;

    public function __construct(TableLocator $tableLocator) {
        $this->CourseGroups = $tableLocator->get('CourseGroups');
        $this->CourseInstances = $tableLocator->get('CourseInstances');
    }

    /**
     * Invite course, generate application forms, check instances, do not invite if none
     * @param CourseGroup $cg the course group to invite
     * @param array $invited_ranks to ranks to invite
     */
    public function invite(CourseGroup $cg, array $invited_ranks) : int {
        // 
        $num = $this->CourseInstances->find()->where(['course_group_id'=>$cg->id])->count();
        if (!$num)
            return $num;
        return $num;
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
        $ci = $this->CourseInstances->newEntity($dummy_course_array);
        $ci->start_at = $nom_close->hour(9)->addDays(10);
        $ci->end_at =  $nom_close->hour(17)->addDays(10);
        return $ci;
    }

    public function nextCourseCode($division) {
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
        // this switch block to be moved to housekeep command invite.
        switch ($division) {
            case 'MEM': 
                $invited_ranks = [$division => ['SARTI','WM1M']]; 
                break; // qty 1, 2
            case 'MEP':
                $invited_ranks = [$division => ['STOI','WS1E']]; 
                break; // qty 3, 4
            default:
                $invited_ranks = [$division => []]; 
        }
        
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