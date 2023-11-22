#!/usr/bin/env php

<?php

/**
 *  Xdebug ip detector for docker
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

$matches = [];
preg_match("/^\w*\s(00000000)\s(\w*)/m",file_get_contents("/proc/net/route"),$matches);

// At regex each () is also matxched seperately. In my case I have 2 matched groups therefore the secodn match is my ip hex encoded
$ip = $matches[2];
$ip = str_split($ip,2);
$ip = array_reverse($ip);

$ip = array_reduce($ip,function($acc,$item){
    return $acc.intval($item,16).".";
},"");

$ip = rtrim($ip,'.');

echo $ip;