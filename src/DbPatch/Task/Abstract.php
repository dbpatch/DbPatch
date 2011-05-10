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
    protected $logger = null;
    protected $db = null;
    protected $config = null;
    
    abstract public function execute();

    public function setLogger(DbPatch_Core_Log $logger)
    {
        $this->logger = $logger;
    }

    public function setDb(DbPatch_Core_Db $db)
    {
        $this->db = $db;
    }

    public function setConfig(DbPatch_Core_Config $config)
    {
        $this->config = $config;
    }

}