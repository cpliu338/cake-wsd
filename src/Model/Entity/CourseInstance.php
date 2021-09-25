<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CourseInstance Entity
 *
 * @property int $id
 * @property int $course_group_id
 * @property string $code
 * @property string $start_at
 * @property string $end_at
 * @property string|null $venue
 * @property string|null $remark
 *
 * @property \App\Model\Entity\CourseGroup $course_group
 */
class CourseInstance extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'course_group_id' => true,
        'code' => true,
        'start_at' => true,
        'end_at' => true,
        'venue' => true,
        'remark' => true,
        'course_group' => true,
    ];

    protected function _setStartAt($start_at) {
        if ($start_at instanceof FrozenTime)
            return $start_at->i18nFormat();
        return $start_at;
    }

    protected function _setEndAt($end_at) {
        if ($end_at instanceof FrozenTime)
            return $end_at->i18nFormat();
        return $end_at;
    }
}
