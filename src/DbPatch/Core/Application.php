<?php

class DbPatch_Core_Application
{

    protected $opts;

    public function main()
    {
        $console = $this->getConsole($_SERVER['argv']);
        
        //@todo pass optional config file 
        $config  = $this->getConfig();
        $db      = $this->getDb($config);
        $logger  = $this->getLogger();
        $writer  = $this->getWriter();

        $task    = $console->getTask();
        $options = $console->getOptions();
        
        try
        {
            $runner = $this->getTaskRunner($writer);
            $runner->getTask($task)
                ->setConfig($config)
                ->setDb($db)
                ->setLogger($logger)
                ->execute();
        }
        catch (Exception $e)
        {
            $runner->showHelp();
            exit;
        }
    }

    protected function getLogger()
    {
        return new DbPatch_Core_Log();
    }

    protected function getTaskRunner($writer)
    {
        return new DbPatch_Task_Runner($writer);
    }

    protected function getConfig($filename = null)
    {
        $config = new DbPatch_Core_Config($filename);
        return $config->getConfig();
    }

    protected function getDb($config)
    {
        $db = new DbPatch_Core_Db($config);
        return $db->getDb();
    }
    
    protected function getWriter()
    {
        return new DbPatch_Core_Writer();
    }
    
    protected function getConsole($argv)
    {
        return new DbPatch_Core_Console($argv);
    }
}
