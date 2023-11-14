<?php
declare(strict_types=1);

namespace EaglenavigatorSystem\Wopi\Test\TestCase\Exception;

use EaglenavigatorSystem\Wopi\Exception\FileHandingException;
use Cake\TestSuite\TestCase;

/**
 * EaglenavigatorSystem\Wopi\Exception\FileHandingException Test Case
 */
class FileHandingExceptionTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \EaglenavigatorSystem\Wopi\Exception\FileHandingException
     */
    public $FileHandingException;

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
        $this->FileHandingException = new FileHandingException($message, $code, $previous);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->FileHandingException);

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
        $this->FileHandingException = new FileHandingException($message, $code, $previous);
        $this->assertInstanceOf(FileHandingException::class, $this->FileHandingException);
    }
}