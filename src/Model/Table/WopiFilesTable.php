<?php

namespace EaglenavigatorSystem\Wopi\Model\Table;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\I18n\FrozenTime;
use Cake\ORM\Entity;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Utility\Security;
use Cake\Utility\Text;
use Cake\Validation\Validator;
use EaglenavigatorSystem\Wopi\Exception\FileHandingException;
use EaglenavigatorSystem\Wopi\Exception\FileUpdateContentIsEmptyException;
use EaglenavigatorSystem\Wopi\Model\Entity\WopiFile;

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

        $this->belongsTo('UserManagements', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
            'className' => 'UserManagements',
        ]);
        //each file can have only one lock at a time
        $this->hasOne(
            'Locks',
            [
                'foreignKey' => 'file_id',
                'className' => 'EaglenavigatorSystem/Wopi.Locks',
                'dependent' => true,
            ]
        );

        $this->HasMany(
            'PivotWopiFilesProjects',
            [
                'foreignKey' => 'wopi_file_id',
                'className' => 'EaglenavigatorSystem/Projects.PivotWopiFilesProjects',
                'dependent' => true,
            ]
        );
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     *
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

            $validator
            ->integer('parent_id')
            ->allowEmptyString('parent_id', null, 'create');

        $validator
            ->scalar('file_uuid')
            ->maxLength('file_uuid', 36)
            ->requirePresence('file_uuid', 'create')
            ->notEmptyFile('file_uuid');

        $validator
            ->scalar('file_name')
            ->maxLength('file_name', 255)
            ->requirePresence('file_name', 'create')
            ->notEmptyFile('file_name');

        $validator
            ->integer('file_size')
            ->requirePresence('file_size', 'create')
            ->notEmptyFile('file_size');

        $validator
            ->scalar('version')
            ->maxLength('version', 255)
            ->requirePresence('version', 'create')
            ->notEmptyFile('version');

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

            //user info column max 1024 ascii
            $validator
            ->scalar('user_info')
            ->maxLength('user_info', 1024)
            ->allowEmptyString('user_info');

            $validator
            ->boolean('soft_delete')
            ->allowEmptyFor('soft_delete', true);

            $validator
            ->dateTime('soft_delete_at')
            ->allowEmptyDateTime('soft_delete_at', true);

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
        $rules->add($rules->existsIn(['user_id'], 'UserManagements'));

        //parent id , id must exist in table
        $rules->add(

            function ($entity, $options) {
                $parent_id = $entity->parent_id;
                if ($parent_id) {
                    $wopiFile = $this->find('all', [
                        'conditions' => [
                            'id' => $parent_id
                        ]
                    ])->first();

                    if (!$wopiFile) {
                        return false;
                    }
                }

                return true;
            },
            'parentExists',
            [
                'errorField' => 'parent_id',
                'message' => 'Parent file does not exist',
            ]
        );

        //file must exist in path
        $rules->add(

            function ($entity, $options) {
                $file_path = $entity->file_path;

                dump('--check file existsd--' . $file_path);
                if (!file_exists($file_path)) {
                    return false;
                }

                return true;
            },
            'fileExists',
            [
                'errorField' => 'file_path',
                'message' => 'File does not exist in path',
            ]
        );

        return $rules;
    }


    public function getWopiFile($file_uuid)
    {
        $wopiFile = $this->find('all', [
            'conditions' => [
                'file_uuid' => $file_uuid
            ]
        ])->first();

        return $wopiFile;
    }

    public function getWopiFileById($id)
    {
        $wopiFile = $this->find('all', [
            'conditions' => [
                'id' => $id
            ]
        ])->first();

        return $wopiFile;
    }

    /**
     * @param $file_uuid
     * @return \Cake\Datasource\EntityInterface|null
     */
    public function generateFileuuid(): ?string
    {

        $uuid = Text::uuid();
        while ($this->uuidExists($uuid)) {
            # code...
            $uuid = Text::uuid();
        }

        return $uuid;
    }

    protected function uuidExists($uuid)
    {
        $wopiFile = $this->find('all', [
            'conditions' => [
                'file_uuid' => $uuid
            ]
        ])->first();

        return $wopiFile;
    }

    public function createRecord(array $data)
    {


        $data['file_uuid'] = $this->generateFileuuid();
        $data['file_path'] = $this->generateFilePath($data['file_uuid'], $data['file_extension']);

        //cp file_data to file_path
        file_put_contents($data['file_path'], file_get_contents($data['file_source']));

        //using blob - file_data write to path
        $blob = file_get_contents($data['file_path']);

        $data['file_data'] = $blob;
        //create new entity and unlock file_data field
        $wopiFile = $this->newEntity($data, ['accessibleFields' => ['file_data' => true]]);
        $wopiFile->version = $this->generateFileVersion($wopiFile);

        $wopiFile = $this->save($wopiFile);

        return $wopiFile;
    }

    public function updateRecord(int $wopiId, array $data)
    {

        $wopiFile = $this->get($wopiId);
        $wopiFile = $this->patchEntity($wopiFile, $data);
        $wopiFile = $this->save($wopiFile);

        return $wopiFile;
    }

    public function deleteRecord(int $wopiId)
    {

        $wopiFile = $this->get($wopiId);
        $wopiFile = $this->delete($wopiFile);

        return $wopiFile;
    }

    public function generateFilePath($file_uuid, $file_extension)
    {
        //generate file path in tmp
        $base = TMP . 'wopi' . DS;
        $file_path = $base . $file_uuid . '.' . $file_extension;

        //check if directory exists
        if (!file_exists($base)) {
            mkdir($base, 0777, true);
        } else {
            //check if file exists
            if (file_exists($file_path)) {

                unlink($file_path);
            }
        }

        return $file_path;
    }

    public function generateFileVersion(WopiFile $wopiFile)
    {
        //generate file version maxlength 255
        //read configuration
        $versioning = Configure::read('Wopi.versioning');
        $validVersioning = Configure::read('Wopi.valid_versioning_type');

        if (!$versioning) {
            throw new FileHandingException('Versioning configuration not found', 500, null);
        }

        if (!in_array($versioning, $validVersioning)) {
            throw new FileHandingException('Invalid versioning configuration', 500, null);
        }

        if ($versioning == 'timestamp') {
            //use format "c"
            $date = new FrozenTime();
            $version = $date->format('c');
        } elseif ($versioning == 'hash') {
            $version = Security::randomBytes(255);
        } elseif ($versioning == 'increment') {
            $version = $this->getLatestVersionForIncremental($wopiFile);
            $version = $version + 1;
        }

        return $version;
    }

    /**
     * This method is used to get the latest version of the file
     * @param WopiFile $wopiFile
     * @return int|string
     */
    private function getLatestVersionForIncremental(WopiFile $wopiFile)
    {
        $latestVersion = $this->find('all', [
            'conditions' => [
                'file_uuid' => $wopiFile->file_uuid
            ],
            'order' => [
                'version' => 'DESC'
            ]
        ])->first();

        if (!$latestVersion) {
            return 1;
        }

        return $latestVersion->version;
    }

    public function beforeSave(Event $event, Entity $entity, $options)
    {
        $entity->created_at = date('Y-m-d H:i:s');
        $entity->version = $this->generateFileVersion($entity);

        $entity->updated_at = date('Y-m-d H:i:s');

        return true;
    }

    public function deleteFile(int $fileId): bool
    {

        $delete = function () use ($fileId) {
            $wopiFile = $this->get($fileId);


            if($this->Locks->exists(['file_id' => $fileId])){
                $resultDeleteLock = $this->Locks->deleteAll(['file_id' => $fileId]);
                if (!$resultDeleteLock) {
                    return false;
                }
            }
            if($this->PivotWopiFilesProjects->exists(['wopi_file_id' => $fileId])){
                $resultDeletePivot = $this->PivotWopiFilesProjects->deleteAll(['wopi_file_id' => $fileId]);
                if (!$resultDeletePivot) {
                    return false;
                }
            }



            if (file_exists($wopiFile->file_path)) {
                $deleteFile = unlink($wopiFile->file_path);
            }

            if (!$deleteFile) {
                return false;
            }


            $wopiFile = $this->delete($wopiFile);

            return $wopiFile;
        };

        return $this->getConnection()->transactional($delete);
    }

    public function renameFile(int $fileId, string $newFileName): bool
    {

        $rename = function () use ($fileId, $newFileName) {


            $notunique = $this->find('all')->where(['file_name' => $newFileName , 'id !=' => $fileId ])->first();

            if ($notunique) {
                return false;
            }



            $wopiFile = $this->get($fileId);

            $wopiFile->file_name = $newFileName;

            $wopiFile = $this->save($wopiFile);

            return $wopiFile;
        };

        return $this->getConnection()->transactional($rename);
    }

    public function validateName(string $fileName){

        $notunique = $this->find('all')->where(['file_name' => $fileName])->first();

        if ($notunique) {
            return false;
        }

        //check length
        if(strlen($fileName) > 255){
            return false;
        }

        return true;
    }

    /**
     * Update file content
     *
     * @param  integer $fileId
     * @param  array   $data
     * @return \EaglenavigatorSystem\Wopi\Model\Entity\WopiFile $wopiFile
     * @throws \EaglenavigatorSystem\Wopi\Exception\FileUpdateContentIsEmptyException
     */
    public function updateFileContent(int $fileId, array $data)
    {

        $update = function () use ($fileId, $data) {
            //data will be from $this->request->getBody()->getContents())

            $wopiFile = $this->get($fileId);

            if(!$data['file_size'] || !$data['file_data']){
                throw new FileUpdateContentIsEmptyException('File size or file data is empty', 500, null);
            }

            $wopiFile->file_size = $data['file_size'];

            $wopiFile->version = $this->generateFileVersion($wopiFile);
            $wopiFile->file_data = $data['file_data'];

            $wopiFile = $this->save($wopiFile);

            return $wopiFile;
        };

        return $this->getConnection()->transactional($update);
    }


    public function putUserInfo(int $fileId,array $data){

        $userInfo = $data['user_info'];

        $file = $this->get($fileId);

        $file->user_info = $userInfo;

        $file = $this->save($file);

        if(!$file){

            throw new FileHandingException('Error saving user info', 500, null);
        }

        return $file;

    }

    public function putFileSoftDelete(int $fileId) {

        $file = $this->get($fileId);

        $file->soft_delete = true;
        $file->soft_delete_at = date('Y-m-d H:i:s');

        return $this->saveOrFail($file);
    }


}
