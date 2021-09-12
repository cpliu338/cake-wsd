<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Directory Entity
 *
 * @property int $id
 * @property int $staffid
 * @property string|null $name
 * @property string|null $rank_code
 * @property string|null $post_name
 * @property string|null $tree_code
 * @property string|null $phone
 * @property string|null $notesemail
 * @property string|null $dposting_type
 */
class Directory extends Entity
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
        'staffid' => true,
        'name' => true,
        'rank_code' => true,
        'post_name' => true,
        'tree_code' => true,
        'phone' => true,
        'notesemail' => true,
        'dposting_type' => true,
    ];
}
