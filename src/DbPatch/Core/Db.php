<?php

class DbPatch_Core_Db
{
    protected $db = null;

    public function __construct($config)
    {
        $this->db = Zend_Db::factory($config->db);
    }

    public function getDb()
    {
        return $this->db;
    }
}