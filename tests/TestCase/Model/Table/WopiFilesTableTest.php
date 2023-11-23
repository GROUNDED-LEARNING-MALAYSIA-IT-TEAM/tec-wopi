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
    //TEST_FILE_PATH is in tests/test_files folder of plugin
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
        'plugin.EaglenavigatorSystem/Wopi.PivotWopiFilesProjects',

        'app.Users',
    ];

    public string $originalContentTest = '"UMECH showed genuine interest in conducting business in Penang and was the sole company expressing such interest at the expo.

    "Prior to the expo, materials and promotions (for) the upcoming projects had... received approval from the state government and PDC Board," he said in reply to a question from Lee Khai Loon (PH-Machang Bubok).

    Chow said the selection of the company was done in a lengthy and transparent process based on established criteria.';

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        //load plugin
        //it is 2 level up from this file
        //so it is in plugins/EaglenavigatorSystem/Wopi
        define('TEST_FILE_PATH_TEST', dirname(dirname(dirname(dirname(__FILE__)))) . DS . 'test_files' . DS);

        $this->loadPlugins(['EaglenavigatorSystem/Wopi']);
        $config = TableRegistry::getTableLocator()->exists('WopiFiles') ? [] : ['className' => WopiFilesTable::class];
        $this->WopiFiles = TableRegistry::getTableLocator()->get('WopiFiles', $config);

        $fileTestContent = 'From this configuration, there are no apparent errors or issues. The bootstrap file is correctly specified, and the test suite is properly defined. If you are experiencing issues or errors when running tests, the problem might be elsewhere, such as in the actual test code, the bootstrap file, or the environment setup.

        ';

        $filetest1 = TEST_FILE_PATH_TEST . 'test.txt';

        //if filetest1 do not exist, create it
        if (!file_exists($filetest1)) {
            touch($filetest1);
            file_put_contents($filetest1, $fileTestContent);

        } else {

            //if filetest1 exist, overwrite it

            file_put_contents($filetest1, $fileTestContent);
        }
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

        $fileUpdateTarget = TEST_FILE_PATH_TEST . 'test_f.txt';

        $fileTestContent = 'From this configuration, there are no apparent errors or issues. The bootstrap file is correctly specified, and the test suite is properly defined. If you are experiencing issues or errors when running tests, the problem might be elsewhere, such as in the actual test code, the bootstrap file, or the environment setup.

        ';

        $filetest1 = TEST_FILE_PATH_TEST . 'test.txt';


        //update the content to original
        file_put_contents($fileUpdateTarget, $this->originalContentTest);

        //if filetest1 do not exist, create it
        if (!file_exists($filetest1)) {
            touch($filetest1);
            file_put_contents($filetest1, $fileTestContent);

        } else {

            //if filetest1 exist, overwrite it

            file_put_contents($filetest1, $fileTestContent);
        }

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
        $filecontent = file_get_contents(TEST_FILE_PATH_TEST . 'test.txt');

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
        //source is file path
        $source = TEST_FILE_PATH_TEST . 'test_b.txt';
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
        $source = TEST_FILE_PATH_TEST . 'test_b.txt';
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

        $source = TEST_FILE_PATH_TEST . 'test_c.txt';
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

        $target = TEST_FILE_PATH_TEST . 'test_d.txt';

        //if file target do not exist, create it
        if (!file_exists($target)) {
            touch($target);
            file_put_contents($target, $fileContent);

        } else {

        //if file target exist, overwrite it

        file_put_contents($target, $fileContent);
        }



        $result = $this->WopiFiles->deleteFile($id);

        $this->assertTrue($result);
    }

    public function testUpdateFileContent()
    {
        $original = 'UMECH showed genuine interest in conducting business in Penang and was the sole company expressing such interest at the expo.

        "Prior to the expo, materials and promotions (for) the upcoming projects had... received approval from the state government and PDC Board," he said in reply to a question from Lee Khai Loon (PH-Machang Bubok).

        Chow said the selection of the company was done in a lengthy and transparent process based on established criteria.';

        $changed  = 'The police have recorded Umno supreme council member Isham Jalil\'s statement after Dr Siti Mastura Muhammad claimed she obtained information about DAP\'s supposed links with communist leaders from a Barisan Nasional campaign leaflet.

        "Bukit Aman called me to ask about Siti Mastura\'s case. I\'m not really familiar with that issue but the police suddenly rang me up.

        "The police said that Siti Mastura claimed that her information was based on a leaflet by BN Communications,\" said Isham in a video posted on Facebook on Saturday (Nov 18).

        He said while he was the former Umno information chief during GE15, the party and Barisan were two separate entities.

        "I don\'t think Barisan published this. Whatever it is, let the police investigate," said Isham.
        He also advised Siti Mastura to cooperate with the police.';

        $target = TEST_FILE_PATH_TEST . 'test_f.txt';


        //use $changed, create tempfile
        $tempFile = tmpfile();
        fwrite($tempFile, $changed);

        //get the temp file path
        $metaData = stream_get_meta_data($tempFile);
        $tempFilePath = $metaData['uri'];
        //size
        $size = filesize($tempFilePath);

        //get content
        $content = file_get_contents($tempFilePath);

        //close the temp file

        //use size and content to override original

        $id = 2;

        $data = [
            'file_size' => $size,
            'file_data' => $content,
        ];

        $result = $this->WopiFiles->updateFileContent($id, $data);

        dump($result);


        //get file content as stream
        $filecontent = file_get_contents($result->file_path);

        //write file_data to a stream
        //file data is a blob, so write it out to a temp file
        // Create a temporary file
        $tempfile = tmpfile();

        // Check if file_data is a resource and not a string
        if (is_resource($result->file_data)) {
            // If it's a stream, read the content and then write it to the temp file
            $streamContent = stream_get_contents($result->file_data);

            $this->assertEquals($changed, $streamContent);
        }

        $this->assertInstanceOf('EaglenavigatorSystem\Wopi\Model\Entity\WopiFile', $result);


    }


    public function testPutUserInfo()
    {
        $data = [
            'user_info' => 'zH=HmN)0EnU]5a)ead48w?AWWap{]|{KUq*cHF.?SM]WST{_2uMkQ4c\'Fg)\\Z?`f6Hca16jBk:)H*m%>M[O,s3UVT\'y$9Q/69P%X<yvkmZgruh$j1#Ck;~E)\'kh0q$c\"P(|2kG\"^q3v\\IO&F?q|Qv+@p>i]b9=UuDX+dmJa@vL4)n[!x3(c7D\'Zf|&fW|6`i1KM)9fC5(VE0>,kV,Xj)foPP7\\s\'zx@AE;b8z,~OqIaFXExiABWeHkksQ<qXwK#]mru>\'=W?11G&i7;vc)Z3M*ebY^*S-##!<a|Bs#:<1pTK+kn<t;+B{Y%_>a-u^DHN-C>fc[/Cjb=KM;,\\`+S/nT4Q:/=\'4xF8L%NSU\\kiVp8RaaU7(!bajD&ZZkK$~Z:F^&I\\$CTNx~s\'FH]#)#\"5D|-0%9Ojs*CRD1@$D*<sQ2AzdY\\Doi@bl?0/7,PYc7MtCW;MG/.qlz_z].!p:W9>[&oEU*\\Y:\"!IiMSA4`jEIAYXrpMj;Kk\'wy>:ug.1lo=--5b|VR|_qt{j;;\'{b7\"-2M3B$PaIU>a#;QFw,YVJk[j\"7&~?}/UXdz'
            ];
        $result = $this->WopiFiles->putUserInfo(2, $data);

        $this->assertInstanceOf('EaglenavigatorSystem\Wopi\Model\Entity\WopiFile', $result);


    }

    public function testPutFileSoftDelete()
    {
        $result = $this->WopiFiles->putFileSoftDelete(3);

        $this->assertInstanceOf('EaglenavigatorSystem\Wopi\Model\Entity\WopiFile', $result);
        $this->assertTrue($result->soft_delete);
        $this->assertNotEmpty($result->soft_delete_at);
    }
}
