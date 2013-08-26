<?php
/**
 * DbPatch
 *
 * Copyright (c) 2011, Sandy Pleyte.
 * Copyright (c) 2010-2011, Martijn De Letter.
 * Copyright (c) 2013, Rudi de Vries
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
 * @author Rudi de Vries
 * @copyright 2013 Rudi de Vries
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link http://www.github.com/dbpatch/DbPatch
 * @since File available since Release 1.1.2
 */

/**
 * Update command
 *
 * @package DbPatch
 * @subpackage Command
 * @author Rudi de Vries
 * @copyright 2013 Rudi de Vries
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link http://www.github.com/dbpatch/DbPatch
 * @since File available since Release 1.1.2
 */
class DbPatch_Command_Print extends DbPatch_Command_Update
{
	/**
     * @return void
     */
    public function execute()
    {
        $branch = $this->getBranch();
        $force = ($this->console->issetOption('force')) ? true : false;
        $addToChangelog = ($this->console->getOptionValue('add-to-changelog')) ? true : false;

        if ($branch != self::DEFAULT_BRANCH) {
            $this->writer->line('Branch: ' . $branch);
        }

        $latestPatchNumber = $this->getLastPatchNumber($branch);

        $patchFiles = $this->getPatches($branch);

        if (count($patchFiles) == 0) {
            $this->writer->success("no update needed " . ($branch != self::DEFAULT_BRANCH ? 'for branch ' . $branch : ''));
            return;
        }

        $this->writer->line(sprintf('found %d patch %s',
            count($patchFiles),
            (count($patchFiles) == 1) ? 'file' : 'files'
        ));

        $patchNumbersToSkip = $this->getPatchNumbersToSkip($this->console->getOptions(), $patchFiles);

        if (count($patchNumbersToSkip)) {
            $this->writer->line('Skip patchnumbers: ' . implode(',', $patchNumbersToSkip));
        }

        foreach ($patchFiles as $patchNr => $patchFile)
        {
            if (($patchFile->patch_number <> $latestPatchNumber + 1) && !$force) {
                $this->writer->error(
                    sprintf('expected patch number %d instead of %d (%s). Use --force to override this check.',
                        $latestPatchNumber + 1,
                        $patchFile->patch_number,
                        $patchFile->basename
                    )
                );
                return;
            }

            if (in_array($patchNr, $patchNumbersToSkip)) {
                $this->writer->line('manually skipped patch ' . $patchFile->basename);
                $this->addToChangelog($patchFile, 'manually skipped');
                $latestPatchNumber = $patchFile->patch_number;
                continue;
            }

            $this->writer->line('#Patch: ' . $patchFile->filename);
            $this->writer->line($patchFile->getContents());

            if ($addToChangelog) {
                $this->addToChangelog($patchFile);
            }

            $latestPatchNumber = $patchFile->patch_number;
        }
    }

    /**
     * @param string $command Command name
     * @return void
     */
    public function showHelp($command = 'print')
    {
        parent::showHelp($command);
        $writer = $this->getWriter();
        $writer
        ->indent(2)->line('--skip=<int>       One or more patchnumbers seperated by a comma to skip')
        ->indent(2)->line('--force            Force the update, and ignore missing patches')
        ->indent(2)->line('--add-to-changelog Add printed patches to changelog')
        ->line();
    }
}