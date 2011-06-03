<?php


class DbPatch_Task_Create extends DbPatch_Task_Abstract
{
    public function execute()
    {
        $type = $this->console->getOptionValue('type', null);
        $patchNumber = $this->console->getOptionValue('number', null);

        if (is_null($type) || !in_array(strtolower($type), array('php', 'sql'))) {
            throw new exception ('Invalid patch type!');
        }

        $patch = DbPatch_Task_Patch::factory($type);
        $patch->setWriter($this->getWriter());

        if (is_null($patchNumber)) {
            $branch = $this->getBranch();
            $patches = $this->getPatches($branch, '*');

            if (count($patches)) {
                $lastPatch = end($patches);
                $patchNumber = $lastPatch->patchNumber + 1;
            } else {
                $patchNumber = 1;
            }
        }

        $description = $this->console->getOptionValue('description', 'Empty Patch');

        $patch->patchNumber = $patchNumber;
        $patch->branch = $this->getBranch();

        $patch->create($description, $this->getPatchDirectory(), $this->getPatchPrefix());
    }

    public function showHelp()
    {
        parent::showHelp('create');
        $writer = $this->getWriter();
        $writer->indent(2)->line('--type=<type>      create patch of the type `php` or `sql`')
            ->indent(2)->line('--number=<int>     Patchnumber to create')
            ->line();
    }
}