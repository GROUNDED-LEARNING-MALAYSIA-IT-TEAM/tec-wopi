<?php
declare(strict_types=1);

namespace EaglenavigatorSystem\Wopi\Test\TestCase\Exception;

use EaglenavigatorSystem\Wopi\Exception\WopiDiscoveryException;
use Cake\TestSuite\TestCase;

/**
 * EaglenavigatorSystem\Wopi\Exception\WopiDiscoveryException Test Case
 */
class WopiDiscoveryExceptionTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \EaglenavigatorSystem\Wopi\Exception\WopiDiscoveryException
     */
    public $WopiDiscoveryException;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $message = 'Wopi discovery error: %s';
        $code = 500;
        $previous = null;
        $this->WopiDiscoveryException = new WopiDiscoveryException($message, $code, $previous);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->WopiDiscoveryException);

        parent::tearDown();
    }

    /**
     * Test __construct method
     *
     * @return void
     */
    public function testConstruct(): void
    {
        $message = 'Wopi discovery error: %s';
        $code = 500;
        $previous = null;
        $this->WopiDiscoveryException = new WopiDiscoveryException($message, $code, $previous);
        $this->assertInstanceOf(WopiDiscoveryException::class, $this->WopiDiscoveryException);
    }
}