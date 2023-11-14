<?php
declare(strict_types=1);

namespace EaglenavigatorSystem\Wopi\Test\TestCase\Exception;

use EaglenavigatorSystem\Wopi\Exception\LockMismatchException;
use Cake\TestSuite\TestCase;

/**
 * EaglenavigatorSystem\Wopi\Exception\LockMismatchException Test Case
 */
class LockMismatchExceptionTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \EaglenavigatorSystem\Wopi\Exception\LockMismatchException
     */
    public $LockMismatchException;

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
        $this->LockMismatchException = new LockMismatchException($message, $code, $previous);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->LockMismatchException);

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
        $this->LockMismatchException = new LockMismatchException($message, $code, $previous);
        $this->assertInstanceOf(LockMismatchException::class, $this->LockMismatchException);
    }
}