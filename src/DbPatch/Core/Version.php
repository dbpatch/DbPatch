<?php
/**
 * Created by JetBrains PhpStorm.
 * User: sandy
 * Date: 08-05-11
 * Time: 19:49
 * To change this template use File | Settings | File Templates.
 */
 
class DbPatch_Core_Version
{
    const VERSION = '1.0.0';

    static public function checkExact($version)
    {
        return (substr(self::VERSION, 0, strlen($version)) == $version);
    }

    static public function checkMinimal($version)
    {
        return version_compare(self::VERSION, $version, '>=');
    }
}