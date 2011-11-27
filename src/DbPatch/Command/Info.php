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
 * @subpackage Command
 * @author Sandy Pleyte
 * @author Martijn De Letter
 * @copyright 2011 Sandy Pleyte
 * @copyright 2010-2011 Martijn De Letter
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link http://www.github.com/dbpatch/DbPatch
 * @since File available since Release 1.0.0
 */

/**
 * Show patch command
 *
 * @package DbPatch
 * @subpackage Command
 * @author Sandy Pleyte
 * @author Martijn De Letter
 * @copyright 2011 Sandy Pleyte
 * @copyright 2010-2011 Martijn De Letter
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link http://www.github.com/dbpatch/DbPatch
 * @since File available since Release 1.0.0
 */
class DbPatch_Command_Info extends DbPatch_Command_Abstract
{

    /**
     * Override init function, don't check for changelog
     * @return DbPatch_Command_Show
     */
    public function init()
    {
        return $this;
    }

    /**
     * @return void
     */
    public function execute()
    {
        $this->writer->version();
        $this->writer->line();

        $this->writer->line('Global settings');
        $this->writer->separate();
        $this->writer->line('Default branch: ' . $this->config->default_branch);
        $this->writer->line('Patch directory: ' . $this->config->patch_directory);
        $this->writer->line('Patchfile prefix: ' . $this->config->patch_prefix);
        $this->writer->line('Use color: ' . ($this->config->color?'yes':'no'));
        $this->writer->line('Dump database before update: ' . ($this->config->dump_before_update?'yes':'no'));
        $this->writer->line('Dump directory: ' . $this->config->dump_directory);

        $this->writer->line();
        $this->writer->line('Database settings');
        $this->writer->separate();
        $this->writer->line('Database adapter: ' . $this->config->db->adapter);
        $this->writer->line('Host: ' . $this->config->db->params->host);
        $this->writer->line('Username: ' . $this->config->db->params->dbname);
        $this->writer->line('Password: ' . $this->config->db->params->username);
        $this->writer->line('Database: ' . $this->config->db->params->password);
        $this->writer->line('Charset: ' . $this->config->db->params->charset);
        $this->writer->line('Bin directory: ' . $this->config->db->params->bin_dir);

        if (isset($this->config->s3)) {
            $this->writer->line();
            $this->writer->line('S3 settings');
            $this->writer->separate();
            $this->writer->line('AWS key: ' . $this->config->s3->aws_key);
            $this->writer->line('AWS secret key: ' . $this->config->s3->aws_secret_key);
            $this->writer->line('AWS bucket: ' . $this->config->s3->aws_bucket);
        }
        $this->writer->line();
        return;
    }

}
