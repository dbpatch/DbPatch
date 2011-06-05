<?php


class DbPatch_Task_Help extends DbPatch_Task_Abstract
{
    public function execute()
    {
        $options = $this->console->getOptions();
        $tasks = DbPatch_Task_Runner::getValidTasks();
        foreach ($tasks as $task) {
            if ($task != 'help' && array_key_exists($task, $options)) {
                $class = 'DbPatch_Task_'.ucfirst(strtolower($task));

                $taskObj= new $class;
                $taskObj->setWriter($this->getWriter());
                $taskObj->showHelp();
                return;
            }
        }
        throw new Exception('Please provide a valid command');
    }

    public function showHelp()
    {
        parent::showHelp('help');
    }
}