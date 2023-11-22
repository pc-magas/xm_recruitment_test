#!/usr/bin/env bash

CURRENT_UID=$(id -u ${USER})
CURRENT_GID=$(id -g ${USER})


echo -e "The settings for user id and group id at \033[1;33menv/php.env\033[0m should contain:"
echo
echo -e "\033[0;96mAPP_UID=${CURRENT_UID}\nAPP_GID=${CURRENT_GID}\033[0m"
echo