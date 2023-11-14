<?php
declare(strict_types=1);

namespace EaglenavigatorSystem\Wopi\Exception;

use Cake\Core\Exception\Exception;

/**
 * Wopi discovery exception
 *
 */
class LockMismatchException extends Exception
{
    protected $_messageTemplate = 'Lock operation failed lock mismatch: %s';

    public function __construct($message, $code = 500, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}