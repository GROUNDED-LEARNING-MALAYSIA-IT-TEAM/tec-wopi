<?php

namespace EaglenavigatorSystem\Wopi\Model\Entity;

use App\Model\Entity\User;
use App\Model\Entity\UserManagement;
use Cake\I18n\FrozenTime;
use Cake\ORM\Entity;

/**
 * WopiFile Entity
 *
 * @property int $id
 * @property string $file_uuid
  * @property string $file_name
 * @property int $file_size
 * @property string $version
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
    public const MAX_NAME_LENGTH = 255;
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
        'file_name' => true,
        'file_size' => true,
        'version' => true,
        'file_extension' => true,
        'user_id' => true,
        'file_path' => true,

        'version' => true,
        'created_at' => true,
        'updated_at' => true,
        'user' => true,
    ];

    protected $_hidden = [
        'file_data'
    ];

    //virtual field
    protected $_virtual = [
        'may_write', //indicates if the user can write to the file
        'read_only', //indicates if the file is read only
    ];

    public function getName()
    {
        return $this->file_name;

    }

    public function getOwner()
    {
        return $this->user;

    }

    public function getVersion()
    {
        return $this->version;

    }

    public function getSize()
    {
        return $this->getFileSizeForWopi();
    }

    private function getFileSizeForWopi(): int
    {
        return $this->file_size;
    }

    /**
     * Get file size in human readable format
     *
     * @return string
     */
    private function convertKbToHumanReadableFilesize(): string
    {
        $size = $this->file_size;
        $unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
        return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];

    }

    public function getFileSizeHumanize()
    {
        return $this->convertKbToHumanReadableFilesize();
    }

    public function setMayWrite(UserManagement $user)
    {

        $this->may_write = $this->_getMayWrite($user);
        return $this;
    }

    public function mayWrite(): bool
    {
        return $this->may_write;
    }

    public function readOnly(): bool
    {
        return $this->read_only;
    }

    public function getModifiedTImeFormatC()
    {
        return $this->updated_at;

    }


    public function isReadOnly(): bool
    {
        return $this->read_only;
    }
    /**
     * Get may_write
     *
     * @return bool
     */
    protected function _getMayWrite(UserManagement $user): bool
    {
        if ($user->id == $this->user_id) {
            $this->read_only = false;
            return true;
        } elseif($user->is_superuser) {
            $this->read_only = false;
            return true;
        } else {
            $this->read_only = true;
            return false;
        }

    }





}
