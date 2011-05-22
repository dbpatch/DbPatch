<?php
class DbPatch_Task_Patch
{
    static public function factory($type)
    {
        $class = 'DbPatch_Task_Patch_'.strtoupper($type);
        return new $class;
    }
}
