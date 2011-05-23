<?php
/**
 * Outputs/Formats messages to the console
 */
class Dbpatch_Core_Writer
{
    /**
     * Outputs a message to the console
     * @param string $message
     * @return Dbpatch_Core_Writer
     */
    public function output($message='')
    {
        echo $message;
        return $this;
    }

    /**
     * Outputs a message with a new line
     * @param string $message
     * @return Dbpatch_Core_Writer
     */
    public function line($message='')
    {
        $this->output($message. PHP_EOL);
        return $this;
    }

    /**
     * Write an error messages
     * @param  string $message
     * @return Dbpatch_Core_Writer
     */
    public function error($message)
    {
        $this->line('Error: '.$message);
        return $this;
    }

    /**
     * Separate the output by outputting a dashed line
     * @return Dbpatch_Core_Writer
     */
    public function separate()
    {
        $this->line('----------------------------------');
        return $this;
    }

    /**
     * Indent output with spaces
     * @param int $spaces
     * @return Dbpatch_Core_Writer
     */
    public function indent($spaces = 4)
    {
        $this->output(str_repeat(' ', $spaces));
        return $this;
    }
}
