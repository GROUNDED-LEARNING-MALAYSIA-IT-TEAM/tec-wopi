<?php
namespace EaglenavigatorSystem\Wopi\Test\TestCase\Controller\Component;

use Cake\Controller\ComponentRegistry;
use Cake\TestSuite\TestCase;
use EaglenavigatorSystem\Wopi\Controller\Component\WopiComponent;

/**
 * EaglenavigatorSystem\Wopi\Controller\Component\WopiComponent Test Case
 */
class WopiComponentTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \EaglenavigatorSystem\Wopi\Controller\Component\WopiComponent
     */
    public $Wopi;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $registry = new ComponentRegistry();
        $this->Wopi = new WopiComponent($registry);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Wopi);

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
