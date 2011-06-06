<?php
/**
 * Outputs/Formats messages to the console
 */
class Dbpatch_Core_Writer
{
    /**
     * @var DbPatch_Core_Color $_color
     */
    protected $_color = null;

    /**
     * Writer uses ANSI coloring when color object provided
     *
     * @param DbPatch_Core_Color $color
     * @return DbPatch_Core_Writer
     */
    public function setColor(DbPatch_Core_Color $color)
    {
        $this->_color = $color;

        return $this;
    }

    /**
     * Outputs a message to the console
     * @param string $message
     * @param resource $stream OPTIONAL, writes to
     *                 standard output by default
     * @return Dbpatch_Core_Writer
     */
    public function output($message='', $stream = null)
    {
        if ($stream === null) {
            $stream = STDOUT;
        }

        fwrite($stream, $message);

        return $this;
    }

    /**
     * Outputs a message with a new line
     * @param string $message
     * @param resource $stream
     * @return Dbpatch_Core_Writer
     */
    public function line($message='')
    {
        return $this->info($message);
    }

    public function info($message)
    {
        $this->_message($message, 'info');
        return $this;
    }

    /**
     * Write an error messages
     * @param  string $message
     * @return Dbpatch_Core_Writer
     */
    public function error($message)
    {
        return $this->_message('ERROR: ' . $message, 'error');
    }

    /**
     * Write an success messages
     * @param  string $message
     * @return Dbpatch_Core_Writer
     */
    public function success($message)
    {
        return $this->_message('SUCCESS: ' . $message, 'success');
    }

    /**
     * Write an warning messages
     * @param  string $message
     * @return Dbpatch_Core_Writer
     */
    public function warning($message)
    {
        return $this->_message('WARNING: ' . $message, 'warning');
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
     * Outputs the version of DbPatch
     * @return Dbpatch_Core_Writer
     */
    public function version()
    {
        $this->line('DbPatch version ' . DbPatch_Core_Version::VERSION);
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


    /**
     * Write an optionally colored messages
     * @param  string $message
     * @param string $pallet
     * @return Dbpatch_Core_Writer
     */
    public function _message($message, $pallet = '')
    {
        if ($this->_color !== null && $pallet != '') {
            $message = $this->_color->pallet($pallet)
                . $message . $this->_color->reset();
        }
        $message .= PHP_EOL;

        $stream = null;

        if ($pallet != 'info') {
            $stream = STDERR;
        }
        $this->output($message, $stream);

        return $this;
    }
}
