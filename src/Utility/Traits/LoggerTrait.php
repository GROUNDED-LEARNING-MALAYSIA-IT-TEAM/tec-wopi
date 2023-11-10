
<?php
namespace EaglenavigatorSystem\Wopi\Utility\Traits;

use Cake\Log\LogTrait;

trait LoggerTrait
{
    use LogTrait;

    /**
     * Logs a message at the debug level.
     *
     * @param string $message The message to log.
     * @param array $context Additional context to include in the log message.
     * @return void
     */
    public function debug($message, array $context = [])
    {
        $this->log($message, 'debug', $context);
    }

    /**
     * Logs a message at the info level.
     *
     * @param string $message The message to log.
     * @param array $context Additional context to include in the log message.
     * @return void
     */
    public function info($message, array $context = [])
    {
        $this->log($message, 'info', $context);
    }

    /**
     * Logs a message at the warning level.
     *
     * @param string $message The message to log.
     * @param array $context Additional context to include in the log message.
     * @return void
     */
    public function warning($message, array $context = [])
    {
        $this->log($message, 'warning', $context);
    }

    /**
     * Logs a message at the error level.
     *
     * @param string $message The message to log.
     * @param array $context Additional context to include in the log message.
     * @return void
     */
    public function error($message, array $context = [])
    {
        $this->log($message, 'error', $context);
    }
}
