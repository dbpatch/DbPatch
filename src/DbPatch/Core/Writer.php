<?php
/**
 * Outputs/Formats messages to the console
 */
class Dbpatch_Core_Writer
{
    public function output($message='')
    {
        echo $message;
        return $this;
    }

    public function line($message='')
    {
        $this->output($message."\n");
        return $this;
    }

    public function separate()
    {
        $this->line('----------------------------------');
        return $this;
    }

    public function indent($spaces = 4)
    {
        $this->output(str_repeat(' ', $spaces));
        return $this;
    }
}
