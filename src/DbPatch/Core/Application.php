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
 * The core application object.
 * Setup different objects and fireup the command runner
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
class DbPatch_Core_Application
{
    /**
     * Initialize the dbpatch application
     * Typically called from bin/dbpatch.php
     * @return void
     */
    public function main()
    {
        $console = $this->getConsole($_SERVER['argv']);
        $writer = $this->getWriter();
        $runner = $this->getCommandRunner($writer);
        $configFile = $console->getOptionValue('config', null);
        $useColor = $console->getOptionValue('color', false);

        // Show dbpatch version
        $writer->version();
        if ($console->issetOption('version')) {
            return;
        }

        // Load the right config file
        try {
            $config = $this->getConfig($configFile);
            if ($useColor || $config->color) {
                $writer->setColor($this->getWriterColor());
            }
            if(isset($config->debug) && $config->debug) {
                $writer->setDebug($config->debug);
            }

        } catch (Exception $e) {
            $writer->error($e->getMessage())->line();
            $runner->showHelp();
            exit(1);
        }

        $db = $this->getDb($config);

        // Finally execute the right command
        try {
            $command = $console->getCommand();

            if($command == '') {
                $runner->showHelp();
                exit(0);
            }

            $runner->getCommand($command, $console)
                    ->setConfig($config)
                    ->setDb($db)
                    ->init()
                    ->execute();

        } catch (Exception $e) {
            $writer->error($e->getMessage())->line();
            $runner->showHelp();
            exit(1);
        }
        return;
    }

    /**
     * @param DbPatch_Core_Writer $writer
     * @return DbPatch_Command_Runner
     */
    protected function getCommandRunner($writer)
    {
        return new DbPatch_Command_Runner($writer);
    }

    /**
     * @param string $filename
     * @return null|\Zend_Config|\Zend_Config_Ini|\Zend_Config_Xml
     */
    protected function getConfig($filename = null)
    {
        $config = new DbPatch_Core_Config($filename);
        return $config->getConfig();
    }

    /**
     * @param \Zend_Config|\Zend_Config_Ini|\Zend_Config_Xml $config
     * @return null|Zend_Db_Adapter_Abstract
     */
    protected function getDb($config)
    {
        $db = new DbPatch_Core_Db($config);
        return $db->getDb();
    }

    /**
     * @return DbPatch_Core_Writer
     */
    protected function getWriter()
    {
        return new DbPatch_Core_Writer();
    }

    /**
     * @param array $argv
     * @return DbPatch_Core_Console
     */
    protected function getConsole($argv)
    {
        return new DbPatch_Core_Console($argv);
    }

    /**
     * @return DbPatch_Core_Color
     */
    protected function getWriterColor()
    {
        return new DbPatch_Core_Color();
    }
}
