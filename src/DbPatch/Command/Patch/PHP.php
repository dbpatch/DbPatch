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
 * @subpackage Command_Patch
 * @author Sandy Pleyte
 * @author Martijn De Letter
 * @author Rudi de Vries
 * @copyright 2011 Sandy Pleyte
 * @copyright 2010-2011 Martijn De Letter
 * @copyright 2013 Rudi de Vries
 * @license http://www.opensource.org/licenses/MIT MIT License
 * @link http://www.github.com/dbpatch/DbPatch
 * @since File available since Release 1.0.0
 */

/**
 * PHP Patch file
 *
 * @package DbPatch
 * @subpackage Command_Patch
 * @author Sandy Pleyte
 * @author Martijn De Letter
 * @author Rudi de Vries
 * @copyright 2011 Sandy Pleyte
 * @copyright 2010-2011 Martijn De Letter
 * @copyright 2013 Rudi de Vries
 * @license http://www.opensource.org/licenses/MIT MIT License
 * @link http://www.github.com/dbpatch/DbPatch
 * @since File available since Release 1.0.0
 */
class DbPatch_Command_Patch_PHP extends DbPatch_Command_Patch_Abstract
{
    /**
     * @var array
     */
    protected $data = array(
        'filename' => null,
        'basename' => null,
        'patch_number' => null,
        'branch' => null,
        'description' => null,
    );

    /**
     * Apply the PHP Patch
     *
     * @return bool
     */
    public function apply()
    {

        $writer = $this->getWriter();
        $phpFile = $this->filename;

        $writer->line('apply patch: ' . $this->basename);

        if (!file_exists($phpFile)) {
            $this->getWriter()->line(sprintf('php file %s doesn\'t exists', $phpFile));
            return false;
        }

        $env = new DbPatch_Command_Patch_PHP_Environment();
        $env->setDb($this->getDb()->getAdapter())
            ->setWriter($this->getWriter())
            ->setConfig($this->getConfig())
            ->install($phpFile);

        return true;
    }

    /**
     * @return string
     */
    public function getContents()
    {
        $content = file_get_contents($this->filename);
        if ($content == '') {
            $this->writer->error(
                sprintf('patch file %s is empty', $this->basename)
            );
            return false;
        }

        return $content;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'PHP';
    }

    /**
     * Return the first line (after <?php)
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->getComment(1);
    }

    /**
     * Create PHP Patch file
     *
     * @param string $description
     * @param string $patchDirectory
     * @param string $patchPrefix
     * @return void
     */
    public function create($description, $patchDirectory, $patchPrefix)
    {
        $patchNumberSize = $this->getPatchNumberSize($patchDirectory);
        $filename = $this->getPatchFilename($patchPrefix, strtolower($this->getType()), $patchNumberSize);
        $content = '<?php' . PHP_EOL . '// ' . $description . PHP_EOL;
        $this->writeFile($patchDirectory . '/' . $filename, $content);
    }
}

