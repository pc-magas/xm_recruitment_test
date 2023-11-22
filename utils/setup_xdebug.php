
<?php

/**
 *
 *  Xdebug setup Script - php version
 *  Copyright (C) 2023  Dimitrios Desyllas
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 */

// Besause I want the script to be runnable at any circumstance I avoid https (mostly on legacy systems)
define("DOWNLOAD_URL","http://pecl.php.net/get/xdebug");
define("PHP_VER",PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION);

$final_url=DOWNLOAD_URL;

// Use old array style for backwards compartibility
$xdebug_version_per_php_ver = array(
    "7.1"=>"2.9.8",
    "7.0"=>"2.7.2",
    "5.6"=>"2.5.5",
    "5.5"=>"2.5.5",
    "5.4"=>"2.4.1",
    "5.3"=>"2.2.6",
);


$xdebug_version = getenv("XDEBUG_VERSION");
if(empty($xdebug_version)){
    $xdebug_version="";
}
$xdebug_version = trim($xdebug_version);

// We need to ignore any compartibility check if php defined is the one we specify.
if((!empty($xdebug_version) && $xdebug_version!="latest")){
    $final_url.="-".$xdebug_version.".tgz";
} elseif(isset($xdebug_version_per_php_ver[PHP_VER])) { // Upon latest we check compartible versions
    $final_url.="-".$xdebug_version_per_php_ver[PHP_VER].".tgz";
} else {
    echo "Detecting latest STABLE version".PHP_EOL;
    $xdebug_version=file_get_contents("https://pecl.php.net/rest/r/xdebug/stable.txt");
    $final_url.="-".$xdebug_version.".tgz";
}

echo "INSTALLING FROM $final_url".PHP_EOL;
sleep(1);

if(!@copy($final_url,'/tmp/xdebug.tgz')){
    $errors= error_get_last();
    fprintf(STDERR,$errors['message']);
    exit(1);
}

if ( !file_exists( '/tmp/xdebug' ) || !is_dir( '/tmp/xdebug') ) {
    mkdir('/tmp/xdebug'); 
}

echo "Extracting downloaded file /tmp/xdebug.tgz";
exec("tar -xv -C /tmp/xdebug -f /tmp/xdebug.tgz | grep xdebug | head -1 | cut -f1 -d\"/\"",$dirname);
$dirname = trim(implode($dirname)); // I EXPECT 1 WORD OUTPUT

if(!file_exists('/tmp/xdebug/'.$dirname)){
    fprintf(STDERR,"FILE NOT EXTRACTED".PHP_EOL);
    exit(1);
}

chdir('/tmp/xdebug/'.$dirname);

error_reporting(E_ALL);
$pd=popen('phpize;./configure --enable-xdebug&&make&&make install','r');
fpassthru($pd);
pclose($pd);

exec('rm -rf /tmp/xdebug');