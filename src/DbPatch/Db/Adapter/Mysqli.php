<?php
/**
 * DbPatch
 *
 * Copyright (c) 2011, Sandy Pleyte.
 * Copyright (c) 2010-2011, Martijn de Letter.
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
 * @subpackage Db_Adapter
 * @author Sandy Pleyte
 * @author Martijn de Letter
 * @copyright 2011 Sandy Pleyte
 * @copyright 2010-2011 Martijn de Letter
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link http://www.github.com/sndpl/DbPatch
 * @since File available since Release 1.0.0
 */

/**
 * Extended Mysqli adapter that adds 2 new functions for using the mysql
 * binaries.
 *
 * @package DbPatch
 * @subpackage Db_Adapter
 * @author Sandy Pleyte
 * @author Martijn de Letter
 * @copyright 2011 Sandy Pleyte
 * @copyright 2010-2011 Martijn de Letter
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link http://www.github.com/sndpl/DbPatch
 * @since File available since Release 1.0.0
 */
class DbPatch_Db_Adapter_Mysqli extends Zend_Db_Adapter_Mysqli
{
    /**
     * @var string
     */
    protected $_bin_dir = '';


    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        if (isset($config['bin_dir'])) {
            $this->_bin_dir = $config['bin_dir'];
        }
        parent::__construct($config);
    }

    /**
     * Import SQL files with Mysql Binary
     * @throws exception
     * @param string $filename
     * @return bool
     */
    public function import($filename)
    {
        $commandArgs = $this->getShellCommandArgs();
        $filename = escapeshellarg($filename);

        $command = sprintf(
            "mysql %s < %s 2>&1",
            $commandArgs,
            $filename
        );

        exec($command, $result, $return);
        var_dump($result);
        if ($return <> 0) {
            throw new exception(
                'Error importing file ' .
                $filename .
                "\n" .
                implode(PHP_EOL, $result)
            );
        }
        return true;

    }

    /**
     * Dump database with MysqlDump binary
     * @param string $filename
     * @return bool
     */
    public function dump($filename)
    {
        $commandArgs = $this->getShellCommandArgs();
        $filename = escapeshellarg('./' . $filename);

        $command = sprintf(
            "mysqldump %s > %s 2>&1",
            $commandArgs,
            $filename
        );

        exec($command, $result, $return);
        if ($return <> 0) {
            throw new exception(
                'Error dumping file ' .
                $filename .
                "\n" .
                implode(PHP_EOL, $result)
            );
        }
        return true;
    }

    /**
     * Get all shell command args
     * @return string
     */
    protected function getShellCommandArgs()
    {
        $database   = '';
        if (isset($this->_config['dbname']) && $this->_config['dbname']) {
            $database = escapeshellarg($this->_config['dbname']);
        }

        $user = '';
        if (isset($this->_config['username']) && $this->_config['username']) {
            $user = '-u ' . escapeshellarg($this->_config['username']);
        }

        $password = '';
        if (isset($this->_config['password']) && $this->_config['password']) {
            $password = '-p' . escapeshellarg($this->_config['password']);
        }

        $host = '';
        if (isset($this->_config['host']) && $this->_config['host']) {
            $host = '-h ' . escapeshellarg($this->_config['host']);
        }

        $port = '';
        if (isset($this->_config['port']) && $this->_config['port']) {
            $port = '-P' . (int)$this->_config['port'];
        }

        $charset = '';
        if (isset($this->_config['charset']) && $this->_config['charset']) {
            $charset = '--default-character-set=' .
                       escapeshellarg($this->_config['charset']);
        }

        return sprintf(
            '%s %s %s %s %s %s',
            $host,
            $user,
            $password,
            $port,
            $charset,
            $database
        );

    }
}
