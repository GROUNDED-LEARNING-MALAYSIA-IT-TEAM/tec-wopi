<?php

namespace EaglenavigatorSystem\Wopi\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use EaglenavigatorSystem\Wopi\Exception\LockMismatchException;
use EaglenavigatorSystem\Wopi\Exception\LockOperationFailedException;
use EaglenavigatorSystem\Wopi\Model\Table\LocksTable;
use Cake\ORM\RulesChecker;

/**
 * EaglenavigatorSystem\Wopi\Model\Table\LocksTable Test Case
 */
class LocksTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \EaglenavigatorSystem\Wopi\Model\Table\LocksTable
     */
    public $Locks;

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
        $config = TableRegistry::getTableLocator()->exists('EaglenavigatorSystem/Wopi.Locks') ? [] : ['className' => LocksTable::class];
        $this->Locks = TableRegistry::getTableLocator()->get('EaglenavigatorSystem/Wopi.Locks', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Locks);
        parent::tearDown();
    }


    public function testInitialization()
    {
        $this->assertInstanceOf(LocksTable::class, $this->Locks);
    }

    public function testGenerateExpirationDate()
    {
        $result = $this->Locks->generateExpirationDate();

        $this->assertInstanceOf('Cake\I18n\FrozenTime', $result);
    }

    //test validation
    public function testValidationDefault()
    {
        $data = [
            'file_id' => 1,
            'lock_id' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
            'lock_expires' => '2018-01-01 00:00:00',
            'locked_by_user_id' => 495,
            'expiration_time' => $this->Locks->generateExpirationDate(),
            'created_at' => '2018-01-01 00:00:00',
            'updated_at' => '2018-01-01 00:00:00',
        ];

        $entity = $this->Locks->newEntity($data);


        $this->assertEmpty($entity->getErrors());
    }

    //test table rule
    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $rules = new RulesChecker();
        $rules = $this->Locks->buildRules($rules);

        $this->assertInstanceOf('Cake\ORM\RulesChecker', $rules);
    }

    /**
     * Test lockFile method
     *
     * @return void
     */
    public function testLockFile()
    {
        $fileId = 1;
        $userId = 495;
        $lockId = 'xxxxxxxxxxxxxxxxxxxxxxxxx';

        $fileId2 = 2;
        $userId = 495;
        $lockId2 = 'xxxxxxxxxxxxxxx7777777';

        $result = $this->Locks->unlockFile($fileId, $userId, $lockId);
        $result = $this->Locks->unlockFile($fileId2, $userId, $lockId2);

        // Test locking a file for the first time
        $result = $this->Locks->lockFile($fileId, $userId, $lockId);
        $this->assertTrue($result);
        // Test locking a file that is already locked by another user
        $result = $this->Locks->lockFile($fileId2, $userId, $lockId2);
        $this->assertTrue($result);
    }

    /**
     * Test unlockFile method
     *
     * @return void
     */
    public function testUnlockFile()
    {
        $fileId = 1;
        $userId = 495;
        $lockId = 'xxxxxxxxxxxxxxxxxxxxxxxxx';

        $fileId2 = 2;
        $userId = 495;
        $lockId2 = 'xxxxxxxxxxxxxxx7777777';

        // Test unlocking a file that is not locked
        $this->expectException(LockMismatchException::class);

        $result = $this->Locks->unlockFile($fileId, $userId, 3);
        $this->assertFalse($result);


        // Test unlocking a file successfully
        $this->Locks->lockFile($fileId, $userId);
        $result = $this->Locks->unlockFile($fileId, $userId, $lockId);
        $this->assertTrue($result);

        // Test unlocking a file successfully
        $this->Locks->lockFile($fileId, $userId);
        $result = $this->Locks->unlockFile($fileId2, $userId, $lockId2);
        $this->assertTrue($result);
    }

    /**
     * Test refreshLock method
     *
     * @return void
     */
    public function testRefreshLock()
    {
        $fileId = 1;
        $userId = 495;
        $lockId = 'xxxxxxxxxxxxxxxxxxxxxxxxx';


        $fileId2 = 2;
        $userId = 495;
        $lockId2 = 'xxxxxxxxxxxxxxx7777777';


        // Test refreshing a lock that does not exist
        $result = $this->Locks->refreshLock($fileId, $userId,  $lockId);
        $this->assertTrue($result);

        // Test refreshing a lock with a mismatched lock ID
        $this->expectException(LockMismatchException::class);
        $this->Locks->refreshLock($fileId, $userId, 44);

        $result = $this->Locks->refreshLock($fileId2, $userId,  $lockId2);
        $this->assertTrue($result);
    }

    /**
     * Test deleteFileLock method
     *
     * @return void
     */
    public function testDeleteFileLock()
    {
        $fileIdFailed = 3;
        $fileId = 1;


        // Test deleting a lock that does not exist
        $result = $this->Locks->deleteFileLock($fileIdFailed);
        $this->assertFalse($result);

        // Test deleting a lock successfully
        $this->Locks->lockFile($fileId, 1);
        $result = $this->Locks->deleteFileLock($fileId);
        $this->assertTrue($result);
    }

    /**
     * Test checkWopiLock method
     *
     * @return void
     */
    public function testCheckWopiLock()
    {
        $checkLockId = 'xxxxxxxxxxxxxxxxxxxxxxxxx';
        $fileId = 1;

        $checkLockId2 = 'xxxxxxxxxxxxxxxxxxxxxxxxx';
        $fileId2 = 33;

        // Test checking a lock that does not exist
        $result = $this->Locks->checkWopiLock($checkLockId, $fileId);
        $this->assertTrue($result);

        $result2 = $this->Locks->checkWopiLock($checkLockId2, $fileId2);
        $this->assertFalse($result2);



    }
}
