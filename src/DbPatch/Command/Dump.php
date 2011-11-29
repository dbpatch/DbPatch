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
 * Dump database command
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
class DbPatch_Command_Dump extends DbPatch_Command_Abstract
{
    /**
     * @return void
     */
    public function execute()
    {
        $filename = $this->getDumpFilename($this->config);
        $database = $this->config->db->params->dbname;

        $moveToS3 = ($this->console->issetOption('s3')) ? true : false;

        $this->writer->line('Dumping database ' . $database . ' to file ' . $filename);
        $this->dumpDatabase($filename);

        if ($moveToS3) {
            $this->moveDumpToS3($filename);
        }
        return;
    }

    /**
     * Validates the S3 settings
     *
     * @param array $config
     * @return void
     */
    protected function validateS3Settings($config)
    {
        if (!$config->aws_key) {
            throw new DbPatch_Command_ConfigurationException('AWS s3 key config setting isn\'t available');
        }

        if (!$config->aws_secret_key) {
            throw new DbPatch_Command_ConfigurationException('AWS s3 secret key config setting isn\'t available');
        }

        if (!$config->aws_bucket) {
            throw new DbPatch_Command_ConfigurationException('AWS s3 secret key config setting isn\'t available');
        }
    }

    /**
     * Move the dump file to a Amazon S3 bucket
     *
     * @param string $filename
     * @return void
     */
    protected function moveDumpToS3($filename)
    {
        $s3Config = $this->config->s3;

        $this->validateS3Settings($s3Config);

        $s3File = $s3Config->aws_bucket . '/' . $filename;


        $this->writer->line('Copy ' . $filename . ' -> Amazon S3: ' . $s3File);

        $s3 = new Zend_Service_Amazon_S3($s3Config->aws_key, $s3Config->aws_secret_key);
        // use https for uploading
        $s3->setEndpoint('https://' . Zend_Service_Amazon_S3::S3_ENDPOINT);
        $s3->putObject($s3Config->aws_bucket . '/' . $filename, file_get_contents($filename));
        $s3->getObject($s3Config->aws_bucket . '/' . $filename);
    }

    /**
     * Prevent db_changelog creation
     * @return DbPatch_Command_Dump
     */
    public function init()
    {
        return $this;
    }

    /**
     * @return void
     */
    public function showHelp($command = 'dump')
    {
        parent::showHelp($command);

        $writer = $this->getWriter();
        $writer->indent(2)->line('--file=<string>    Filename')
            ->indent(2)->line('--s3               Copy the file to S3 (add S3 credentials to the config)')
            ->line();
    }
}
