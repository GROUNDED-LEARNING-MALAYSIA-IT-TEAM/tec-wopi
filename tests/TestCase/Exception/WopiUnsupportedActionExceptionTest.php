<?php
declare(strict_types=1);

namespace EaglenavigatorSystem\Wopi\Test\TestCase\Exception;

use EaglenavigatorSystem\Wopi\Exception\WopiUnsupportedActionException;
use Cake\TestSuite\TestCase;

/**
 * EaglenavigatorSystem\Wopi\Exception\WopiUnsupportedActionException Test Case
 */
class WopiUnsupportedActionExceptionTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \EaglenavigatorSystem\Wopi\Exception\WopiUnsupportedActionException
     */
    public $WopiUnsupportedActionException;

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
        $this->WopiUnsupportedActionException = new WopiUnsupportedActionException($message, $code, $previous);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->WopiUnsupportedActionException);

        parent::tearDown();
    }

    /**
     * Test __construct method
     *
     * @return void
     */
    public function testConstruct(): void
    {
        $message = 'Wopi unsupported action error: %s';
        $code = 500;
        $previous = null;
        $this->WopiUnsupportedActionException = new WopiUnsupportedActionException($message, $code, $previous);
        $this->assertInstanceOf(WopiUnsupportedActionException::class, $this->WopiUnsupportedActionException);
    }
}