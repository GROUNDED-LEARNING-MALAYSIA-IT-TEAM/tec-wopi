<?php
namespace EaglenavigatorSystem\Wopi\Model\Entity;

use Cake\ORM\Entity;

/**
 * WopiFile Entity
 *
 * @property int $id
 * @property string $file_uuid
 * @property string $file_extension
 * @property int $user_id
 * @property string|resource $file_data
 * @property string $file_path
 * @property \Cake\I18n\FrozenTime $created_at
 * @property \Cake\I18n\FrozenTime $updated_at
 *
 * @property \EaglenavigatorSystem\Wopi\Model\Entity\User $user
 */
class WopiFile extends Entity
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
        'file_uuid' => true,
        'file_extension' => true,
        'user_id' => true,
        'file_data' => true,
        'file_path' => true,
        'created_at' => true,
        'updated_at' => true,
        'user' => true,
    ];
}
