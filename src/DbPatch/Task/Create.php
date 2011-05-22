<?php


class DbPatch_Task_Create extends DbPatch_Task_Abstract
{
    public function execute()
    {
        $type = $this->console->getOptionValue('type', null);

        if (is_null($type) || !in_array(strtolower($type), array('php', 'sql'))) {
            throw new exception ('Invalid patch type!');
        }

        $patch = DbPatch_Task_Patch::factory($type);
        $patch->setWriter($this->getWriter());
        $branch = $this->getBranch();
        $patches = $this->getPatches($branch);

        if (count($patches)) {
            $lastPatch = end($patches);
            $lastestPatchNr = $lastPatch->patchNumber + 1;
        } else {
            $lastestPatchNr = 1;
        }

        $description = $this->console->getOptionValue('description', 'Empty Patch');

        $patch->patchNumber = $lastestPatchNr;
        $patch->branch = $this->getBranch();

        $patch->create($description, $this->getPatchDirectory(), $this->getPatchPrefix());
    }

    public function showHelp()
    {
        $this->getWriter()->line('create');
    }


    


}