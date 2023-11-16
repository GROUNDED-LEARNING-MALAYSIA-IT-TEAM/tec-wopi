<?php

namespace EaglenavigatorSystem\Wopi\Test\TestCase\Model\Table;

use Cake\Event\Event;
use Cake\ORM\RulesChecker;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\TestSuite\TestCase;
use EaglenavigatorSystem\Wopi\Model\Table\WopiFilesTable;

/**
 * EaglenavigatorSystem\Wopi\Model\Table\WopiFilesTable Test Case
 */
class WopiFilesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \EaglenavigatorSystem\Wopi\Model\Table\WopiFilesTable
     */
    public $WopiFiles;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.EaglenavigatorSystem/Wopi.WopiFiles',
        'plugin.EaglenavigatorSystem/Wopi.Locks',

        'app.Users',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        //load plugin
        $this->loadPlugins(['EaglenavigatorSystem/Wopi']);
        $config = TableRegistry::getTableLocator()->exists('WopiFiles') ? [] : ['className' => WopiFilesTable::class];
        $this->WopiFiles = TableRegistry::getTableLocator()->get('WopiFiles', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->WopiFiles);
        $this->getTableLocator()->clear();
        $this->removePlugins(['EaglenavigatorSystem/Wopi']);
        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertInstanceOf(WopiFilesTable::class, $this->WopiFiles);
        $this->assertEquals('wopi_files', $this->WopiFiles->getTable());
        $this->assertEquals('id', $this->WopiFiles->getPrimaryKey());
        $this->assertEquals('id', $this->WopiFiles->getDisplayField());
        $this->assertInstanceOf('Cake\ORM\Association\BelongsTo', $this->WopiFiles->getAssociation('UserManagements'));
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $validator = new Validator();
        $validator = $this->WopiFiles->validationDefault($validator);

        $this->assertTrue($validator->hasField('id'));
        $this->assertTrue($validator->hasField('file_uuid'));
        $this->assertTrue($validator->hasField('file_name'));
        $this->assertTrue($validator->hasField('file_size'));
        $this->assertTrue($validator->hasField('version'));
        $this->assertTrue($validator->hasField('file_extension'));
        $this->assertTrue($validator->hasField('file_data'));
        $this->assertTrue($validator->hasField('file_path'));
        $this->assertTrue($validator->hasField('created_at'));
        $this->assertTrue($validator->hasField('updated_at'));
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $rules = new RulesChecker();
        $rules = $this->WopiFiles->buildRules($rules);

        $this->assertInstanceOf('Cake\ORM\RulesChecker', $rules);
    }

    /**
     * Test getWopiFile method
     *
     * @return void
     */
    public function testGetWopiFile()
    {
        $file_uuid = '12345678-1234-1234-1234-123456789012';


        //get file content as stream
        $filecontent = file_get_contents(TEST_FILE_PATH . 'test.txt');

        $wopiFile = $this->WopiFiles->getWopiFile($file_uuid);
        //write file_data to a stream
        //file data is a blob, so write it out to a temp file
        // Create a temporary file
        $tempfile = tmpfile();

        // Check if file_data is a resource and not a string
        if (is_resource($wopiFile->file_data)) {
            // If it's a stream, read the content and then write it to the temp file
            $streamContent = stream_get_contents($wopiFile->file_data);
            fwrite($tempfile, $streamContent);
        } else {
            // If it's already a string, write it directly to the temp file
            fwrite($tempfile, $wopiFile->file_data);
        }

        // Get the file's metadata to extract its path
        $metaData = stream_get_meta_data($tempfile);
        $tempFilePath = $metaData['uri'];

        // Read the temp file's content
        $filetempcontent = file_get_contents($tempFilePath);

        // Close the temporary file
        fclose($tempfile);

        $this->assertNotEmpty($wopiFile);
        $this->assertInstanceOf('EaglenavigatorSystem\Wopi\Model\Entity\WopiFile', $wopiFile);
        $this->assertEquals($file_uuid, $wopiFile->file_uuid);
        $this->assertEquals($filecontent, $filetempcontent);
    }

    /**
     * Test getWopiFileById method
     *
     * @return void
     */
    public function testGetWopiFileById()
    {
        $id = 2;
        $wopiFile = $this->WopiFiles->getWopiFileById($id);

        $this->assertInstanceOf('EaglenavigatorSystem\Wopi\Model\Entity\WopiFile', $wopiFile);
        $this->assertEquals($id, $wopiFile->id);
    }

    /**
     * Test generateFileuuid method
     *
     * @return void
     */
    public function testGenerateFileuuid()
    {
        $uuid = $this->WopiFiles->generateFileuuid();

        $this->assertNotEmpty($uuid);
        $this->assertRegExp('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $uuid);
    }

    /**
     * Test createRecord method
     *
     * @return void
     */
    public function testCreateRecord()
    {
        $source = TEST_FILE_PATH . 'test_b.txt';

        $data = [
            'file_name' => 'test_b.txt',
            'user_id' => 495,
            'file_size' => filesize($source),
            'file_extension' => 'txt',
            'file_source' => $source,
        ];

        $wopiFile = $this->WopiFiles->createRecord($data);

        $this->assertInstanceOf('EaglenavigatorSystem\Wopi\Model\Entity\WopiFile', $wopiFile);
        $this->assertNotEmpty($wopiFile->file_uuid);
        $this->assertNotEmpty($wopiFile->file_path);
        $this->assertNotEmpty($wopiFile->file_data);
        $this->assertNotEmpty($wopiFile->version);
        $this->assertNotEmpty($wopiFile->created_at);
        $this->assertNotEmpty($wopiFile->updated_at);
    }

    /**
     * Test updateRecord method
     *
     * @return void
     */
    public function testUpdateRecord()
    {

        $id = 2;
        $source = TEST_FILE_PATH . 'test_b.txt';
        $data = [
            'file_name' => 'test_b.txt',
            'user_id' => 495,
            'file_size' => filesize($source),
            'file_extension' => 'txt',
            'file_source' => $source,
        ];

        $wopiFile = $this->WopiFiles->updateRecord($id, $data);

        $this->assertInstanceOf('EaglenavigatorSystem\Wopi\Model\Entity\WopiFile', $wopiFile);
        $this->assertEquals($id, $wopiFile->id);
        $this->assertEquals($data['file_name'], $wopiFile->file_name);
        $this->assertEquals($data['file_size'], $wopiFile->file_size);
        $this->assertEquals($data['file_extension'], $wopiFile->file_extension);
    }

    /**
     * Test deleteRecord method
     *
     * @return void
     */
    public function testDeleteRecord()
    {
        $id = 1;
        $result = $this->WopiFiles->deleteRecord($id);

        $this->assertTrue($result);

        $this->assertEmpty($this->WopiFiles->getWopiFileById($id));
    }

    /**
     * Test generateFilePath method
     *
     * @return void
     */
    public function testGenerateFilePath()
    {
        $file_uuid = '12345678-1234-1234-1234-123456789012';
        $file_extension = 'txt';
        $file_path = $this->WopiFiles->generateFilePath($file_uuid, $file_extension);

        $this->assertNotEmpty($file_path);
        $this->assertRegExp('/^' . preg_quote(TMP . 'wopi' . DS, '/') . '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}\.txt$/i', $file_path);
    }

    /**
     * Test generateFileVersion method
     *
     * @return void
     */
    public function testGenerateFileVersion()
    {

        $source = TEST_FILE_PATH . 'test_c.txt';
        $data = [
            'file_name' => 'test_c.txt',
            'user_id' => 495,
            'file_size' => filesize($source),
            'file_extension' => 'txt',
            'file_source' => $source,
        ];
        $wopiFile = $this->WopiFiles->newEntity($data, ['accessibleFields' => ['file_data' => true]]);

        $version = $this->WopiFiles->generateFileVersion($wopiFile);

        $this->assertNotEmpty($version);
    }

    /**
     * Test beforeSave method
     *
     * @return void
     */
    public function testBeforeSave()
    {
        $event = new Event('Model.beforeSave');
        $entity = $this->WopiFiles->newEntity([
            'file_name' => 'test.txt',
            'file_size' => 1024,
            'file_extension' => 'txt',
            'file_data' => 'test',
        ]);
        $options = [];

        $this->WopiFiles->beforeSave($event, $entity, $options);

        $this->assertNotEmpty($entity->created_at);
        $this->assertNotEmpty($entity->updated_at);
        $this->assertNotEmpty($entity->version);

    }

    public function testDeleteFile()
    {
        $id = 1;

        $fileContent = 'Therefore, WOPI locks must:

            Be associated with a single file.

            Contain a lock ID of maximum length 1024 ASCII characters.

            Prevent all changes to that file unless a proper lock ID is provided.

            Expire after 30 minutes unless refreshed. For more information, see RefreshLock.

            Not be associated with a particular user.';

        $target = TEST_FILE_PATH . 'test_d.txt';

        //if file target do not exist, create it
        //if file target exist, overwrite it
        file_put_contents($target, $fileContent);


        $result = $this->WopiFiles->deleteFile($id);

        $this->assertTrue($result);
    }
}
