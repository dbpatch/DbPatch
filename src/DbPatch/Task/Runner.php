<?php
/**
 * Created by JetBrains PhpStorm.
 * User: sandy
 * Date: 08-05-11
 * Time: 19:46
 * To change this template use File | Settings | File Templates.
 */
 
class DbPatch_Task_Runner
{
    private $opts = null;

    public function __construct()
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
        $this->opts = new Zend_Console_Getopt($rules);


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



    public function getTask()
    {
        try {
            $this->opts->parse();
            $action = $this->getAction();

            switch($action) {
                case 'install' : // install
                    $class = 'DbPatch_Task_Install';
                    break;
                case 'reinstall' : // reinstall
                    $class = 'DbPatch_Task_Reinstall';
                    break;
                case 'remove' : // remove patch
                    $class = 'DbPatch_Task_Remove';
                    break;
                case 'status' : // Status
                    $class = 'DbPatch_Task_Status';
                    break;
                case 'sync' : // Status
                    $class = 'DbPatch_Task_Sync';
                    break;
                case 'upgrade' : // Upgrade'
                    $class = 'DbPatch_Task_Upgrade';
                    break;
            }

            $task = new $class;
            
            return $task;

        } catch (Zend_Console_Getopt_Exception $e) {
            throw new Exception($e->getMessage() );
        }
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
}