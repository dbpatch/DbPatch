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

    public function getConfig()
    {
        return $this->config;
    }

    public function getDb()
    {
        return $this->db;
    }

}