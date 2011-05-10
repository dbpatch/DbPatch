<?php
/**
 * Created by JetBrains PhpStorm.
 * User: sandy
 * Date: 08-05-11
 * Time: 19:17
 * To change this template use File | Settings | File Templates.
 */

class DbPatch_Core_Application
{

    protected $opts;

    public function main()
    {
        //@todo create database, inject config
        $this->opts = new Zend_Console_Getopt($this->getRules());

        try {
            $action = $this->getAction();
        }
        catch(Zend_Console_Getopt_Exception $e)
         {
             echo 'ERROR: '.$e->getMessage() . PHP_EOL . PHP_EOL;
             echo $this->getUsageMessage() . PHP_EOL;
             exit;

         }

        $runner = new DbPatch_Task_Runner();
        $task = $runner->getTask($action);

        try
        {

            if ($this->getVerbose())
            {
              DbPatch_Core_Application::renderVersion();
            }

            $task->execute();
        }
        catch(Exception $e)
        {
            echo 'ERROR: '.$e->getMessage() . PHP_EOL . PHP_EOL;
            echo $this->getUsageMessage() . PHP_EOL;
            exit;
        }
        $log->log('End DbPatch');


    }

    public function getUsageMessage()
    {
       echo $this->opts->getUsageMessage();
    }

    public function getVerbose()
    {
        if (isset($this->opts->v)) {
            return true;
        }
        return false;
    }

    protected function getAction()
    {

        if (!isset($this->opts->config)) {
            throw new Zend_Console_Getopt_Exception('No config file set');
        } elseif (isset($this->opts->install)) {
            return 'install';
        } elseif (isset($this->opts->reinstall)) {
            return 'reinstall';
        } elseif (isset($this->opts->remove)) {
            return 'remove';
        } elseif (isset($this->opts->status)) {
            return 'status';
        } elseif (isset($this->opts->sync)) {
            return 'sync';
        } elseif (isset($this->opts->upgrade)) {
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
            'sync'  => 'Sync database',
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
