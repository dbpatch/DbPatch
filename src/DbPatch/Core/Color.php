<?php
/**
 * Generate ANSI control characters to
 *   spice up terminal messages
 *
 * <code>
 *  $color = new DbPatch_Core_Color();
 *
 *  // 7 red characters
 *  echo $color->pallet('error'), 'i\'m red',
 *          $color->reset();
 *
 *  // full green line (erase until end of line)
 *  echo $color->pallet('success'), 'i\'m green',
 *          $color->erase(), $color->reset();
 *
 *  // all next lines are bold
 *  echo $color->color('grey', 'bold', 'black');
 * </code>
 */
class Dbpatch_Core_Color
{
    /**
     * List of ANSI color codes
     *
     * @see PEAR Console_Color
     * @var array $_colors
     */
    protected $_colors = array (
        'color' => array(
            'black'  => 30,
            'red'    => 31,
            'green'  => 32,
            'brown'  => 33,
            'blue'   => 34,
            'purple' => 35,
            'cyan'   => 36,
            'grey'   => 37,
            'yellow' => 33
        ),
        'style' => array(
            'normal'     => 0,
            'bold'       => 1,
            'light'      => 1,
            'underscore' => 4,
            'underline'  => 4,
            'blink'      => 5,
            'inverse'    => 6,
            'hidden'     => 8,
            'concealed'  => 8
        ),
        'background' => array(
            'black'  => 40,
            'red'    => 41,
            'green'  => 42,
            'brown'  => 43,
            'yellow' => 43,
            'blue'   => 44,
            'purple' => 45,
            'cyan'   => 46,
            'grey'   => 47
        )
    );

    /**
     * A pallet is a predefined set of fore- and background color and styles
     *
     * @var array $_pallets (name => (fg, style, bg))
     */
    protected $_pallets = array(
        'error'   => array('grey', 'bold', 'red'),
        'success' => array('black', 'bold', 'green'),
        'warning' => array('black', 'bold', 'brown'),
    );

    /**
     * Returns a color code for a predefined set of
     *  fg, bg and style combinations
     * 
     * @param string $pallet One of error, warning, succes, bold
     * @return string color code
     */
    public function pallet($pallet)
    {
        if (!array_key_exists($pallet, $this->_pallets)) {
            return $this->reset();
        }

        return call_user_func_array(
            array($this, 'color'),
            $this->_pallets[$pallet]
        );
    }

    /**
     * @param string $fgColor
     * @param string $style
     * @param string $bgColor
     * @return string color code
     */
    public function color($fgColor, $style, $bgColor)
    {
        $code = implode(';', array(
            $this->_colors['color'][$fgColor],
            $this->_colors['style'][$style],
            $this->_colors['background'][$bgColor],
        ));

        return "\033[{$code}m";
    }

    /**
     * @return string ANSI color reset code
     */
    public function reset($autoErase = true)
    {
        return ($autoErase ? $this->erase() : '')
            . "\033[0m";
    }

    /**
     * @return string erase until EOL code
     */
    public function erase()
    {
        return "\033[K";
    }
}
