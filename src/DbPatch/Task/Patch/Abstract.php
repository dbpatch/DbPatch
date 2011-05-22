<?php
abstract class DbPatch_Task_Patch_Abstract
{

    protected $db = null;

    protected $writer = null;
    
    /**
     * Creates a new value object
     * @param array $values the values to fill the value object with.
     * If left empty we're creating an empty value object.
     */
    public function __construct(Array $values = null)
    {
        if (!is_null($values)) {
            $this->loadFromArray($values);
        }
    }

    abstract function apply();

    /**
     * Load the values from an array provided.
     *
     * @param array $values the values we're using to set the values in the
     * value object
     */
    public function loadFromArray($values)
    {
        if ($values instanceof Zend_Db_Table_Row_Abstract) {
            $values = $values->toArray();
        } elseif (is_object($values)) {
            $values = (array)$values;
        }

        if (!is_array($values)) {
            throw new Exception('Initial data must be an array or object');
        }

        foreach ($values as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Sets the value provided.
     *
     * @param string $name name of the property
     * @param mixed $value the value
     */
    public function __set($name, $value)
    {
        $name = trim($name);
        $method = 'set' . ucfirst($this->to_camel_case($name));

        if (method_exists($this, $method)) {
            $this->$method($value);
            return;
        }

        $key = $this->from_camel_case($name);

        if (array_key_exists($key, $this->data)) {
            $this->data[$key] = $value;
            return;
        }

        throw new Exception('[SET] Property ' . $name . ' is not implemented for class ' . get_class($this));
    }

    /**
     * Translates a camel case string into a string with underscores (e.g. firstName -&gt; first_name)
     * @param    string   $str    String in camel case format
     * @return    string            $str Translated into underscore format
     */
    protected function from_camel_case($str)
    {
        $str[0] = strtolower($str[0]);
        $func = create_function('$c', 'return "_" . strtolower($c[1]);');
        return preg_replace_callback('/([A-Z])/', $func, $str);
    }

    /**
     * Translates a string with underscores into camel case (e.g. first_name -&gt; firstName)
     * @param    string   $str                     String in underscore format
     * @param    bool     $capitalise_first_char   If true, capitalise the first char in $str
     * @return   string                              $str translated into camel caps
     */
    protected function to_camel_case($str, $capitalise_first_char = false)
    {
        if ($capitalise_first_char) {
            $str[0] = strtoupper($str[0]);
        }
        $func = create_function('$c', 'return strtoupper($c[1]);');
        return preg_replace_callback('/_([a-z])/', $func, $str);
    }

    /**
     * Returns the value requested.
     *
     * @param string $name the name of the property
     * @return mixed the value
     */
    public function __get($name)
    {
        $name = trim($name);
        $method = 'get' . ucfirst($this->to_camel_case($name));

        if (method_exists($this, $method)) {
            return $this->$method();
        }

        $key = $this->from_camel_case($name);

        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        throw new Exception('[GET] Property ' . $name . '/' . $key . ' is not implemented for class ' . get_class($this));
    }

    public function __isset($name)
    {
        $name = trim($name);

        $method = 'get' . ucfirst($this->to_camel_case($name));

        if (method_exists($this, $method)) {
            $val = $this->$method();
            return isset($val);
        }

        $key = $this->from_camel_case($name);

        if (array_key_exists($key, $this->data)) {
            return isset($this->data[$key]);
        }

        throw new Exception('[ISSET] Property ' . $name . ' is not implemented for class ' . get_class($this));
    }

    /**
     * Returns the objects values as an array.
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * Returns the field names of the data array.
     * @return array
     */
    public function getFields()
    {
        return array_keys($this->data);
    }

    public function setDb($db)
    {
        $this->db = $db;
    }

    /**
     * @return null|Zend_Db
     */
    public function getDb()
    {
        return $this->db;
    }
    public function setWriter($writer)
    {
        $this->writer = $writer;
    }

    public function getWriter()
    {
        return $this->writer;
    }

    /**
     * Extract the first line of comment out of a patch file
     * @param int $lineNumber Line number where the comment is
     * @return string
     */
    protected function getComment($lineNumber)
    {
        $lines = file($this->filename);
        $line = $lines[$lineNumber];
        $comment = '';

        $pattern = '/^\s*(\/\/|\/\*|\#|-- )\s?(.*)$/';
        if (preg_match($pattern, $line, $matches))
        {
            $comment = trim(str_replace('*/', '', $matches[2]));
            if (substr($comment, 0, 5) == '-----') {
                $comment = '';
            }
        }
        return $comment;
    }

    public function getHash()
    {
        return hash_file('crc32', $this->filename);
    }

    public function show()
    {
        echo "\n".file_get_contents($this->filename)."\n";
    }

}
