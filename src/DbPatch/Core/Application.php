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
    public function main()
    {
        //@todo create database, inject config

        $runner = new DbPatch_Task_Runner();
        $task = $runner->getTask();

        try
        {

            if ($runner->getVerbose())
            {
              DbPatch_Core_Application::renderVersion();
            }

            $task->execute();
        }
        catch(Exception $e)
        {
            echo 'ERROR: '.$e->getMessage() . PHP_EOL . PHP_EOL;
            echo $runner->getUsageMessage() . PHP_EOL;
        }


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
