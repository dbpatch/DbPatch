<?php

class DbPatch_Core_Config
{
    protected $config = null;

    /**
     * Create config object
     *
     * @param string $filename optional config file
     */
    public function __construct($filename = null)
    {
        if (is_null($filename) || !file_exists($filename)) {
            $filename = $this->searchConfigFile();
        }

        if (is_null($filename)) {
            throw new Exception('No config file found');
        }

        $type = $this->detectConfigType($filename);

        switch ($type) {
            case 'php' :
                $dbPatchConfig = array();
                require_once $filename;
                $this->config = new Zend_Config($dbPatchConfig);
                break;
            case 'ini' :
                $this->config = new Zend_Config_Ini($filename, 'dbpatch');
                break;
            case 'xml' :
                $this->config = new Zend_Config_Xml($filename, 'dbpatch');
                break;
            default:
                throw new Exception('Not a valid config file');
        }
    }

    protected function searchConfigFile()
    {
        $supportedConfigExtentsions = array('php', 'ini', 'xml');

        foreach($supportedConfigExtentsions as $ext) {
            $filename = './dbpatch.' . $ext;
            if (file_exists($filename)) {
                return $filename;
            }
        }
        return null;
    }

    protected function detectConfigType($filename)
    {
        return strtolower(end(explode('.',$filename )));
    }

    public function getConfig()
    {
        return $this->config;
    }
}
