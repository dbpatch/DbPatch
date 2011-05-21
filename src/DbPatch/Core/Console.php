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
        if (count($argv) > 0 ) {
            $this->arguments = array_slice($argv,1);
            $this->parseOptions();
        }
    }

    protected function parseOptions()
    {
        $options = array();
        foreach ($this->arguments as $argument) {
            if (substr($argument, 0, 2) == '--') {

                $tmpArg = explode('=', $argument);
                $argName = strtolower(substr($tmpArg[0],2));
                $argValue = '';
                if (isset($tmpArg[1])) {
                    $argValue = $tmpArg[1];
                }
                $options[$argName] = $argValue;
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
        $task = $this->arguments[0];

        if (strpos($task, '--') === false) {
            return $task;
        }
        return '';
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

    public function getOptionValue($option, $default = '')
    {
        if (array_key_exists($option, $this->options)) {
            return $this->options[$option];
        }
        return $default;
    }

    public function issetOption($option) {
        return array_key_exists($option, $this->options);
    }
}