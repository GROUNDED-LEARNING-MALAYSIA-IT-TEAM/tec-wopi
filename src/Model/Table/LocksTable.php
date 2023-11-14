<?php

namespace EaglenavigatorSystem\Wopi\Model\Table;

use Cake\I18n\FrozenTime;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use EaglenavigatorSystem\Wopi\Exception\LockOperationFailedException;
use EaglenavigatorSystem\Wopi\Exception\LockMismatchException;
use Exception;
use Cake\Log\LogTrait;

/**
 * Locks Model
 *
 * @property &\Cake\ORM\Association\BelongsTo $WopiFiles
 * @property \EaglenavigatorSystem\Wopi\Model\Table\LocksTable&\Cake\ORM\Association\BelongsTo $Locks
 * @property &\Cake\ORM\Association\BelongsTo $LockedByUsers
 * @property \EaglenavigatorSystem\Wopi\Model\Table\LocksTable&\Cake\ORM\Association\HasMany $Locks
 *
 * @method \EaglenavigatorSystem\Wopi\Model\Entity\Lock get($primaryKey, $options = [])
 * @method \EaglenavigatorSystem\Wopi\Model\Entity\Lock newEntity($data = null, array $options = [])
 * @method \EaglenavigatorSystem\Wopi\Model\Entity\Lock[] newEntities(array $data, array $options = [])
 * @method \EaglenavigatorSystem\Wopi\Model\Entity\Lock|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \EaglenavigatorSystem\Wopi\Model\Entity\Lock saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \EaglenavigatorSystem\Wopi\Model\Entity\Lock patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \EaglenavigatorSystem\Wopi\Model\Entity\Lock[] patchEntities($entities, array $data, array $options = [])
 * @method \EaglenavigatorSystem\Wopi\Model\Entity\Lock findOrCreate($search, callable $callback = null, $options = [])
 */
class LocksTable extends Table
{
    use LogTrait;
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('locks');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo(
            'WopiFiles',
            [
                'foreignKey' => 'file_id',
                'joinType' => 'INNER',
                'className' => 'EaglenavigatorSystem/Wopi.WopiFiles',
                //on delete cascade
                'dependent' => true,
            ]
        );
        $this->belongsTo('UserManagements', [
            'foreignKey' => 'locked_by_user_id',
            'joinType' => 'INNER',
            'className' => 'UserManagements',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->dateTime('expiration_time')
            ->requirePresence('expiration_time', 'create')
            ->notEmptyDateTime('expiration_time');

        $validator
            ->dateTime('created_at')
            ->requirePresence('created_at', 'create')
            ->notEmptyDateTime('created_at');

        $validator
            ->dateTime('updated_at')
            ->requirePresence('updated_at', 'create')
            ->notEmptyDateTime('updated_at');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['file_id'], 'WopiFiles'));
        $rules->add($rules->existsIn(['lock_id'], 'Locks'));
        $rules->add($rules->existsIn(['locked_by_user_id'], 'UserManagements'));

        //lock id must be unique per file
        $rules->add(
            //on create

            function ($entity, $options) {
                $lockId = $entity->lock_id;
                $fileId = $entity->file_id;

                $lock = $this->find()
                    ->where(['lock_id' => $lockId, 'file_id' => $fileId])
                    ->first();

                if (!empty($lock)) {
                    throw new LockOperationFailedException('Lock id already exists for this file');
                }

                return true;
            },
            'uniqueLockId',
            [
                'errorField' => 'lock_id',
                'message' => 'Lock id already exists for this file',
                //only on create
                'on' => 'create',
            ]
        );


        return $rules;
    }

    private function generateLockId(): string
    {
        //lock limit varchar 1024
        $lockId =  substr(md5(uniqid(rand(), true)), 0, 1024);

        while ($this->lockIdExists($lockId)) {
            $lockId = substr(md5(uniqid(rand(), true)), 0, 1024);
        }

        return $lockId;
    }

    private function lockIdExists(string $lockId): bool
    {
        $lock = $this->find()
            ->where(['lock_id' => $lockId])
            ->first();

        return !empty($lock);
    }


    private function generateExpirationDate(): FrozenTime
    {
        $expires = date("Y-m-d H:i:s", strtotime('+1 minute')); // Set expiration time to one minute from now

        return new FrozenTime($expires);
    }

    private function expirationDateAlreadyExpired(FrozenTime $expirationDate): bool
    {
        return $expirationDate < new FrozenTime();
    }

    //this handle logic lock, unlock , refresh lock for a wopi file.
    /**
     * Attempts to lock a file.
     *
     * @param int $fileId The ID of the file to lock.
     * @param int $userId The ID of the user attempting to lock the file.
     * @param string $lockId The lock ID.
     * @param \Cake\I18n\FrozenTime $expiry The expiration time of the lock.
     * @return bool True on success, false on failure.
     */
    public function lockFile(int $fileId, int $userId)
    {
        try {

            $lock = $this->find()
                ->where(['file_id' => $fileId])
                ->first();




            // If lock exists and is not expired, check if it's owned by the same user
            if ($lock && !$this->expirationDateAlreadyExpired($lock->expiration_time)) {
                if ($lock->locked_by_user_id === $userId) {
                    return true; // Lock is already held by this user
                }
                return false; // File is locked by another user
            }

            // Create or update the lock
            $data = [
                'file_id' => $fileId,
                'locked' => true,
                'lock_id' => $this->generateLockId(),
                'locked_by_user_id' => $userId,
                'expiration_time' => $this->generateExpirationDate(),
            ];

            $lock = $this->newEntity($data);

            if ($this->save($lock)) {
                return true;
            }

            return false;
        } catch (Exception $e) {

            $this->log($e->getMessage(), 'error');

            throw new LockOperationFailedException('Lock operation failed ' . $e->getMessage());
        }
    }

    /**
     * Unlocks a file.
     *
     * @param int $fileId The ID of the file to unlock.
     * @param int $userId The ID of the user attempting to unlock the file.
     * @param string $lockId The lock ID.
     * @return bool True on success, false on failure.
     */
    public function unlockFile(int $fileId, int $userId, string $lockId)
    {
        try {
            $lock = $this->find()
                ->where(['file_id' => $fileId])
                ->first();

            if ($lock->lock_id !== $lockId) {

                $this->log('lock id mismatch', 'error');

                throw new LockMismatchException('Lock id mismatch');
            }

            if ($lock && $lock->locked_by_user_id === $userId && $lock->locked == true) {

                //unlock file
                $lock->locked = false;

                if ($this->save($lock)) {
                    return true;
                }
            }

            return false;
        } catch (Exception $e) {
            $this->log($e->getMessage(), 'error');

            throw new LockOperationFailedException('Lock operation failed ' . $e->getMessage());
        }
    }


    /**
     * Refreshes a file lock.
     *
     * @param int $fileId The ID of the file.
     * @param int $userId The ID of the user.
     * @param string $lockId The lock ID.
     * @return bool True on success, false on failure.
     */
    public function refreshLock(int $fileId, int $userId, string $lockId)
    {
        try {


            $lock = $this->find()
                ->where(['file_id' => $fileId, 'lock_id' => $lockId])
                ->first();

            if (!$lock || $lock->locked_by_user_id !== $userId) {
                return false; // Lock not found or is held by another user
            }

            $lock->expiration_time = $this->generateExpirationDate();

            if ($this->save($lock)) {
                return true;
            }

            return false;
        } catch (\Throwable $e) {

            $this->log($e->getMessage(), 'error');

            throw new LockOperationFailedException('Lock operation failed ' . $e->getMessage());
        }
    }

    /**
     * Deletes a file lock.
     *
     * @param int $fileId The ID of the file.
     * @return bool True on success, false on failure.
     */
    public function deleteFileLock(int $fileId)
    {
        try {

            $lock = $this->find()
                ->where(['file_id' => $fileId])
                ->first();

            if (!$lock) {
                return false; // No lock found
            }

            if ($this->delete($lock)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            $this->log($e->getMessage(), 'error');

            throw new LockOperationFailedException('Lock operation failed ' . $e->getMessage());
        }
    }
}
