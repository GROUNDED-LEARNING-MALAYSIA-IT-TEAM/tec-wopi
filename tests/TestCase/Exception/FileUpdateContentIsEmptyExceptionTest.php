<?php
declare(strict_types=1);

namespace EaglenavigatorSystem\Wopi\Test\TestCase\Exception;

use EaglenavigatorSystem\Wopi\Exception\FileUpdateContentIsEmptyException;
use Cake\TestSuite\TestCase;

/**
 * EaglenavigatorSystem\Wopi\Exception\FileHandingException Test Case
 */
class FileUpdateContentIsEmptyExceptionTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \EaglenavigatorSystem\Wopi\Exception\FileUpdateContentIsEmptyException
     */
    public $FileUpdateContentIsEmptyException;

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
        $this->FileUpdateContentIsEmptyException = new FileUpdateContentIsEmptyException($message, $code, $previous);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->FileUpdateContentIsEmptyException);

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
        $this->FileUpdateContentIsEmptyException = new FileUpdateContentIsEmptyException($message, $code, $previous);
        $this->assertInstanceOf(FileUpdateContentIsEmptyException::class, $this->FileUpdateContentIsEmptyException);
    }
}