<?php
/**
 * The core application object.
 * Setup different objects and fireup the task runner
 */
class DbPatch_Core_Application
{
    public function main()
    {
        $console    = $this->getConsole($_SERVER['argv']);
        $logger     = $this->getLogger();
        $writer     = $this->getWriter();
        $runner     = $this->getTaskRunner($writer);
        $configFile = $console->getOptionValue('config', null);
        $useColor   = $console->getOptionValue('color', false);
        $verbose    = $console->getOptionValue('verbose', false);

        if ($useColor) {
            $writer->setColor($this->getWriterColor());
        }

        if ($verbose) {
            $writer->setVerbose();
        }

        try {
            $config  = $this->getConfig($configFile);
        } catch (Exception $e) {
            $writer->error($e->getMessage())->line();
            $runner->showHelp();
            exit;
        }
        
        $db = $this->getDb($config);

        if ($console->issetOption('version')) {
            $writer->setVerbose()->version();
            return;
        }

        try {
            $task = $console->getTask();
            $runner->getTask($task, $console)
                ->setConfig($config)
                ->setDb($db)
                ->setLogger($logger)
                ->init()
                ->execute();
                
        } catch (Exception $e) {
            $writer->error($e->getMessage())->line();
            $runner->showHelp();
            exit;
        }
        return;
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

    /**
     * Color object to pass to CLI writer
     * @return DbPatch_Core_Color
     */
    protected function getWriterColor()
    {
        return new DbPatch_Core_Color();
    }
}
