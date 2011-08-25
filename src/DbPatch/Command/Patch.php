<?php
class DbPatch_Command_Patch
{
    static public function factory($type)
    {
        $class = 'DbPatch_Command_Patch_'.strtoupper($type);
        return new $class;
    }
}
