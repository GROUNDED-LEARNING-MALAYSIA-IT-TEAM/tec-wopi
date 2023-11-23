<?php
declare(strict_types=1);

namespace EaglenavigatorSystem\Wopi\Test\TestCase\Exception;

use EaglenavigatorSystem\Wopi\Exception\FileManagementException;
use Cake\TestSuite\TestCase;

/**
 * EaglenavigatorSystem\Wopi\Exception\FileHandingException Test Case
 */
class FileManagementExceptionTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \EaglenavigatorSystem\Wopi\Exception\FileManagementException
     */
    public $FileManagementException;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $message = 'File management process failed: %s';
        $code = 500;
        $previous = null;
        $this->FileManagementException = new FileManagementException($message, $code, $previous);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->FileManagementException);

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
        $this->FileManagementException = new FileManagementException($message, $code, $previous);
        $this->assertInstanceOf(FileManagementException::class, $this->FileManagementException);
    }
}