<?php
class DbPatch_Task_Patch_PHP extends DbPatch_Task_Patch_Abstract
{
    protected $data = array(
        'filename' => null,
        'basename' => null,
        'patch_number' => null,
        'branch' => null,
        'description' => null,
    );

    public function apply()
    {

        $db = $this->getDb();
        $writer = $this->getWriter();
        $phpFile = $this->filename;

        $writer->line('apply patch: '. $this->basename);

        if (!file_exists($phpFile)) {
            $this->getWriter()->line(sprintf('php file %s doesn\'t exists', $phpFile));
            return false;
        }

        try {
            include($phpFile);
        } catch (Exception $e) {
            $this->getWriter()->line(sprintf('error php patch: %s', $e->getMessage()));
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

    public function create($description, $patchDirectory, $patchPrefix)
    {
        $patchNumberSize = $this->getPatchNumberSize($patchDirectory);
        $filename = $this->getPatchFilename($patchPrefix, strtolower($this->getType()), $patchNumberSize);
        $content = '<?php'. PHP_EOL . '// ' . $description . PHP_EOL;
        $this->writeFile($patchDirectory . $filename, $content);
    }

}
 
