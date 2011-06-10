<?php


class DbPatch_Command_Help extends DbPatch_Command_Abstract
{
    public function execute()
    {
        $options = $this->console->getOptions();
        $commands = DbPatch_Command_Runner::getValidCommands();
        foreach ($commands as $command) {
            if ($command != 'help' && array_key_exists($command, $options)) {
                $class = 'DbPatch_Command_'.ucfirst(strtolower($command));

                $commandObj= new $class;
                $commandObj->setWriter($this->getWriter());
                $commandObj->showHelp();
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
