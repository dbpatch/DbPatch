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
 * The class interacts with the console
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
class DbPatch_Core_Console
{

    /**
     * @var array
     */
    protected $arguments = array();

    /**
     * @var array
     */
    protected $options = array();

    /**
     * @param array $argv
     * @return void
     */
    public function __construct(array $argv)
    {
        if (count($argv) > 0) {
            $this->arguments = array_slice($argv, 1);
            $this->parseOptions();
        }
    }

    /**
     * @return void
     */
    protected function parseOptions()
    {
        $options = array();
        foreach ($this->arguments as $key => $argument) {
            if (substr($argument, 0, 2) == '--') {

                $tmpArg = explode('=', $argument);
                $argName = strtolower(substr($tmpArg[0], 2));
                $argValue = '';
                if (isset($tmpArg[1])) {
                    $argValue = $tmpArg[1];
                }

                // parse boolean values
                switch (strtolower($argValue)) {
                    case 'yes':
                    case 'true':
                    case '':
                        $argValue = true;
                        break;
                    case 'no':
                    case 'false':
                        $argValue = false;
                        break;
                }

                $options[$argName] = $argValue;
            } elseif ($key > 0) {
                $options[$argument] = $argument;
            }
        }
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Returns the command parameter from the command line
     *
     * @return string
     */
    public function getCommand()
    {
        $command = '';

        foreach ($this->arguments as $arg) {
            if (strpos($arg, '--') === false) {
                $command = $arg;
                break;
            }

        }

        return $command;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string $option
     * @param string $default
     * @return string
     */
    public function getOptionValue($option, $default = '')
    {
        if (array_key_exists($option, $this->options)) {
            return $this->options[$option];
        }
        return $default;
    }

    /**
     * @param string $option
     * @return bool
     */
    public function issetOption($option)
    {
        return array_key_exists($option, $this->options);
    }
}
