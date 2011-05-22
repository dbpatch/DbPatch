<?php


class DbPatch_Task_Sync extends DbPatch_Task_Abstract
{
    public function execute()
    {
        $branches = $this->detectBranches();

        foreach ($branches as $branch) {
            $patches = $this->getPatches($branch);

            foreach ($patches as $patch) {
                $this->addToChangelog($patch);
            }
        }
    }

    public function showHelp()
    {
        $this->getWriter()->line('sync');
    }


    


}
