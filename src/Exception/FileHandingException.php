<?php
declare(strict_types=1);

namespace EaglenavigatorSystem\Wopi\Exception;

use Cake\Core\Exception\Exception;

/**
 * Wopi discovery exception
 *
 */
class FileHandingException extends Exception
{
    protected $_messageTemplate = 'File handling failed : %s';

    public function __construct($message, $code = 500, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}