<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ApplicationForm Entity
 *
 * @property int $id
 * @property int $user_id
 * @property int $course_group_id
 * @property string|null $choice
 * @property bool $accept_other
 * @property string|null $attachment
 * @property string|null $safety_expired_day
 * @property string $status
 * @property int $status_priority
 * @property int $recommendation_priority
 * @property int $approval_priority
 * @property int $nomination_priority
 * @property \Cake\I18n\FrozenTime $modified_at
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\CourseGroup $course_group
 */
class ApplicationForm extends Entity
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
        'user_id' => true,
        'course_group_id' => true,
        'choice' => true,
        'accept_other' => true,
        'attachment' => true,
        'safety_expired_day' => true,
        'status' => true,
        'status_priority' => true,
        'recommendation_priority' => true,
        'approval_priority' => true,
        'nomination_priority' => true,
        'modified_at' => true,
        'user' => true,
        'course_group' => true,
    ];

}
