<?php
class DbPatch_Task_Patch_PHP extends DbPatch_Task_Patch_Abstract
{
    protected $data = array(
        'filename' => null,
        'basename' => null,
        'patch_number' => null,
        'branch' => null,
    );

    public function apply()
    {
        $db = $this->getDb();
        $writer = $this->getWriter();
        $phpFile = $this->filename;

        if (!file_exists($phpFile)) {
            $this->error(sprintf('php file %s doesn\'t exists', $phpFile));
            return false;
        }

        try {
            include($phpFile);
        } catch (Exception $e) {
            $this->error(sprintf('error php patch: %s', $e->getMessage()));
            return false;
        }

        return true;
    }

    public function getType()
    {
        return 'PHP';
    }

    public function getDescription()
    {
        return $this->getComment(1);
    }
}
 
