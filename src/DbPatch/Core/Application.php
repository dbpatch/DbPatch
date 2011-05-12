<?php

class DbPatch_Core_Application
{

    protected $opts;

    public function main()
    {
        $opts = $this->getOpts();
        $config = $this->getConfig($opts);
        $db = $this->getDb($config);
        $logger = $this->getLogger();

        try {
            $action = $this->getAction($opts);
        }
        catch (Zend_Console_Getopt_Exception $e)
        {
            echo 'ERROR: ' . $e->getMessage() . PHP_EOL . PHP_EOL;
            echo $opts->getUsageMessage() . PHP_EOL;
            exit;
        }

        $runner = new DbPatch_Task_Runner();
        $task = $runner->getTask($action);
        $task->setConfig($config)->setDb($db)->setLogger($logger);

        try
        {

            if ($config->verbose) {
                DbPatch_Core_Application::renderVersion();
            }

            $task->execute();
        }
        catch (Exception $e)
        {
            echo 'ERROR: ' . $e->getMessage() . PHP_EOL . PHP_EOL;
            echo $opts->getUsageMessage() . PHP_EOL;
            exit;
        }
    }

    protected function getLogger()
    {
        return new DbPatch_Core_Log();
    }

    protected function getOpts()
    {
        return new Zend_Console_Getopt($this->getRules());
    }

    protected function getConfig($opts)
    {
        $config = new DbPatch_Core_Config($opts);
        return $config->getConfig();
    }

    protected function getDb($config)
    {
        $db = new DbPatch_Core_Db($config);
        return $db->getDb();
    }

    protected function getAction($opts)
    {

        if (isset($opts->install)) {
            return 'install';
        } elseif (isset($opts->reinstall)) {
            return 'reinstall';
        } elseif (isset($opts->remove)) {
            return 'remove';
        } elseif (isset($opts->status)) {
            return 'status';
        } elseif (isset($opts->sync)) {
            return 'sync';
        } elseif (isset($opts->upgrade)) {
            return 'upgrade';
        } else {
            throw new Zend_Console_Getopt_Exception('Unknown action');
        }
    }


    protected function getRules()
    {
        $rules = array(
            'branch=s' => 'Branch',
            'config=s' => 'Config file',
            'help' => 'Displays usage information.',
            'install' => 'Install database',
            'patch=s' => 'Patch number',
            'remove' => 'Remove patch',
            'reinstall' => 'Reinstall database',
            'skip=s' => 'Skip patch number',
            'status' => 'Database patch status',
            'sync' => 'Sync database',
            'upgrade' => 'Upgrade database',
            'verbose' => 'Displays debug information.',
        );
        return $rules;
    }

    /**
     * Returns the version header.
     *
     * @return string
     */
    public static function renderVersion()
    {
        echo 'DbPatch version ' . DbPatch_Core_Version::VERSION . PHP_EOL . PHP_EOL;
    }
}
