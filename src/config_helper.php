<?php

/**
 * searches for a config-file in the current and parent directories until found.
 * @return path to found config file, or FALSE otherwise.
 */
function find_config($filename='config.php') {
    // Count the deph of the current directory, so we know how far we can go up.
    $path_length = substr_count(getcwd(), DIRECTORY_SEPARATOR)
        + 1; // also search the current directory

    $dir = '.'; // updated in each loop
    for ($i=0; $i<$path_length;$i++) {
        $config_filename = $dir . DIRECTORY_SEPARATOR . $filename;
        if (file_exists($config_filename)) {
            return $config_filename;
        } else {
            $dir = '../' . $dir;
        }
    }
    return false;
}

/**
 * searches and loads the config file. Prints an error if not found.
 */
function load_config() {
    global $config;
    $file = find_config();
    if ($file !== false) {
        require_once($file);
        if (!isset($config) || !is_array($config)) {
            die('ERROR: Config file is invalid. Please see the installation instructions in the README.md');
        }
    } else {
        die('ERROR: Config file not found. Please see the installation instructions in the README.md');
    }
}
