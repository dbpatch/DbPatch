<?php


class DbPatch_Task_Remove extends DbPatch_Task_Abstract
{
    public function execute()
    {
        if ($this->console->issetOption('patch')) {
            $patchNumbers = explode(",", $this->console->getOptionValue('patch', null));
            $filteredPatchNumbers = array_filter($patchNumbers, 'is_numeric');

            if (empty($filteredPatchNumbers)) {
                $this->error('no patch defined or patch isn\'t numeric');
                return;
            }

            $branch = $this->getBranch();
            foreach ($filteredPatchNumbers as $patchNumber) {
                $this->removePatch($patchNumber, $branch);
            }
            return;
        }
        $this->writer->line('No patch defined or patch isn\'t numeric');
        return;
    }

    /**
     * @param  $patchNumber
     * @param  $branchName
     * @return void
     */
    protected function removePatch($patchNumber, $branchName)
    {
        $db = $this->getDb();
        $branchSQL = "";

        if (!empty($branchName)) {
            $branchSQL = sprintf("AND `branch` = '%s'",
                                 $branchName);
        }

        $query = sprintf("SELECT branch
                              FROM `%s`
                              WHERE `patch_number` = %d {$branchSQL}",
                         self::TABLE,
                         $patchNumber);

        $stmt = $db->query($query);
        $patchRecords = $stmt->fetchAll();

        if (count($patchRecords) == 0) {
            $branchMsg = (empty($branchName) ? ""
                    : "for branch '$branchName' ");
            $this->getWriter()->line("Patch $patchNumber not found {$branchMsg}in `" . self::TABLE . "` table");
        }
        else if (count($patchRecords) > 1) {
            // @todo this is not happening anymore ???????
            $branchArray = array();
            foreach ($patchRecords as $branch) {
                $branchArray[] = $branch['branch'];
            }

            $this->getWriter()->line("There's a patch '$patchNumber' in multiple branches: '" . implode("', '", $branchArray) . "'");
            $this->getWriter()->line("Specify the correct branch by adding: 'branch=" . implode("' or 'branch=", $branchArray) . "' to the command");
        }
        else {
            $branchMsg = (empty($branchName) ? ""
                    : "from branch '$branchName' ");
            $query = sprintf("DELETE FROM `%s`
                                  WHERE `patch_number` = %d {$branchSQL}",
                             self::TABLE,
                             $patchNumber);

            $db->query($query);
            $db->commit();
            $this->getWriter()->line("Removed patch $patchNumber {$branchMsg}in the `" . self::TABLE . "` table");
        }
    }

    public function showHelp()
    {
        $this->getWriter()->line('remove');
    }
}