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
        foreach ($this->arguments as $key => $argument) {
            if (substr($argument, 0, 2) == '--') {

                $tmpArg = explode('=', $argument);
                $argName = strtolower(substr($tmpArg[0],2));
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

    public function issetOption($option) 
    {
        return array_key_exists($option, $this->options);
    }
}
