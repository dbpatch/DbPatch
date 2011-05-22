<?php
/**
 * Created by JetBrains PhpStorm.
 * User: sandy
 * Date: 10-05-11
 * Time: 23:08
 * To change this template use File | Settings | File Templates.
 */

class DbPatch_Core_Log
{
    const EMERG = Zend_Log::EMERG;
    const ALERT = Zend_Log::ALERT;
    const CRIT = Zend_Log::CRIT;
    const ERR = Zend_Log::ERR;
    const WARN = Zend_Log::WARN;
    const NOTICE = Zend_Log::NOTICE;
    const INFO = Zend_Log::INFO;
    const DEBUG = Zend_Log::DEBUG;
    const QUIET = -1;
    const FILE_STDOUT = 'php://stdout';
    
    protected $threshold = self::INFO;

    protected $logger = null;

    public function __construct()
    {
        $file = self::FILE_STDOUT;
        $this->logger = new Zend_Log(new Zend_Log_Writer_Stream(fopen($file, 'w')));
    }

    public function setThreshold($threshold)
    {
        if (!is_numeric($threshold)) {
            if (!defined('DbPatch_Core_Log::' . strtoupper($threshold))) {
                throw new InvalidArgumentException(
                    'Expected one of the constants of the DbPatch_Core_Log class, "' . $threshold . '" received'
                );
            }
            $threshold = constant('DbPatch_Core_Log::' . strtoupper($threshold));
        }

        $this->threshold = $threshold;
    }

    public function getThreshold()
    {
        return $this->threshold;
    }

    public function log($data, $level = self::INFO)
    {
        // is the log level is below the priority; just skip this
        if ($this->getThreshold() < $level) {
            return;
        }

        // if the given is not a string then we var dump the object|array to inspect it
        if (!is_string($data)) {
            ob_start();
            var_dump($data);
            $data = ob_get_clean();
        }

        /*
        $data = (($this->getThreshold() == Zend_Log::DEBUG)
                ? '[' . number_format(round(memory_get_usage() / 1024 / 1024, 2), 2) . 'mb]: '
                : '')
                . $data;
        */
        $this->logger->log($data, $level);
    }


}