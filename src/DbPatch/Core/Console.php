<?php
/**
 * The class interacts with the console
 */
class DbPatch_Core_Console
{
    protected $arguments = array();

    protected $options = array();

    public function __construct($argv)
    {
        $this->arguments = array_slice($argv,1);
        $this->parseOptions();
    }

    protected function parseOptions()
    {
        $options = array();
        foreach ($this->arguments as $argument) {
            if (substr($argument, 0, 2) == '--') {
                $options[] = strtolower(substr($argument,2));
            }
        }
        $this->options = $options;
    }

    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Returns the task parameter from the command line
     *
     * @return string
     */
    public function getTask()
    {
        return $this->arguments[0];
    }

    /**
     * Return the options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}