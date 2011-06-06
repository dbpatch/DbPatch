<?php


class DbPatch_Task_Status extends DbPatch_Task_Abstract
{
    const LIMIT = 10;

    public function execute()
    {
        $this->writer->version();

        $branches = $this->detectBranches();

        foreach ($branches as $branch) {
            $this->showPatchesToApply($branch);
        }

        $limit = $this->getLimit();
        $patches = $this->getAppliedPatches();
        $this->getWriter()->line()->line("applied patches (".$limit." latest)")->separate();

        if (count($patches) == 0) {
             $this->getWriter()->warning("no patches found")->line();
        } else {
          foreach ($patches as $patch) {
              $this->writer->line(sprintf("%04d | %s | %s | %s",
                  $patch['patch_number'],
                  $patch['completed'],
                  $patch['filename'],
                  $patch['description']));
          }
        }
    }

    /**
     * Output all the patches to apply for a specific branch
     * @param string $branch
     */
    protected function showPatchesToApply($branch)
    {
        $line = 'patches to apply';
        if ($branch <> self::DEFAULT_BRANCH) {
            $line .= " for branch '{$branch}'";
        }
        $this->getWriter()->line($line)->separate();

        $patches = $this->getPatches($branch);

        if (count($patches) == 0) {
            $this->getWriter()->line("no patches found")->line();
        } else {
          foreach ($patches as $patch) {
              $this->getWriter()->line(sprintf("%04d | %s | %s",
                  $patch->patchNumber,
                  $patch->basename,
                  $patch->description));
          }

          $line = "use 'dbpatch update";
          if ($branch <> self::DEFAULT_BRANCH) {
              $line .= " branch={$branch}";
          }
          $line .= "' to apply the patches\n";
          $this->getWriter()->line()->line($line);
        }
    }

    protected function getLimit()
    {
        $limit = $this->config->get('limit', self::LIMIT);
        return $limit;
    }




    protected function getAppliedPatches($branch='')
    {
        $db = $this->getDb();
        
        $where = '';
        if ($branch != '') {
            $where = 'WHERE branch =\''.$db->escapeSQL($branch).'\'';
        }
        $limit = $this->getLimit();

        $sql = sprintf("
            SELECT
                `patch_number`,
                `completed`,
                `filename`,
                `description`,
                IF(`branch`='%s',0,1) as `branch_order`
            FROM `%s`
            %s
            ORDER BY `completed` DESC, `branch_order` ASC, `patch_number` DESC
            LIMIT %d",
            self::DEFAULT_BRANCH,
            self::TABLE,
            $where,
            (int) $limit
        );

        return $db->fetchAll($sql);
    }

    public function showHelp()
    {
        parent::showHelp('status');
    }


}
