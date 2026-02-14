<?php

/**
 * Manages an Array of values that can be saved in the WP options table.
 */
class WJECF_Options
{
    private $options; // [ 'key' => 'value' ]
    private $defaults = []; // [ 'key' => 'value' ]
    private $option_name;

    /**
     * @param string $option_name
     * @param array  $defaults            Default values
     * @param bool   $auto_reload_on_save If true, the options shall be reloaded after save
     */
    public function __construct($option_name, $defaults = [], $auto_reload_on_save = true)
    {
        $this->option_name = $option_name;
        $this->defaults = $defaults;

        if ($auto_reload_on_save) {
            // reload options directly after save
            add_action('update_option_'.$option_name, [$this, 'invalidate'], 1, 0);
        }
    }

    /**
     * Forces reloading of the options; invalidates the current values.
     */
    public function invalidate()
    {
        $this->options = null;
    }

    /**
     * The option_name used in the WP options table.
     *
     * @return string
     */
    public function get_option_name()
    {
        return $this->option_name;
    }

    /**
     * Get option [ $key ]. If $key is not given, return all options.
     *
     * @param string $key
     * @param mixed  $default The default value to return (only if $key is given)
     *
     * @return mixed The value of the option
     */
    public function get($key = null, $default = null)
    {
        if (!isset($this->options)) {
            $this->load_options();
        }

        // Return all options
        if (is_null($key)) {
            return $this->options;
        }

        // Return option[$key]
        return $this->options[$key] ?? $default;
    }

    /**
     * Set option [ $key ].
     *
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value)
    {
        if (!isset($this->options)) {
            $this->load_options();
        }
        $this->options[$key] = $value;
    }

    /**
     * Save options to the database.
     */
    public function save()
    {
        update_option($this->option_name, $this->options, false);
    }

    /**
     * Loads the options from the database.
     */
    protected function load_options()
    {
        $options = get_option($this->option_name);
        if (!is_array($options) || empty($options)) {
            $this->options = $this->defaults;
        } else {
            $this->options = array_merge($this->defaults, $options);
        }
    }
}
