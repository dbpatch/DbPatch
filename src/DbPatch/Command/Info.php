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
 * @license http://www.opensource.org/licenses/MIT MIT License
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
 * @license http://www.opensource.org/licenses/MIT MIT License
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
        $this->writer->line('Global settings')
            ->separate()
            ->line('Default branch: ' . $this->config->default_branch)
            ->line('Patch directory: ' . $this->config->patch_directory)
            ->line('Patchfile prefix: ' . $this->config->patch_prefix)
            ->line('Use color: ' . ($this->config->color ? 'yes' : 'no'))
            ->line('Dump database before update: ' . ($this->config->dump_before_update ? 'yes' : 'no'))
            ->line('Dump directory: ' . $this->config->dump_directory)
            ->line('Debug mode: ' . ($this->config->debug ? 'on' : 'off'))
            ->line()
            ->line('Database settings')
            ->separate()
            ->line('Database adapter: ' . get_class($this->getDb()->getAdapter()));

            $this->dumpArray($this->getDb()->getAdapter()->getConfig());

        if (isset($this->config->db->params->bin_dir)) {
            $this->writer->line('Bin directory: ' . $this->config->db->params->bin_dir);
        }
        if (isset($this->config->cli_cmd_import)) {
            $this->writer->line('Argument template CLI client: ' . $this->config->cli_cmd_import);
        }
        if (isset($this->config->cli_cmd_dump)) {
            $this->writer->line('Argument template CLI dump: ' . $this->config->cli_cmd_dump);
        }

        $this->writer->line();
        return;
    }

    /**
     * Writes array like ['foo' => 'bar'] as "Foo: bar"
     *
     * @param array $data
     * @param int $indent
     */
    public function dumpArray(array $data, $indent = 0)
    {
        foreach ($data as $key => $value) {
            if (empty($value)) {
                continue;
            }

            if ($key == 'password') {
                $value = '[hidden]';
            }

            $this->writer->indent($indent);

            if (is_array($value)) {
                $this->writer->line(
                    ucfirst($key) . ':'
                );

                $this->dumpArray($value, $indent+1);
            } else {
                $this->writer->line(
                    ucfirst($key) . ': ' . $value
                );
            }
        }

        return $this;
    }

    public function showHelp($command = 'info')
    {
        parent::showHelp($command);
    }


}
