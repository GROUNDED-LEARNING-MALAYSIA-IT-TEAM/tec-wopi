<?php
namespace EaglenavigatorSystem\Wopi\Test\TestCase\Controller\Component;

use Cake\Controller\ComponentRegistry;
use Cake\TestSuite\TestCase;
use EaglenavigatorSystem\Wopi\Controller\Component\SessionComponent;

/**
 * EaglenavigatorSystem\Wopi\Controller\Component\SessionComponent Test Case
 */
class SessionComponentTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \EaglenavigatorSystem\Wopi\Controller\Component\SessionComponent
     */
    public $Session;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $registry = new ComponentRegistry();
        $this->Session = new SessionComponent($registry);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Session);

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
