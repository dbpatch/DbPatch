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
 * @subpackage Command
 * @author Sandy Pleyte
 * @author Martijn de Letter
 * @copyright 2011 Sandy Pleyte
 * @copyright 2010-2011 Martijn de Letter
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link http://www.github.com/sndpl/DbPatch
 * @since File available since Release 1.0.0
 */

/**
 * Dump database command
 *
 * @package DbPatch
 * @subpackage Command
 * @author Sandy Pleyte
 * @author Martijn de Letter
 * @copyright 2011 Sandy Pleyte
 * @copyright 2010-2011 Martijn de Letter
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link http://www.github.com/sndpl/DbPatch
 * @since File available since Release 1.0.0
 */
class DbPatch_Command_Dump extends DbPatch_Command_Abstract
{
    /**
     * @return void
     */
    public function execute()
    {
        $filename = null;
        $database = $this->config->db->params->dbname;
        if ($this->console->issetOption('file')) {
            $filename = $this->console->getOptionValue('file', null);
        }

        if (is_null($filename)) {
            $filename = $database . '_' . date('Ymd_Hi') . '.sql';
        }

        $this->writer->line('Dumping database ' . $database . ' to file ' . $filename);
        $this->dumpDatabase($filename);
        return;
    }

    /**
     * Dump database
     *
     * @todo Add Character set to mysqldump command
     * @param string $filename
     * @return bool
     */
    protected function dumpDatabase($filename)
    {
        $config = $this->config->db->params;
        $database = $config->dbname;
        $user = $config->username;
        $password = $config->password;
        $host = $config->host;
        $port = isset($config->port) ? $config->port : '';

        if ($password != '') {
            $password = '-p' . $password;
        }

        if ($port != '') {
            $port = '-P' . $port;

        }
        $command = sprintf("mysqldump -h %s -u %s %s %s %s > '%s' 2>&1",
                           $host,
                           $user,
                           $password,
                           $port,
                           $database,
                           $filename
        );

        exec($command, $result, $return);

        if ($return <> 0) {

            $this->writer->error(sprintf("invalid SQL in patch file %s:\n\n%s\n",
                                         $this->data['basename'],
                                         implode(PHP_EOL, $result)
                                 ));
            return false;
        }

        return true;
    }

    /**
     * @return void
     */
    public function showHelp()
    {
        parent::showHelp('dump');

        $writer = $this->getWriter();
        $writer->indent(2)->line('--file=<string>    Filename')
                ->line();
    }
}
