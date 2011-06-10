<?php

class DbPatch_Command_Update extends DbPatch_Command_Abstract
{
    public function execute()
    {
        $branch = $this->getBranch();
        $force = ($this->console->issetOption('force')) ? true : false;

        $latestPatchNumber = $this->getLastPatchNumber($branch);

        $this->writer->line('last patch number applied: '. $latestPatchNumber);
        $patchFiles = $this->getPatches($branch);
        
        if (count($patchFiles) == 0) {
            $this->writer->success("no update needed");
            return;
        }

        $this->writer->line(sprintf('found %d patch %s',
            count($patchFiles),
            (count($patchFiles) == 1) ? 'file' : 'files'
        ));

        $patchNumbersToSkip = $this->getPatchNumbersToSkip($this->console->getOptions(), $patchFiles);

        if(count($patchNumbersToSkip)) {
            $this->writer->line('Skip patchnumbers: '.implode(',', $patchNumbersToSkip));
        }

        foreach ($patchFiles as $patchNr => $patchFile)
        {
            if (($patchFile->patch_number <> $latestPatchNumber + 1) && !$force) {
                $this->writer->error(
                    sprintf('expected patch number %d instead of %d (%s). Use --force to override this check.',
                        $latestPatchNumber + 1,
                        $patchFile->patch_number,
                        $patchFile->basename
                    )
                );
                return;
            }


            if (in_array($patchNr, $patchNumbersToSkip)) {
                $this->writer->line('manually skiped patch '.$patchFile->basename);
                $this->addToChangelog($patchFile, 'manually skipped');
                $latestPatchNumber = $patchFile->patch_number;
                continue;
            }

            $result = $patchFile->setDb($this->db)
                ->setConfig($this->config)
                ->setWriter($this->writer)
                ->apply();

            if (!$result) {
              return;
            }

            $this->addToChangelog($patchFile);


            $latestPatchNumber = $patchFile->patch_number;

        }
    }

    /**
     * Returns the last applied patch number from the database
     * @param  string $branch
     * @return int
     */
    protected function getLastPatchNumber($branch)
    {
        $patch = $this->getAppliedPatches(1, $branch);

        if (count($patch) == 0) {
            return 0;
        }

        return $patch[0]['patch_number'];
    }

    /**
     * Return the already applied patches from the patch tabel
     * @param int $limit
     * @param string $branch
     * @return array
     */
    protected function getAppliedPatches($limit, $branch='')
    {
        $db = $this->getDb();

        $where = '';
        if ($branch != '') {
            $where = 'WHERE branch = '.$db->quote($branch);
        }

        $sql = sprintf("
            SELECT
                `patch_number`,
                `completed`,
                `filename`,
                `description`,
                IF(`branch`=%s,0,1) as `branch_order`
            FROM %s
            %s
            ORDER BY `completed` DESC, `branch_order` ASC, `patch_number` DESC
            LIMIT %d",
            $db->quote(self::DEFAULT_BRANCH),
            $db->quoteIdentifier(self::TABLE),
            $where,
            (int) $limit
        );
        
        return $db->fetchAll($sql);
    }



    /**
     * Determine which patch numbers can be skipped
     * We may only skip numbers that are ready to apply
     *
     * These patches will not be executed and marked as skipped in the changelog
     *
     * @param array $params commandline params
     * @param array $patchFiles patches that are ready to apply
     * @return array $patchNumbers patchnumbers to skip
     */
    protected function getPatchNumbersToSkip($params, $patchFiles)
    {
        if (!isset($params['skip'])) {
            return array();
        }

        // requested numbers to skip
        $patchNumbers = explode(",", $params['skip']);

        // we may only skip numbers that are ready to apply
        $readyToApplyPatches = array_keys($patchFiles);

        // check which patchnumbers match
        $validPatchNumbers = array_intersect($patchNumbers, $readyToApplyPatches);

        return $validPatchNumbers;
    }
    
    public function showHelp()
    {
        parent::showHelp('update');
        $writer = $this->getWriter();
        $writer
            ->indent(2)->line('--skip=<int>       One or more patchnumbers seperated by a comma to skip')
            ->indent(2)->line('--force            Force the update, and ignore missing patches')
            ->line();
    }
}
