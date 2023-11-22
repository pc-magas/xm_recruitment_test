#!/usr/bin/env php

<?php

/**
 *  Xdebug connection tester
 *  Copyright (C) 2023  Dimitrios Desyllas

 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.

 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.

 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

define('ERROR',"\033[0;31m");
define('OK',"\033[0;32m");
define('HIGILIGHT',"\033[0;96m");
define('HIGILIGHT_ERROR',"\033[0;95m");

define('NC',"\033[0m");

$xdebug_settings = getenv("XDEBUG_CONF_FILE");
$xdebug_settings = trim($xdebug_settings);
echo "SETTINGS FILE: ".HIGILIGHT.$xdebug_settings.NC.PHP_EOL;

$php_settings_dir = getenv("PHP_CONF_DIR");

if(dirname($xdebug_settings)!=trim($php_settings_dir)){
    echo "######".PHP_EOL.ERROR."Xdebug config file not in ".HIGILIGHT.$php_settings_dir.NC.PHP_EOL;
    exit(1);
}

echo PHP_EOL."##### CONTENTS #####".PHP_EOL.HIGILIGHT.file_get_contents($xdebug_settings).NC.PHP_EOL;
$settings = parse_ini_file($xdebug_settings);

$xdebug_version = phpversion("xdebug");

echo "Installed XDEBUG Version: ".HIGILIGHT.$xdebug_version.NC.PHP_EOL;


$host=$settings['xdebug.client_host'];
$port=$settings['xdebug.client_port'];

if(version_compare($xdebug_version,"3.0")<0){
    $host=$settings['xdebug.remote_host'];
    $port=$settings['xdebug.remote_port'];
}

$timeout = 30;

echo PHP_EOL."##############".PHP_EOL."TESTING ".HIGILIGHT."${host}:${port}".NC.PHP_EOL;

//check if connection established via is_resource
$sk = @fsockopen($host, $port, $errnum, $errstr, $timeout);
if (!is_resource($sk)) {
    echo ERROR."connection fail: ".HIGILIGHT_ERROR. $errnum . " " . $errstr.NC.PHP_EOL;
    exit(1);
} else {
    echo OK."Connected".NC.PHP_EOL;
    exit(0);
}