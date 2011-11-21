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
 * @subpackage Command_Patch
 * @author Sandy Pleyte
 * @author Martijn de Letter
 * @copyright 2011 Sandy Pleyte
 * @copyright 2010-2011 Martijn de Letter
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link http://www.github.com/dbpatch/DbPatch
 * @since File available since Release 1.0.0
 */

/**
 * Abstract Patch file
 * 
 * @package DbPatch
 * @subpackage Command_Patch
 * @author Sandy Pleyte
 * @author Martijn de Letter
 * @copyright 2011 Sandy Pleyte
 * @copyright 2010-2011 Martijn de Letter
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link http://www.github.com/dbpatch/DbPatch
 * @since File available since Release 1.0.0
 */
abstract class DbPatch_Command_Patch_Abstract
{

    /**
     * @var null|\Zend_Db_Adapter_Abstract
     */
    protected $db = null;

    /**
     * @var null|DbPatch_Core_Writer
     */
    protected $writer = null;

    /**
     * @var null|DbPatch_Core_Config
     */
    protected $config = null;

    /**
     * @var array
     */
    protected $data = array();

    /**
     * Creates a new value object
     * 
     * @param array $values the values to fill the value object with.
     * If left empty we're creating an empty value object.
     * @return void
     */
    public function __construct(Array $values = null)
    {
        if (!is_null($values)) {
            $this->loadFromArray($values);
        }
    }

    /**
     * @abstract
     * @return void
     */
    abstract function apply();

    /**
     * @abstract
     * @return void
     */
    abstract function getType();

    /**
     * Load the values from an array provided.
     *
     * @throws DbPatch_Exception
     * @param array $values the values we're using to set the values in the
     * value object
     * @return void
     */
    public function loadFromArray($values)
    {
        if ($values instanceof Zend_Db_Table_Row_Abstract) {
            $values = $values->toArray();
        } elseif (is_object($values)) {
            $values = (array)$values;
        }

        if (!is_array($values)) {
            throw new DbPatch_Exception('Initial data must be an array or object');
        }

        foreach ($values as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Sets the value provided.
     *
     * @param string $name name of the property
     * @param mixed $value the value
     * @throws DbPatch_Exception
     */
    public function __set($name, $value)
    {
        $name = trim($name);
        $method = 'set' . ucfirst($this->to_camel_case($name));

        if (method_exists($this, $method)) {
            $this->$method($value);
            return;
        }

        $key = $this->from_camel_case($name);

        if (array_key_exists($key, $this->data)) {
            $this->data[$key] = $value;
            return;
        }

        throw new DbPatch_Exception('[SET] Property ' . $name . ' is not implemented for class ' . get_class($this));
    }

    /**
     * Translates a camel case string into a string with
     * underscores (e.g. firstName -&gt; first_name)
     *
     * @param string $str String in camel case format
     * @return string $str Translated into underscore format
     */
    protected function from_camel_case($str)
    {
        $str[0] = strtolower($str[0]);
        $func = create_function('$c', 'return "_" . strtolower($c[1]);');
        return preg_replace_callback('/([A-Z])/', $func, $str);
    }

    /**
     * Translates a string with underscores into camel case
     * (e.g. first_name -&gt; firstName)
     *
     * @param string $str String in underscore format
     * @param bool $capitalise_first_char If true, capitalise the first char in $str
     * @return string $str translated into camel caps
     */
    protected function to_camel_case($str, $capitalise_first_char = false)
    {
        if ($capitalise_first_char) {
            $str[0] = strtoupper($str[0]);
        }
        $func = create_function('$c', 'return strtoupper($c[1]);');
        return preg_replace_callback('/_([a-z])/', $func, $str);
    }

    /**
     * Returns the value requested.
     *
     * @throws DbPatch_Exception
     * @param string $name the name of the property
     * @return mixed the value
     */
    public function __get($name)
    {
        $name = trim($name);
        $method = 'get' . ucfirst($this->to_camel_case($name));

        if (method_exists($this, $method)) {
            return $this->$method();
        }

        $key = $this->from_camel_case($name);

        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        throw new DbPatch_Exception('[GET] Property ' . $name . '/' . $key . ' is not implemented for class ' . get_class($this));
    }

    /**
     * Check if a value isset
     * 
     * @throws DbPatch_Exception
     * @param string $name Name of the property
     * @return bool
     */
    public function __isset($name)
    {
        $name = trim($name);

        $method = 'get' . ucfirst($this->to_camel_case($name));

        if (method_exists($this, $method)) {
            $val = $this->$method();
            return isset($val);
        }

        $key = $this->from_camel_case($name);

        if (array_key_exists($key, $this->data)) {
            return isset($this->data[$key]);
        }

        throw new DbPatch_Exception('[ISSET] Property ' . $name . ' is not implemented for class ' . get_class($this));
    }

    /**
     * Returns the objects values as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * Returns the field names of the data array.
     *
     * @return array
     */
    public function getFields()
    {
        return array_keys($this->data);
    }

    /**
     * @param \Zend_Db_Adapter_Abstract $db
     * @return DbPatch_Command_Patch_Abstract
     */
    public function setDb($db)
    {
        $this->db = $db;
        return $this;
    }

    /**
     * @return null|\Zend_Db_Adapter_Abstract
     */
    public function getDb()
    {
        return $this->db;

    }

    /**
     * @param DbPatch_Core_Writer $writer
     * @return DbPatch_Command_Patch_Abstract
     */
    public function setWriter($writer)
    {
        $this->writer = $writer;
        return $this;
    }

    /**
     * @return DbPatch_Core_Writer|null
     */
    public function getWriter()
    {
        return $this->writer;
    }

    /**
     * @param DbPatch_Core_Config $config
     * @return DbPatch_Command_Patch_Abstract
     */
    public function setConfig($config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @return DbPatch_Core_Config|null
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Extract the first line of comment out of a patch file
     *
     * @param int $lineNumber Line number where the comment is
     * @return string
     */
    protected function getComment($lineNumber)
    {
        $lines = file($this->filename);
        $line = $lines[$lineNumber];
        $comment = '';

        $pattern = '/^\s*(\/\/|\/\*|\#|-- )\s?(.*)$/';
        if (preg_match($pattern, $line, $matches)) {
            $comment = trim(str_replace('*/', '', $matches[2]));
            if (substr($comment, 0, 5) == '-----') {
                $comment = '';
            }
        }
        return $comment;
    }

    /**
     * Retrieve a file hash
     *
     * @return string
     */
    public function getHash()
    {
        return hash_file('crc32', $this->filename);
    }

    /**
     * Echo contents of the patch
     * 
     * @return void
     */
    public function show()
    {
        echo "\n" . file_get_contents($this->filename) . "\n";
    }

    /**
     * Create empty patch
     *
     * @param string $filename
     * @param string $content
     * @return void
     */
    protected function writeFile($filename, $content)
    {
        if (!$this->fileExists($filename)) {
            $fp = fopen($filename, 'w');
            fwrite($fp, $content);
            fclose($fp);
            $this->getWriter()->success('Created empty patch ' . $filename);
        } else {
            $this->getWriter()->error('Patch ' . $this->patchNumber . ' already exists!');
        }

    }

    /**
     * @param string $filename
     * @return bool
     */
    protected function fileExists($filename)
    {
        $filename2 = '';
        if ($this->getType() == 'PHP') {
            $filename2 = str_replace('.php', '.sql', $filename);
        } else if ($this->getType() == 'SQL') {
            $filename2 = str_replace('.sql', '.php', $filename);
        }

        return file_exists($filename) || file_exists($filename2);
    }

    /**
     * @param string $patchPrefix
     * @param string $extension
     * @param int $patchNumberSize
     * @return string
     */
    protected function getPatchFilename($patchPrefix, $extension, $patchNumberSize = 4)
    {
        $branch = '';
        if ($this->branch != 'default') {
            $branch .= $this->branch . '-';
        }
        $filename = $patchPrefix . '-' . $branch . str_pad($this->patchNumber, $patchNumberSize, '0', STR_PAD_LEFT) . '.' . $extension;
        return $filename;
    }

    /**
     * @param string $patchDirectory
     * @return int
     */
    protected function getPatchNumberSize($patchDirectory)
    {
        try {
            $iterator = new DirectoryIterator($patchDirectory);
        } catch (Exception $e) {
            $this->writer->error($e->getMessage());
            return 4;
        }

        $filename = '';
        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isDot() || substr($fileinfo->getFilename(), 0, 1) == '.') {
                continue;
            }
            $filename = $fileinfo->getFilename();
            break;
        }

        $pattern = '/(\d{3,4})./';
        if (preg_match($pattern, $filename, $matches)) {
            return strlen($matches[1]);
        }
        return 4;

    }


}
