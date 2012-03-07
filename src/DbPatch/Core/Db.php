<?php
/**
 * DbPatch
 *
 * Copyright (c) 2011, Sandy Pleyte.
 * Copyright (c) 2010-2011, Martijn De Letter.
 *
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *  * Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 *
 *  * Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in
 *    the documentation and/or other materials provided with the
 *    distribution.
 *
 *  * Neither the name of the authors nor the names of his
 *    contributors may be used to endorse or promote products derived
 *    from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package DbPatch
 * @subpackage Core
 * @author Sandy Pleyte
 * @author Martijn De Letter
 * @copyright 2011 Sandy Pleyte
 * @copyright 2010-2011 Martijn De Letter
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link http://www.github.com/dbpatch/DbPatch
 * @since File available since Release 1.0.0
 */

/**
 * Database object
 *
 * @package DbPatch
 * @subpackage Core
 * @author Sandy Pleyte
 * @author Martijn De Letter
 * @copyright 2011 Sandy Pleyte
 * @copyright 2010-2011 Martijn De Letter
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link http://www.github.com/dbpatch/DbPatch
 * @since File available since Release 1.0.0
 */
class DbPatch_Core_Db
{
    /**
     * @var null|\Zend_Db_Adapter_Abstract
     */
    protected $adapter = null;

    /**
     * @var Zend_Config $config
     */
    protected $config = null;

    /**
     * @param \Zend_Config|\Zend_Config_Ini|\Zend_Config_Xml $config
     * @return void
     */
    public function __construct($config)
    {
        $this->config = $config;
        if (!isset($config->db)) {
            throw new DbPatch_Exception('No database configuration found');
        }
        if ($config->db instanceof Zend_Db_Adapter_Abstract) {
            $this->adapter = $config->db;
        } else {
            $this->adapter = Zend_Db::factory(
                $config->db->adapter, $config->db->params->toArray()
            );
        }

        // Enable compatibility for the bin_dir setting
        $this->enableOldConfigCompatibility();
    }

    /**
     * @return null|Zend_Db_Adapter_Abstract
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @return Zend_Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Import a SQL file
     *
     * @throws DbPatch_Exception
     * @param string $filename
     * @return bool
     */
    public function import($filename)
    {
        $commandLine = $this->getCliCommand($this->config->import_command, $filename);

        $retval = exec($commandLine, $result, $return);

        if (($retval === false) || ($return <> 0)) {
            throw new DbPatch_Exception(
                'Error importing file ' .
                    $filename .
                    "\n" .
                    $commandLine .
                    "\n" .
                    implode(PHP_EOL, $result)
            );
        }
        return true;

    }

    /**
     * Dump database to file
     *
     * @throws DbPatch_Exception
     * @param string $filename
     * @return bool
     */
    public function dump($filename)
    {
        $commandLine = $this->getCliCommand($this->config->dump_command, $filename);

        $retval = exec($commandLine, $result, $return);

        if (($retval === false) || ($return <> 0)) {
            throw new DbPatch_Exception(
                'Error dumping file ' .
                    $filename .
                    "\n" .
                    $commandLine .
                    "\n" .
                    implode(PHP_EOL, $result)
            );
        }
        return true;
    }

    /**
     * @param string $command Shell command template to execute,
     *                takes :configkey notation
     * @param string $filename Filename of patch or dump file
     * @return string Command to execute
     */
    protected function getCliCommand($command, $filename)
    {
        $config = $this->getAdapter()->getConfig();
        $config['filename'] = $filename;

        foreach (array(
                     'host', 'port', 'username', 'password', 'dbname', 'charset', 'filename'
                 ) as $key) {
            $value = !empty($config[$key])
                ? escapeshellarg($config[$key])
                : '';

            $command = str_replace(":$key", $value, $command);
        }

        // do not send options if empty and optional
        //! @todo not really elegant, works fine for mysql
        return str_replace(array(
            '-p\'\'', '--password=\'\'',
            '-P\'\'', '--port=\'\'',
        ), '', $command);
    }

    /**
     * This method provides backward compatibility for
     * the 'bin_dir' configuration option. it could be
     * removed in future versions. using bin_dir only
     * is not sufficient because it limits the user to
     * mysql/mysqldump. it's also not possible to use
     * bin_dir and pass a Zend_Db_Adapter instance as
     * configuration value.
     *
     * @return DbPatch_Core_Db
     */
    protected function enableOldConfigCompatibility()
    {
        $options = '-h:host -P\':port\' -u:username -p\':password\' --default-character-set=:charset :dbname';

        if (!isset($this->config->dump_command)) {
            $dir = '';

            if (isset($this->config->db->bin_dir)) {
                $dir = $this->config->db->bin_dir . DIRECTORY_SEPARATOR;
            }

            $this->config->dump_command = "{$dir}mysqldump {$options} > :filename 2>&1";
        }

        if (!isset($this->config->import_command)) {
            $dir = '';

            if (isset($this->config->db->bin_dir)) {
                $dir = $this->config->db->bin_dir . DIRECTORY_SEPARATOR;
            }

            $this->config->import_command = "{$dir}mysql {$options} < :filename 2>&1";
        }

        return $this;
    }
}
