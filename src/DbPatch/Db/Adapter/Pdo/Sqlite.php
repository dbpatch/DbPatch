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
 * @subpackage Db_Adapter
 * @author Sandy Pleyte
 * @author Martijn De Letter
 * @copyright 2011 Sandy Pleyte
 * @copyright 2010-2011 Martijn De Letter
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link http://www.github.com/dbpatch/DbPatch
 * @since File available since Release 1.0.0
 */

/**
 * Extended Sqlite adapter that adds new functions for using the sqlite3
 * binaries.
 *
 * @package DbPatch
 * @subpackage Db_Adapter
 * @author Sandy Pleyte
 * @author Martijn De Letter
 * @copyright 2011 Sandy Pleyte
 * @copyright 2010-2011 Martijn De Letter
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link http://www.github.com/dbpatch/DbPatch
 * @since File available since Release 1.0.2
 */
class DbPatch_Db_Adapter_Pdo_Sqlite extends Zend_Db_Adapter_Pdo_Sqlite
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
     * Import SQL files with Sqlite3 Binary
     * @throws DbPatch_Exception
     * @param string $filename
     * @return bool
     */
    public function import($filename)
    {
        $commandArgs = $this->getShellCommandArgs();
        $filename = escapeshellarg($filename);

        $command = "sqlite3";

        if (isset($this->_config['bin_dir']) && $this->_config['bin_dir']) {
            $command = $this->_config['bin_dir'] . '/' . $command;
        }

        $commandLine = sprintf(
            "%s %s < %s 2>&1",
            $command,
            $commandArgs,
            $filename
        );

        exec($commandLine, $result, $return);

        if ($return <> 0) {
            throw new DbPatch_Exception(
                'Error importing file ' .
                    $filename .
                    "\n" .
                    $commandLine .
                    "\n" .
                    implode(PHP_EOL, $result)
            );
        }
        $this->closeConnection();
        return true;

    }

    /**
     * Dump database with Sqlite3 binary
     * @throws DbPatch_Exception
     * @param string $filename
     * @return bool
     */
    public function dump($filename, $noData = false)
    {
        $commandArgs = $this->getShellCommandArgs($noData);
        $filename = escapeshellarg($filename);

        $command = 'sqlite3';

        if (isset($this->_config['bin_dir']) && $this->_config['bin_dir']) {
            $command = $this->_config['bin_dir'] . '/' . $command;
        }
        $commandLine = sprintf(
            "%s %s > %s 2>&1",
            $command,
            $commandArgs,
            $filename
        );

        exec($commandLine, $result, $return);
        if ($return <> 0) {
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
     * Get all shell command args
     * @return string
     */
    protected function getShellCommandArgs($noData = false)
    {
        $database = '';
        if (isset($this->_config['dbname']) && $this->_config['dbname']) {
            $database = escapeshellarg($this->_config['dbname']);
        }

        $data = '.dump';
        if ($noData) {
            $data = '.schema';
        }

        return sprintf(
            '%s %s',
            $database,
            $data
        );

    }

    /**
     * @param string $table Changelog table name
     * @return string
     */
    public function changeLogExists($table)
    {
        $result = $this->fetchOne(
            $this->quoteInto('SELECT `name` FROM `sqlite_master` WHERE `type` = "table" AND `name` = ?', $table)
        );
        return $result;
    }

    /**
     * @param string $table Changelog table name
     */
    public function createChangeLog($table)
    {
        $this->query(
            sprintf("
            CREATE TABLE %s (
            `patch_number` int(11) NOT NULL,
            `branch` varchar(50) NOT NULL,
            `completed` timestamp NOT NULL default CURRENT_TIMESTAMP,
            `filename` varchar(100) NOT NULL,
            `hash` varchar(32) NOT NULL,
            `description` varchar(200) default NULL,
            PRIMARY KEY  (`patch_number`, `branch`)
            )",
                $this->quoteIdentifier($table)
            ));


        $this->query("CREATE TRIGGER insert_".$table."_completed AFTER  INSERT ON ".$table."
                         BEGIN
                          UPDATE ".$table." SET completed = DATETIME('NOW')  WHERE rowid = new.rowid;
                         END;
                     ");


    }

    /**
     * @param string $where Where clause
     * @param string $defaultBranch Default branch
     * @param string $table Change table name
     * @return array
     */
    public function getAppliedPatches($where, $defaultBranch, $table)
    {
        $sql = sprintf("
            SELECT
                `patch_number`,
                `completed`,
                `filename`,
                `description`,
                `hash`,
                case when `branch`='%s' then 0 else 1 end as `branch_order`
            FROM `%s`
            %s
            ORDER BY `completed` DESC, `branch_order` ASC, `patch_number` DESC
            ",
            $defaultBranch,
            $table,
            $where
        );

        return $this->fetchAll($sql);
    }

}