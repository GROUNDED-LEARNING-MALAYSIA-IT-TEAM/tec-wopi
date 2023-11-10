<?php
namespace EaglenavigatorSystem\Wopi\Test\TestCase\Model\Behavior;

use Cake\TestSuite\TestCase;
use EaglenavigatorSystem\Wopi\Model\Behavior\DocumentManagerBehavior;

/**
 * EaglenavigatorSystem\Wopi\Model\Behavior\DocumentManagerBehavior Test Case
 */
class DocumentManagerBehaviorTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \EaglenavigatorSystem\Wopi\Model\Behavior\DocumentManagerBehavior
     */
    public $DocumentManager;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->DocumentManager = new DocumentManagerBehavior();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->DocumentManager);

        parent::tearDown();
    }

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testInitialization()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
