<?php
namespace EaglenavigatorSystem\Wopi\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use EaglenavigatorSystem\Wopi\Model\Table\LocksTable;

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
        'plugin.EaglenavigatorSystem/Wopi.Locks',
        'plugin.EaglenavigatorSystem/Wopi.Files',
        'plugin.EaglenavigatorSystem/Wopi.UserManagements',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Locks') ? [] : ['className' => LocksTable::class];
        $this->Locks = TableRegistry::getTableLocator()->get('Locks', $config);
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
