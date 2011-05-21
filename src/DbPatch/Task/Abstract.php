<?php
/**
 * Created by JetBrains PhpStorm.
 * User: sandy
 * Date: 08-05-11
 * Time: 23:03
 * To change this template use File | Settings | File Templates.
 */
 
abstract class DbPatch_Task_Abstract
{
    const DEFAULT_BRANCH = 'default';
    const TABLE = 'db_changelog';

    protected $logger = null;
    protected $db = null;
    protected $config = null;
    protected $writer = null;
    protected $options = array();
    
    abstract public function execute();

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

    public function setConfig($config)
    {
        $this->config = $config;
        return $this;
    }

    public function setWriter($writer)
    {
        $this->writer = $writer;
        return $this;
    }

    public function getConfig()
    {
        return $this->config;
    }
    
    public function getWriter()
    {
        return $this->writer;
    }
    
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
    
    /**
     * Checks if the changelog table is present in the database
     * @return bool
     */
    protected function changelogExists()
    {
        $db = $this->getDb();
        $result = $db->fetchOne(
            $db->quoteInto('SHOW TABLES LIKE ?', $this->table)
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
            `description` varchar(200) default NULL,
            PRIMARY KEY  (`patch_number`, `branch`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",
            $db->quoteIdentifier(self::TABLE)
        ));

        if (! $this->changelogExists()) {
            return false;
        }
        
        $this->getWriter()->line(sprintf("changelog table '%s' created", self::TABLE));
        $this->getWriter()->line("use 'dbpatch sync' to sync your patches");
        
        return true;
    }
    
    /**
     * Determine the branch based on the given parameters
     * @param array $params
     * @return string
     */
    protected function getBranch($params)
    {
        if (isset($params['branch']) && $params['branch'] != '') {
            return $params['branch'];
        }
        return self::DEFAULT_BRANCH;
    }
}