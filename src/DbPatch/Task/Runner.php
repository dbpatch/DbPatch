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
    {}

    public function getTask($action, $configFilename = null)
    {
        $class = null;
        
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

    }
}