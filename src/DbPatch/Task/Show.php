<?php


class DbPatch_Task_Show extends DbPatch_Task_Abstract
{
    public function execute()
    {
        if ($this->console->issetOption('patch')) {
            $patchNumber = $this->console->getOptionValue('patch', null);
            if(!is_null($patchNumber) && is_numeric($patchNumber)) {

                $this->showPatch($patchNumber);
                return;
            }
        }
        $this->writer->line('No patch defined or patch isn\'t numeric');
        return;
    }

    public function showPatch($patchNumber)
    {
        $branch = $this->getBranch();
        $patch = $this->getPatch($patchNumber, $branch);

        if ($patch == null) {
            $this->writer->line("no patchfile found for patch number: " . $patchNumber);
            return;
        }
        DbPatch_Task_Runner::showVersion();
        $this->writer->line("Show patch $patchNumber (" . $patch->basename . "):");
        $this->writer->line($patch->getHash());
        $patch->show();
        return;
    }
}