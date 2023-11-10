<?php
namespace EaglenavigatorSystem\Wopi\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * WopiFiles Model
 *
 * @property \EaglenavigatorSystem\Wopi\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \EaglenavigatorSystem\Wopi\Model\Entity\WopiFile get($primaryKey, $options = [])
 * @method \EaglenavigatorSystem\Wopi\Model\Entity\WopiFile newEntity($data = null, array $options = [])
 * @method \EaglenavigatorSystem\Wopi\Model\Entity\WopiFile[] newEntities(array $data, array $options = [])
 * @method \EaglenavigatorSystem\Wopi\Model\Entity\WopiFile|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \EaglenavigatorSystem\Wopi\Model\Entity\WopiFile saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \EaglenavigatorSystem\Wopi\Model\Entity\WopiFile patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \EaglenavigatorSystem\Wopi\Model\Entity\WopiFile[] patchEntities($entities, array $data, array $options = [])
 * @method \EaglenavigatorSystem\Wopi\Model\Entity\WopiFile findOrCreate($search, callable $callback = null, $options = [])
 */
class WopiFilesTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('wopi_files');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
            'className' => 'EaglenavigatorSystem/Wopi.Users',
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
            ->scalar('file_uuid')
            ->maxLength('file_uuid', 36)
            ->requirePresence('file_uuid', 'create')
            ->notEmptyFile('file_uuid');

        $validator
            ->scalar('file_extension')
            ->maxLength('file_extension', 10)
            ->requirePresence('file_extension', 'create')
            ->notEmptyFile('file_extension');

        $validator
            ->requirePresence('file_data', 'create')
            ->notEmptyFile('file_data');

        $validator
            ->scalar('file_path')
            ->maxLength('file_path', 255)
            ->requirePresence('file_path', 'create')
            ->notEmptyFile('file_path');

        $validator
            ->dateTime('created_at')
            ->notEmptyDateTime('created_at');

        $validator
            ->dateTime('updated_at')
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
        $rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }
}
