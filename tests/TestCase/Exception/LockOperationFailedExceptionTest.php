<?php
declare(strict_types=1);

namespace EaglenavigatorSystem\Wopi\Test\TestCase\Exception;

use EaglenavigatorSystem\Wopi\Exception\LockOperationFailedException;
use Cake\TestSuite\TestCase;

/**
 * EaglenavigatorSystem\Wopi\Exception\WopiDiscoveryException Test Case
 */
class WopiDiscoveryExceptionTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \EaglenavigatorSystem\Wopi\Exception\LockOperationFailedException
     */
    public $LockOperationFailedException;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $message = 'Lock operation failed: %s';
        $code = 500;
        $previous = null;
        $this->LockOperationFailedException = new LockOperationFailedException($message, $code, $previous);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->LockOperationFailedException);

        parent::tearDown();
    }

    /**
     * Test __construct method
     *
     * @return void
     */
    public function testConstruct(): void
    {
        $message = 'Lock operation failed: %s';
        $code = 500;
        $previous = null;
        $this->LockOperationFailedException = new LockOperationFailedException($message, $code, $previous);
        $this->assertInstanceOf(LockOperationFailedException::class, $this->LockOperationFailedException);
    }
}