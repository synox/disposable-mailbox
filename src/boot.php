<?php
if (version_compare(phpversion(), '7.2', '<')) {
    die("ERROR! The php version isn't high enough, you need at least 7.2 to run this application! But you have: " . phpversion());
}

# set the new path of config.php (must be in a safe location outside the `public_html`)
#require_once '../../config.php';

function find_config($filename='config.php'){
	$path_length = substr_count(getcwd(),DIRECTORY_SEPARATOR)
		+ 1; # also search the current directory

	$dir = '.';
	for($i=0; $i<$path_length;$i++){
		$config_filename = $dir . DIRECTORY_SEPARATOR . $filename;
		if(file_exists($config_filename)){
			return $config_filename;
		} else {
			$dir = '../'.$dir;
		}
	}
	return FALSE;
}

function load_config(){
	global $config;
	$file = find_config();
	if ( $file !== FALSE) {
		require_once($file);
	} else {
		die('ERROR: Config file not found. Please see the installation instructions in the README.md');
	}
}

load_config();

# load php dependencies:
require_once './backend-libs/autoload.php';

