<?php


class DbPatch_Task_Status extends DbPatch_Task_Abstract
{
    const LIMIT = 10;
    
    public function execute()
    {
        $this->logger->log('Start DbPatch status');

        $patches = $this->getAppliedPatches(self::LIMIT);
        
        if (count($patches) == 0) {
            echo "no patches found\n";
        } else {
          foreach ($patches as $patch) {
              $this->logger->log(sprintf("%04d | %s | %s | %s",
                  $patch['patch_number'],
                  $patch['completed'],
                  $patch['filename'],
                  $patch['description']));
          }
        }
    }

    protected function getAppliedPatches($limit, $branch='')
    {
        $db = $this->getDb();
        
        $where = '';
        if ($branch != '') {
            $where = 'WHERE branch =\''.$db->escapeSQL($branch).'\'';
        }

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
            (int) self::LIMIT
        );

        return $db->fetchAll($sql);
    }

}
