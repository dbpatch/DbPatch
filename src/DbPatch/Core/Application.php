<?php
/**
 * The core application object.
 * Setup different objects and fireup the command runner
 */
class DbPatch_Core_Application
{
    /**
     * Initialize the dbpatch application
     * Typically called from bin/dbpatch.php
     */
    public function main()
    {
        $console    = $this->getConsole($_SERVER['argv']);
        $logger     = $this->getLogger();
        $writer     = $this->getWriter();
        $runner     = $this->getCommandRunner($writer);
        $configFile = $console->getOptionValue('config', null);
        $useColor   = $console->getOptionValue('color', false);

        if ($useColor) {
            $writer->setColor($this->getWriterColor());
        }

        // Load the right config file
        try {
            $config  = $this->getConfig($configFile);
        } catch (Exception $e) {
            $writer->error($e->getMessage())->line();
            $runner->showHelp();
            exit;
        }
        
        $db = $this->getDb($config);

        if ($console->issetOption('version')) {
            $writer->version();
            return;
        }

        // Finally execute the right command
        try {
            $command = $console->getCommand();
            $runner->getCommand($command, $console)
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

    /**
     * Returns the dbpatch logger object
     *
     * @return DbPatch_Core_Log
     */
    protected function getLogger()
    {
        return new DbPatch_Core_Log();
    }

    /**
     * Creates the command runner with a specific CLI writer
     *
     * @param DbPatch_Core_Writer $writer 
     * @return DbPatch_Command_Runner
     */
    protected function getCommandRunner($writer)
    {
        return new DbPatch_Command_Runner($writer);
    }

    /**
     * Try to load the right config file
     *
     * @param string $filename
     * @return DbPatch_Core_Config
     */
    protected function getConfig($filename = null)
    {
        $config = new DbPatch_Core_Config($filename);
        return $config->getConfig();
    }

    /**
     * DB object
     *
     * @param DbPatch_Core_Config $config
     * @return DbPatch_Core_Db
     */
    protected function getDb($config)
    {
        $db = new DbPatch_Core_Db($config);
        return $db->getDb();
    }
    
    /**
     * CLI writer
     *
     * @return DbPatch_Core_Writer
     */
    protected function getWriter()
    {
        return new DbPatch_Core_Writer();
    }

    /**
     * Creates a console object
     *
     * @param array $argv
     * @return DbPatch_Core_Writer
     */
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
