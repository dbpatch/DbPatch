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
 * @since File available since Release 1.1.0
 */

/**
 * Setup command
 * 
 * The only thing this command does is simply copying the right config file from the docs dir to the current dir
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
class DbPatch_Command_Setup extends DbPatch_Command_Abstract
{
    public function execute()
    {
        $type = $this->console->getOptionValue('type', 'ini');

        if (!in_array(strtolower($type), array('ini', 'php', 'xml'))) {
            throw new DbPatch_Exception('Invalid configuration type!');
        }
        
        $skelConfig = realpath(dirname(__FILE__) . '/../../../') . '/docs/dbpatch.' . $type;
        $newConfig = 'dbpatch.'.$type;

        if (!file_exists($skelConfig)) {
            throw new DbPatch_Exception('Invalid configuration skeleton file: '. $skelConfig);
        }

        if (file_exists($newConfig)) {
        	throw new DbPatch_Exception($newConfig .' already exists.');
        }

        if (!copy($skelConfig, $newConfig)) {
        	throw new DbPatch_Exception('failed to copy '. $newConfig);	
        }

        $this->getWriter()->line($newConfig. ' in place.')->line('Use \'vi '. $newConfig.'\' to finalize your dbpatch configuration.');
    }

    /**
     * @return void
     */
    public function showHelp($command = 'setup')
    {
        parent::showHelp($command);
        $writer = $this->getWriter();
        $writer->indent(2)->line('--type=<type>      create a configuration file of the following types `ini`, `php` or `xml`')
                ->line();
    }
}