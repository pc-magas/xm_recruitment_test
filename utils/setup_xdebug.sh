#!/usr/bin/env bash

#
#  Xdebug setup Script
#  Copyright (C) 2023  Dimitrios Desyllas
#
#  This program is free software: you can redistribute it and/or modify
#  it under the terms of the GNU General Public License as published by
#  the Free Software Foundation, either version 3 of the License, or
#  (at your option) any later version.
#
#  This program is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#
#  You should have received a copy of the GNU General Public License
#  along with this program.  If not, see <https://www.gnu.org/licenses/>.
#

XDEBUG_VERSION="$(echo "${XDEBUG_VERSION}" | tr -d '[:space:]')"

if [ -z ${XDEBUG_VERSION} ] && [ "${XDEBUG_VERSION}" != "" ] && [ "${XDEBUG_VERSION}" != "latest" ] ; then
    echo "Installing xdebug version ${XDEBUG_VERSION}"
    install-php-extensions xdebug-${XDEBUG_VERSION}
else
    echo "Installing xdebug latest version"
    install-php-extensions xdebug
fi

mkdir -p /var/log/xdebug
touch /var/log/xdebug/xdebug.log
chown -R www-data:www-data /var/log/xdebug
chmod 666 -R /var/log/xdebug