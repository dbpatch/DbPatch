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
 * @subpackage Core
 * @author Sandy Pleyte
 * @author Martijn De Letter
 * @copyright 2011 Sandy Pleyte
 * @copyright 2010-2011 Martijn De Letter
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link http://www.github.com/dbpatch/DbPatch
 * @since File available since Release 1.0.0
 */

/**
 * Outputs/Formats messages to the console
 *
 * @package DbPatch
 * @subpackage Core
 * @author Sandy Pleyte
 * @author Martijn De Letter
 * @copyright 2011 Sandy Pleyte
 * @copyright 2010-2011 Martijn De Letter
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link http://www.github.com/dbpatch/DbPatch
 * @since File available since Release 1.0.0
 */
class DbPatch_Core_Writer
{
    /**
     * @var DbPatch_Core_Color
     */
    protected $_color = null;

    /**
     * Writer uses ANSI coloring when color object provided
     *
     * @param DbPatch_Core_Color $color
     * @return DbPatch_Core_Writer
     */
    public function setColor(DbPatch_Core_Color $color)
    {
        $this->_color = $color;

        return $this;
    }

    /**
     * Outputs a message to the console
     *
     * @param string $message
     * @param resource $stream OPTIONAL, writes to
     *                 standard output by default
     * @return DbPatch_Core_Writer
     */
    public function output($message = '', $stream = null)
    {
        if ($stream === null) {
            $stream = STDOUT;
        }

        fwrite($stream, $message);

        return $this;
    }

    /**
     * Outputs a message with a new line
     * @param string $message
     * @return DbPatch_Core_Writer
     */
    public function line($message = '')
    {
        return $this->info($message);
    }

    /**
     * Write an info message
     *
     * @param string $message
     * @return DbPatch_Core_Writer
     */
    public function info($message)
    {
        $this->_message($message, 'info');
        return $this;
    }

    /**
     * Write an error messages
     *
     * @param  string $message
     * @return DbPatch_Core_Writer
     */
    public function error($message)
    {
        return $this->_message('ERROR: ' . $message, 'error');
    }

    /**
     * Write an success messages
     *
     * @param  string $message
     * @return DbPatch_Core_Writer
     */
    public function success($message)
    {
        return $this->_message('SUCCESS: ' . $message, 'success');
    }

    /**
     * Write an warning messages
     *
     * @param  string $message
     * @return DbPatch_Core_Writer
     */
    public function warning($message)
    {
        return $this->_message('WARNING: ' . $message, 'warning');
    }

    /**
     * Separate the output by outputting a dashed line
     *
     * @return DbPatch_Core_Writer
     */
    public function separate()
    {
        $this->line('----------------------------------');
        return $this;
    }

    /**
     * Outputs the version of DbPatch
     *
     * @return DbPatch_Core_Writer
     */
    public function version()
    {
        $this->line('DbPatch version ' . DbPatch_Core_Version::VERSION);
        return $this;
    }

    /**
     * Indent output with spaces
     *
     * @param int $spaces
     * @return DbPatch_Core_Writer
     */
    public function indent($spaces = 4)
    {
        $this->output(str_repeat(' ', $spaces));
        return $this;
    }


    /**
     * Write an optionally colored message
     * 
     * @param string $message
     * @param string $pallet
     * @return DbPatch_Core_Writer
     */
    public function _message($message, $pallet = '')
    {
        if ($this->_color !== null && $pallet != '') {
            $message = $this->_color->pallet($pallet)
                       . $message . $this->_color->reset();
        }
        $message .= PHP_EOL;

        $stream = null;

        if ($pallet != 'info') {
            $stream = STDERR;
        }
        $this->output($message, $stream);

        return $this;
    }
}
