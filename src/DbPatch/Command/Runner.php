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
 * Handle all the available commands
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
class DbPatch_Command_Runner
{
    /**
     * @var \DbPatch_Core_Writer|null
     */
    protected $writer = null;

    /**
     * @static
     * @return array
     */
    static public function getValidCommands()
    {
        return array(
            'help', 'create', 'remove', 'show',
            'status', 'sync', 'update', 'dump'
        );

    }

    /**
     * @param DbPatch_Core_Writer $writer
     */
    public function __construct(DbPatch_Core_Writer $writer)
    {
        $this->writer = $writer;
    }

    /**
     * @return DbPatch_Core_Writer
     */
    public function getWriter()
    {
        return $this->writer;
    }

    /**
     * @throws Exception
     * @param string $command
     * @param DbPatch_Core_Console $console
     * @return DbPatch_Command_Abstract
     */
    public function getCommand($command, $console)
    {
        if (empty($command) || !in_array($command, self::getValidCommands())) {
            throw new Exception('Please provide a valid command');
        }

        $class = 'DbPatch_Command_' . ucfirst(strtolower($command));

        try {
            $command = new $class;
            $command->setWriter($this->getWriter())
                    ->setConsole($console);

        } catch (Exception $e) {
            throw new Exception('Unknown command: ' . $command);
        }
        return $command;
    }

    /**
     * Show help options of DbPatch
     * 
     * @return void
     */
    public function showHelp()
    {
        $writer = $this->getWriter();
        $writer->line()->version();
        $writer->line('usage: dbpatch [--version] [--help] [--config=<file>] [--color] <command> [<args>]')
                ->line()
                ->line('The commands are:')
                ->indent(2)->line('update     execute the patches')
                ->indent(2)->line('create     create empty patch file')
                ->indent(2)->line('remove     remove a patch file from the changelog')
                ->indent(2)->line('sync       sync the changelog with the current patch files')
                ->indent(2)->line('show       show the contents of a patch file')
                ->indent(2)->line('status     show latest applied patches')
                ->indent(2)->line('dump       dump database')
                ->line()
                ->line('see \'dbpatch help <command>\' for more information on a specific command');
    }
}
