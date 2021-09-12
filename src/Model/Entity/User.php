<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * User Entity
 *
 * @property int $id
 * @property int $staffid
 * @property string $name
 * @property string $name_chi
 * @property string $notesmail
 * @property string $rank
 * @property string $rank_code
 * @property string $tree_code
 * @property string $password
 * @property string|null $telephone
 * @property int|null $mobile
 * @property string $id_prefix
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Group $group
 */
class User extends Entity
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
        'name_chi' => true,
        'notesmail' => true,
        'rank' => true,
        'rank_code' => true,
        'tree_code' => true,
        'password' => true,
        'telephone' => true,
        'mobile' => true,
        'id_prefix' => true,
        'modified' => true,
        'group' => true,
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array
     */
    protected $_hidden = [
        'password',
    ];
}
