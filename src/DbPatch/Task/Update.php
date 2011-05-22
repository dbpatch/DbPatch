<?php

class DbPatch_Task_Update extends DbPatch_Task_Abstract
{
    public function execute()
    {
        if (!$this->validateChangelog()) {
            return;
        }
        
        $branch = $this->getBranch();
        
        die($branch);        
    }

    public function showHelp()
    {
        $this->getWriter()->line('update');
    }

}
