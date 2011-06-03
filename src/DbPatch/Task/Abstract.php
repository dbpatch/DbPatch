<?php

abstract class DbPatch_Task_Abstract
{
    const DEFAULT_BRANCH = 'default';
    const TABLE = 'db_changelog';
    const PATCH_DIRECTORY = './database/patch';
    const PATCH_PREFIX = 'patch';

    /**
     * @var $logger DbPatch_Core_Log
     */
    protected $logger = null;

    /**
     * @var $db Zend_Db
     */
    protected $db = null;

    /**
     * @var $config Zend_Config
     */
    protected $config = null;

    /**
     * @var $console DbPatch_Core_Console
     */
    protected $console = null;

    /**
     * @var $writer DbPatch_Core_Writer
     */
    protected $writer = null;
    protected $options = array();
    
    abstract public function execute();

    public function init()
    {
        if (!$this->validateChangelog()) {
            throw new Exception('Can\'t create changelog table');
        }
        return $this;
    }

    public function setLogger(DbPatch_Core_Log $logger)
    {
        $this->logger = $logger;
        return $this;
    }

    public function setDb($db)
    {
        $this->db = $db;
        return $this;
    }

    public function setConsole(DbPatch_Core_Console $console)
    {
        $this->console = $console;
        return $this;
    }

    public function setConfig(Zend_Config $config)
    {
        $this->config = $config;
        return $this;
    }

    public function setWriter(DbPatch_Core_Writer $writer)
    {
        $this->writer = $writer;
        return $this;
    }

    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return DbPatch_Core_Writer|null
     */
    public function getWriter()
    {
        return $this->writer;
    }

    /**
     * @return null|Zend_Db
     */
    public function getDb()
    {
        return $this->db;
    }
    
    public function setOptions($options)
    {
        $this->options = $options;
    }
    
    public function getOptions()
    {
        return $this->options;
    }
    
    /**
     * Validate if the changelog is present in the database
     * if not try to create the table
     * @return bool
     */
    protected function validateChangelog()
    {
        if ($this->changelogExists()) {
            return true;
        }
        
        $this->getWriter()
            ->line("no changelog database found, try to create one");
            
        if (!$this->createChangelog()) {
            $this->getWriter()
                ->line("couldn't create a changelog table");
            return false;
        }
        return true;
    }

    public function getConsole()
    {
        return $this->console;
    }

    public function getBranch()
    {
        if ($this->console instanceof DbPatch_Core_Console &&
            $this->console->issetOption('branch')) {
            return $this->console->getOptionValue('branch', self::DEFAULT_BRANCH);
        } else {
            return self::DEFAULT_BRANCH;
        }
    }

    public function getPatchPrefix()
    {
        if (isset($this->config->patch_prefix)) {
            $prefix = $this->config->patch_prefix;
        } else {
            $prefix = self::PATCH_PREFIX;
        }
        return $prefix;
    }

    public function getPatchDirectory()
    {
        if (isset($this->config->patch_directory)) {
            $dir = $this->config->patch_directory;
        } else {
            $dir = self::PATCH_DIRECTORY;
        }
        return $dir;
    }

    /**
     * Check if the passed patch number can be found in the changelog table for the specified branch
     *
     * @todo Also check if the patch has been modified and return status (no, yes, yes but changed)
     * @param int $patchNumber
     * @param string $branch
     * @return boolean $result true if patch already applied; false if not
     */
    protected function isPatchApplied($patchNumber, $branch)
    {
        $db = $this->getDb();
        $query = sprintf("SELECT COUNT(patch_number) as applied
                          FROM `%s`
                          WHERE `patch_number` = %d
                          AND `branch` = '%s'",
                          self::TABLE,
                          $patchNumber,
                          $branch);

        $patchRecords = $db->fetchAll($query);

        if ((int)$patchRecords[0]['applied'] == 0) {
            return false;
        }

        return true;
    }

    public function getPatches($branch, $searchPatchNumber = null)
    {
        $patchDirectory = $this->getPatchDirectory();

        if (!file_exists($patchDirectory)) {
            $this->writer->error('path '. $patchDirectory .' doesn\'t exists');
            return array();
        }

        try {
            $iterator = new DirectoryIterator($patchDirectory);
        } catch (Exception $e) {
            $this->writer->line('Error: '.$e->getMessage());
            return array();
        }

        $branch = $branch == '' ? $this->getBranch() : $branch;
        $patchPrefix = $this->getPatchPrefix();

        if ($branch <> self::DEFAULT_BRANCH) {
            $patchPrefix .= '-'.$branch;
        }
        
        $patches = array();
        $pattern = '/^'.preg_quote($patchPrefix).'-(\d{3,4})\.(sql|php)$/';

        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isDot() || substr($fileinfo->getFilename(),0,1) == '.') {
               continue;
            }
            if (preg_match($pattern, $fileinfo->getFilename(), $matches)) {
                $patchNumber = (int) $matches[1];

                if ((!is_null($searchPatchNumber) && $searchPatchNumber != '*' && $patchNumber != $searchPatchNumber) ||
                    is_null($searchPatchNumber) && $this->isPatchApplied($patchNumber, $branch)) {
                    continue;
                }

                $filename = $patchDirectory . '/' . $fileinfo->getFilename();
                $type = $matches[2];

                $patch = DbPatch_Task_Patch::factory($type);

                $patch->loadFromArray(
                    array(
                        'filename' => $filename,
                        'basename' => $matches[0],
                        'patchNumber' => $patchNumber,
                        'branch' => $branch
                    )
                );

                $patches[$patchNumber] = $patch;
            } 
        }
        return $patches;
    }


    /**
     * Detect the different branches are used in the patch dir
     * the default branch is always returned
     * @return array with branches
     */
    protected function detectBranches()
    {
        $branches = array(self::DEFAULT_BRANCH);
        $patchDir = $this->getPatchDirectory();

        $patchDirectory = $this->getPatchDirectory();
        try {
            $iterator = new DirectoryIterator($patchDirectory);
        } catch (Exception $e) {
            $this->writer->line('Error: '.$e->getMessage());
            return array();
        }


        $patchPrefix = $this->getPatchPrefix();
        $pattern = '/^'.preg_quote($patchPrefix).'-(.*)?-\d{3,4}\.(sql|php)$/';

        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isDot() || substr($fileinfo->getFilename(),0,1) == '.') {
               continue;
            }

            if (preg_match($pattern, $fileinfo->getFilename(), $matches)) {

                $branch = $matches[1];
                if (!in_array($branch, $branches)) {
                    $branches[] = $branch;
                }
            }
        }
        return $branches;
    }




    public function getPatch($patchNumber, $branch)
    {
        $patches = $this->getPatches($branch, $patchNumber);
        if (array_key_exists($patchNumber, $patches)) {
            return $patches[$patchNumber];
        }
        return false;
    }
    
        /**
     * Checks if the changelog table is present in the database
     * @return bool
     */
    protected function changelogExists()
    {
        $db = $this->getDb();
        $result = $db->fetchOne(
            $db->quoteInto('SHOW TABLES LIKE ?', self::TABLE)
        );
        
        return (bool) ($result == self::TABLE);
    }

    /**
     * Try to create the changelog table
     * @return bool
     */
    protected function createChangelog()
    {
        if ($this->changelogExists()) {
            return true;
        }

        $db = $this->getDb();
        
        $db->query(
            sprintf("
            CREATE TABLE %s (
            `patch_number` int(11) NOT NULL,
            `branch` varchar(50) NOT NULL,
            `completed` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
            `filename` varchar(100) NOT NULL,
            `hash` varchar(32) NOT NULL,
            `description` varchar(200) default NULL,
            PRIMARY KEY  (`patch_number`, `branch`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",
            $db->quoteIdentifier(self::TABLE)
        ));
        $db->commit();

        if (! $this->changelogExists()) {
            return false;
        }
        
        $this->getWriter()->line(sprintf("changelog table '%s' created", self::TABLE));
        $this->getWriter()->line("use 'dbpatch sync' to sync your patches");
        
        return true;
    }

     /**
     * Store patchfile entry to the changelog table
     * @param array $patchFile
     * @param string $description
     * @return void
     */
    protected function addToChangelog($patchFile, $description=null)
    {
        $this->writer->line(sprintf(
            'added %s to the changelog ', $patchFile->basename));

        if ($description == null) {
            $description = $patchFile->description;
        }
        $db = $this->getDb();

        $sql = sprintf("
            INSERT INTO %s (patch_number, branch, completed, filename, description, hash)
            VALUES(%d, %s, NOW(), %s, %s, %s)",
            $db->quoteIdentifier(self::TABLE),
            $patchFile->patch_number,
            $db->quote($patchFile->branch),
            $db->quote($patchFile->basename),
            $db->quote($description),
            $db->quote($patchFile->hash)
        );

        $db->query($sql);
        $db->commit();
    }

    protected function showHelp($task)
    {
        $writer = $this->getWriter();
        $writer->setVerbose();
        $writer->line('dbpatch version ' . DbPatch_Core_Version::VERSION);
        $writer->line('usage: dbpatch ' . $task . ' [<args>]')
            ->line()
            ->line('The args are:')
            ->indent(2)->line('--config=<string>  Filename of the config file')
            ->indent(2)->line('--branch=<string>  Branch name')
            ->indent(2)->line('--color            Show colored output')
            ->indent(2)->line('--verbose          Show output');

    }

}