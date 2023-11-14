<?php
namespace EaglenavigatorSystem\Wopi\Model\Entity;

use Cake\ORM\Entity;

/**
 * Lock Entity
 *
 * @property int $id
 * @property int $file_id
 * @property bool $locked
 * @property string $lock_id
 * @property int $locked_by_user_id
 * @property \Cake\I18n\FrozenTime $expiration_time
 * @property \Cake\I18n\FrozenTime $created_at
 * @property \Cake\I18n\FrozenTime $updated_at
 *
 * @property \EaglenavigatorSystem\Wopi\Model\Entity\WopiFile $file
 * @property \EaglenavigatorSystem\Wopi\Model\Entity\Lock[] $locks
 * @property \App\Model\Entity\UserManagement $user_management
 */
class Lock extends Entity
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
        'file_id' => true,
        'locked' => true,
        'lock_id' => true,
        'locked_by_user_id' => true,
        'expiration_time' => true,
        'created_at' => true,
        'updated_at' => true,
        'file' => true,
        'locks' => true,
        'user_management' => true,
    ];
}
