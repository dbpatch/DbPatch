<?php


class DbPatch_Task_Sync extends DbPatch_Task_Abstract
{
    public function execute()
    {
        $this->writer->line('start syncing...');
        $branches = $this->detectBranches();

        foreach ($branches as $branch) {
            $patches = $this->getPatches($branch, '*');

            foreach ($patches as $patch) {
                $this->addToChangelog($patch);
            }
        }
        $this->writer->line('sync completed');
    }

    public function showHelp()
    {
       parent::showHelp('sync');
    }


    


}
