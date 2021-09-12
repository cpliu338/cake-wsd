<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Post Entity
 *
 * @property int $id
 * @property string $title
 * @property string $post_name
 * @property string $tree_code
 * @property int $user_id
 * @property string $division
 * @property string $unit
 * @property string $recommending_posts
 * @property string $approving_posts
 * @property int $level
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\RequiredCourse[] $required_courses
 */
class Post extends Entity
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
        'post_name' => true,
        'tree_code' => true,
        'user_id' => true,
        'division' => true,
        'unit' => true,
        'recommending_posts' => true,
        'approving_posts' => true,
        'level' => true,
        'user' => true,
        'required_courses' => true,
    ];
}
