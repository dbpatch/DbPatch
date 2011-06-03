<?php


class DbPatch_Task_Show extends DbPatch_Task_Abstract
{
    public function init()
    {
        $this->writer->setVerbose();
        return parent::init();
    }

    public function execute()
    {
        if ($this->console->issetOption('patch')) {
            $patchNumber = $this->console->getOptionValue('patch', null);
            if(!is_null($patchNumber) && is_numeric($patchNumber)) {

                $this->showPatch($patchNumber);
                return;
            }
        }
        $this->writer->error('No patch defined or patch isn\'t numeric');
        return;
    }

    public function showPatch($patchNumber)
    {
        $branch = $this->getBranch();
        $patch = $this->getPatch($patchNumber, $branch);

        if ($patch == null) {
            $this->writer->error("no patchfile found for patch number: " . $patchNumber);
            return;
        }
        DbPatch_Task_Runner::showVersion();
        $this->writer
            ->line("Show patch $patchNumber (" . $patch->basename . "):")
            ->line();
        $patch->show();
        return;
    }

    public function showHelp()
    {
        parent::showHelp('show');

        $writer = $this->getWriter();
        $writer->indent(2)->line('--patch=<int>      Patchnumber to show')
            ->line();
    }

}