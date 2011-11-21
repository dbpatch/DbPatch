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
 * @link http://www.github.com/dbpatch/DbPatch
 * @since File available since Release 1.0.0
 */

/**
 * Create patch command
 * 
 * @package DbPatch
 * @subpackage Command
 * @author Sandy Pleyte
 * @author Martijn de Letter
 * @copyright 2011 Sandy Pleyte
 * @copyright 2010-2011 Martijn de Letter
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link http://www.github.com/dbpatch/DbPatch
 * @since File available since Release 1.0.0
 */
class DbPatch_Command_Create extends DbPatch_Command_Abstract
{
    /**
     * Create empty patch file
     *
     * @throws DbPatch_Exception
     * @return void
     */
    public function execute()
    {
        $type = $this->console->getOptionValue('type', null);
        $patchNumber = $this->console->getOptionValue('number', null);

        if (is_null($type) || !in_array(strtolower($type), array('php', 'sql'))) {
            throw new DbPatch_Exception('Invalid patch type!');
        }

        $patch = DbPatch_Command_Patch::factory($type);
        $patch->setWriter($this->getWriter());

        if (is_null($patchNumber)) {
            $branch = $this->getBranch();
            $patches = $this->getPatches($branch, '*');

            if (count($patches)) {
                $lastPatch = end($patches);
                $patchNumber = $lastPatch->patchNumber + 1;
            } else {
                $patchNumber = 1;
            }
        }

        $description = $this->console->getOptionValue('description', 'Empty Patch');

        $patch->patchNumber = $patchNumber;
        $patch->branch = $this->getBranch();

        $patch->create($description, $this->getPatchDirectory(), $this->getPatchPrefix());
    }

    /**
     * @return void
     */
    public function showHelp()
    {
        parent::showHelp('create');
        $writer = $this->getWriter();
        $writer->indent(2)->line('--type=<type>      create patch of the type `php` or `sql`')
                ->indent(2)->line('--number=<int>     Patchnumber to create')
                ->line();
    }
}
