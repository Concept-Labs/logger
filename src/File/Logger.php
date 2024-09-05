<?php
namespace Concept\Logger\File;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Psr\Log\AbstractLogger;

class Logger extends AbstractLogger implements LoggerInterface
{
    private ?string$filePath = null;
    
    /**
     * @param string $filePath
     * 
     * @return self
     */
    public function withFile(string $filePath): self
    {
        $this->filePath = $filePath;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function log($level, $message, array $context = [])
    {
        if (empty($this->filePath)) {
            throw new \Exception('File path is not set. use withFile() method to set file path');
        }

        $logMessage = sprintf("[%s] %s: %s\n", date('Y-m-d H:i:s'), strtoupper($level), $this->interpolate($message, $context));
        
        file_put_contents($this->filePath, $logMessage, FILE_APPEND);

        if ($level === LogLevel::EMERGENCY || $level === LogLevel::ALERT || $level === LogLevel::CRITICAL) {
            $this->sendNotification($logMessage);
        }
    }


    /**
     * Interpolates context values into the message placeholders.
     * 
     * @param string $message
     * @param array $context
     * 
     * @return string
     */
    private function interpolate(string $message, array $context = []): string
    {

        $replace = [];
        foreach ($context as $key => $val) {
            $replace['{' . $key . '}'] = $val;
        }

        return strtr($message, $replace);
    }

    /**
     * Send notification
     * 
     * @param string $message
     */
    private function sendNotification(string $message)
    {
        // send email, sms, slack message, etc
    }
}
