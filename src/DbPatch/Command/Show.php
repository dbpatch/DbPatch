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
class DbPatch_Command_Show extends DbPatch_Command_Abstract
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
        if ($this->console->issetOption('patch')) {
            $patchNumber = $this->console->getOptionValue('patch', null);
            if (!is_null($patchNumber) && is_numeric($patchNumber)) {

                $this->showPatch($patchNumber);
                return;
            }
        }
        $this->writer->error('No patch defined or patch isn\'t numeric');
        return;
    }

    /**
     * @param int $patchNumber
     * @return void
     */
    public function showPatch($patchNumber)
    {
        $branch = $this->getBranch();
        $patch = $this->getPatch($patchNumber, $branch);

        if ($patch == null) {
            $this->writer->error("no patchfile found for patch number: " . $patchNumber);
            return;
        }

        $this->writer
                ->line("show patch $patchNumber (" . $patch->basename . "):")
                ->separate();
        $patch->show();
        return;
    }

    /**
     * @return void
     */
    public function showHelp($command = 'show')
    {
        parent::showHelp($command);

        $writer = $this->getWriter();
        $writer->indent(2)->line('--patch=<int>      Patchnumber to show')
                ->line();
    }

}
