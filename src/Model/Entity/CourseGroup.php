<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CourseGroup Entity
 *
 * @property int $id
 * @property string $title
 * @property string $application_close_at
 * @property string $recommendation_close_at
 * @property string $approval_close_at
 * @property string $nomination_close_at
 * @property string|null $remark
 * @property bool $invited
 * @property string $invited_ranks
 * @property bool $nominated
 * @property bool $confirmed
 * @property string|null $attachments
 * @property int $require_attachment
 * @property string $division
 * @property int $type
 *
 * @property \App\Model\Entity\ApplicationForm[] $application_forms
 * @property \App\Model\Entity\CourseInstance[] $course_instances
 */
class CourseGroup extends Entity
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
        'title' => true,
        'application_close_at' => true,
        'recommendation_close_at' => true,
        'approval_close_at' => true,
        'nomination_close_at' => true,
        'remark' => true,
        'invited' => true,
        'invited_ranks' => true,
        'nominated' => true,
        'confirmed' => true,
        'attachments' => true,
        'require_attachment' => true,
        'division' => true,
        'type' => true,
        'application_forms' => true,
        'course_instances' => true,
    ];
}
