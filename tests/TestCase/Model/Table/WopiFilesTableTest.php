<?php
namespace EaglenavigatorSystem\Wopi\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
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
        'plugin.EaglenavigatorSystem/Wopi.Users',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
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

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
