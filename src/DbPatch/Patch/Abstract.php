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
 * @subpackage Patch
 * @author Sandy Pleyte
 * @author Martijn De Letter
 * @copyright 2011 Sandy Pleyte
 * @copyright 2010-2011 Martijn De Letter
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link http://www.github.com/dbpatch/DbPatch
 * @since File available since Release 1.1.0
 */

/**
 * Abstract PHP Patch file
 *
 * @package DbPatch
 * @subpackage Patch
 * @author Sandy Pleyte
 * @author Martijn De Letter
 * @copyright 2011 Sandy Pleyte
 * @copyright 2010-2011 Martijn De Letter
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link http://www.github.com/dbpatch/DbPatch
 * @since File available since Release 1.1.0
 */
abstract class DbPatch_Patch_Abstract
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
     * @abstract
     * Install the php patch
     */
    abstract public function install();

    /**
     * @abstract
     * Rollback the php patch
     */
    abstract public function rollback();

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


}