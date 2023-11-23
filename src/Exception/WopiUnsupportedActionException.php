<?php
declare(strict_types=1);

namespace EaglenavigatorSystem\Wopi\Exception;

use Cake\Core\Exception\Exception;

/**
 * Wopi unsupported action exception
 *
 */
class WopiUnsupportedActionException extends Exception
{
    protected $_messageTemplate = 'Wopi unsupported action error: %s';

    public function __construct($message, $code = 500, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}