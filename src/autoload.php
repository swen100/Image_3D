<?php

define('IMAGE_3D_DRIVER_ASCII_GRAY', 0.01);

/**
 * project-specific implementation.
 *
 * @param string $class The fully-qualified class name.
 * @return void
 */
spl_autoload_register(function ($class) {
    #echo @get_called_class() . " called: " . "<br>";
    #echo "<b>$class</b><br>\n";
    
    // project-specific namespace prefix
    $prefix = 'Image3D';

    // base directory for the namespace prefix
    $base_dir = __DIR__ . '/3D';

    // does the class use the namespace prefix?
    $len = strlen($prefix);
    #var_dump(strncmp($prefix, $class, $len));
    #die();
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }
    
    // get the relative class name
    #$relative_class = str_replace('Image_3D_', '', substr($class, $len));
    $relative_class = substr($class, $len);
    #echo "<b>Image3D\\$relative_class</b><br>\n";
    
    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    #echo $file . "<br>\n";
    // if the file exists, require it
    if (file_exists($file)) {
        require_once $file;
    }
});

/**
 *
 * @param mixed $var
 * @param bool $die Exit script yes/no. Default yes.
 * @return void
 */
function dbg($var, $die = true)
{
    if (is_string($var)) {
        echo $var . '<br>';
    } else {
        echo "<pre>";
        var_dump($var);
        echo "</pre>";
    }

    if ($die === true) {
        die();
    }
}
