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
 * Show patch status command
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
class DbPatch_Command_Status extends DbPatch_Command_Abstract
{
    /**
     * @var int
     */
    const LIMIT = 10;

    /**
     * @return void
     */
    public function execute()
    {
        $this->writer->version();

        $branches = $this->detectBranches();

        foreach ($branches as $branch) {
            $this->showPatchesToApply($branch);
        }

        $limit = $this->getLimit();
        $patches = $this->getAppliedPatches();
        $this->getWriter()->line()->line("applied patches (" . $limit . " latest)")->separate();

        if (count($patches) == 0) {
            $this->getWriter()->warning("no patches found")->line();
        } else {
            foreach ($patches as $patch) {
                $this->writer->line(sprintf("%04d | %s | %s | %s",
                                            $patch['patch_number'],
                                            $patch['completed'],
                                            $patch['filename'],
                                            $patch['description']));
            }
        }
    }

    /**
     * Output all the patches to apply for a specific branch
     *
     * @param string $branch
     * return void
     */
    protected function showPatchesToApply($branch)
    {
        $line = 'patches to apply';
        if ($branch <> self::DEFAULT_BRANCH) {
            $line .= " for branch '{$branch}'";
        }
        $this->getWriter()->line($line)->separate();

        $patches = $this->getPatches($branch);

        if (count($patches) == 0) {
            $this->getWriter()->line("no patches found")->line();
        } else {
            foreach ($patches as $patch) {
                $this->getWriter()->line(sprintf("%04d | %s | %s",
                                                 $patch->patchNumber,
                                                 $patch->basename,
                                                 $patch->description));
            }

            $line = "use 'dbpatch update";
            if ($branch <> self::DEFAULT_BRANCH) {
                $line .= " branch={$branch}";
            }
            $line .= "' to apply the patches\n";
            $this->getWriter()->line()->line($line);
        }
    }

    /**
     * @return int
     */
    protected function getLimit()
    {
        $limit = $this->config->get('limit', self::LIMIT);
        return $limit;
    }

    /**
     * Get list of patches that are applied
     * 
     * @param string $branch
     * @return array
     */
    protected function getAppliedPatches($branch = '')
    {
        $db = $this->getDb();

        $where = '';
        if ($branch != '') {
            $where = 'WHERE branch =\'' . $db->escapeSQL($branch) . '\'';
        }
        $limit = $this->getLimit();

        $sql = sprintf("
            SELECT
                `patch_number`,
                `completed`,
                `filename`,
                `description`,
                IF(`branch`='%s',0,1) as `branch_order`
            FROM `%s`
            %s
            ORDER BY `completed` DESC, `branch_order` ASC, `patch_number` DESC
            LIMIT %d",
                       self::DEFAULT_BRANCH,
                       self::TABLE,
                       $where,
                       (int)$limit
        );

        return $db->fetchAll($sql);
    }

    /**
     * @return void
     */
    public function showHelp()
    {
        parent::showHelp('status');
    }


}
