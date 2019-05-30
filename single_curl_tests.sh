#!/usr/bin/env bash

API_URL="http://localhost:8580/api"
LANGUAGE="en"
APP_VERSION="1.0.0"
CODE_VERSION="1.0.0"

DEFAULT_QUERY="language=${LANGUAGE}&app_version=${APP_VERSION}&code_version=${CODE_VERSION}"

response=$(curl -s -XPOST "${API_URL}/login_check?${DEFAULT_QUERY}" \
    -H  "accept: application/json" \
    -H  "Content-Type: application/json" \
    -d '{"_username": "adminuser", "_password": "adminuser123"}')

token=$(echo "${response}" | jq -r '.token')

if [[ -n "${token}" ]]; then
    echo "LOGIN OK!"
else
    echo "FAIL ..."
fi


echo " ---------------------------------------- "

response=$(curl -s -XGET "${API_URL}/v1/commonAreaAvailability/$1?${DEFAULT_QUERY}" \
    -H  "accept: application/json" \
    -H  "Content-Type: application/json" \
    -H  "Authorization: BEARER ${token}" )

echo "${response}"
echo "${response}" | jq -r '.message'
echo "${response}" | jq -r '.metadata'
echo "${response}" | jq -r '.data'

echo " ---------------------------------------- "