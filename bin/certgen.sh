#!/usr/bin/env bash

#  Development Certificate Generator
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

SCRIPT="$(readlink --canonicalize-existing "$0")"
SCRIPTPATH="$(dirname "$SCRIPT")"
SSL_PATH="${SCRIPTPATH}/../ssl"

CERT_PATH="${SSL_PATH}/certs"
SSL_CONF_PATH="${SSL_PATH}/conf/ssl_conf"
SIGNING_REQUEST_CONF="${SSL_PATH}/conf/v3.sign"

CA_PATH="${SSL_PATH}/ca"
CA_KEY=${CA_PATH}/ca.key 
CA_CERT=${CA_PATH}/ca.crt 

if  [[ ! -f ${CA_CERT} ]] || [[ ! -f ${CA_KEY} ]]; then

    openssl genrsa -out ${CA_KEY} 2048
    openssl req -x509 -new -nodes \
            -key ${CA_KEY} -subj "/C=GR/L=ATTICA" \
            -days 1825 -out ${CA_CERT}

fi

CERT_BASENAME="www"

CERTIFICATE_PATH=${CERT_PATH}/${CERT_BASENAME}.crt
KEY_PATH=${CERT_PATH}/${CERT_BASENAME}.key
SIGNING_REQUEST=${CERT_PATH}/${CERT_BASENAME}.csr

echo "CREATING CERTIFICATE"

openssl req -new -sha512 -keyout ${KEY_PATH} -nodes -out ${SIGNING_REQUEST} -config ${SSL_CONF_PATH}


echo "SIGNING CERTIFICATE using CA"
openssl x509 -req -days 9000 -startdate -sha512 -in ${SIGNING_REQUEST} \
     -CAkey ${CA_KEY} -CA ${CA_CERT} -CAcreateserial \
     -extfile ${SIGNING_REQUEST_CONF} \
     -out ${CERTIFICATE_PATH} 

rm -rf ${SIGNING_REQUEST}

echo "#######################################"
echo "IMPORT these cert into your system as CA cert:"
echo -e "\033[0;96m${CA_CERT}\033[0m"
echo
echo The generated certificates are:
echo -e "CERT: \033[0;96m${CERTIFICATE_PATH}\033[0m"
echo -e "KEY: \033[0;96m${KEY_PATH}\033[0m"