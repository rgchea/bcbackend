#!/usr/bin/env bash

API_URL="http://localhost:8580/api"
#API_URL="http://bettercondos.pizotesoftdev.com/api"
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

ACCEPTS_FLAG="accept: application/json"
CONTENT_FLAG="Content-Type: application/json"
AUTH_FLAG="Authorization: BEARER ${token}"

echo " ---------------------------------------- "

echo " ---------------------------------------- Create user "

user_timestamp=$(date -u +%s)
password="chepe"
payload=$(printf '{"name": "Chepe Alvarez", "mobile_phone": "+330695507415", "country_code": "1", "email": "%s", "password": "%s"}' "chepeftw${user_timestamp}@gmail.com" "${password}")

echo ${payload}
echo " ------ "

response=$(curl -s -XPOST "${API_URL}/register?${DEFAULT_QUERY}" \
    -H "${ACCEPTS_FLAG}" -H "${CONTENT_FLAG}" -H "${AUTH_FLAG}" \
    -d "${payload}")

echo ${response}

echo " - "
echo " - "

echo " ---------------------------------------- test login "

payload=$(printf '{"_username": "%s", "_password": "%s"}' "chepeftw${user_timestamp}@gmail.com" "${password}")
response=$(curl -s -XPOST "${API_URL}/login_check?${DEFAULT_QUERY}" \
    -H "${ACCEPTS_FLAG}" -H "${CONTENT_FLAG}" \
    -d "${payload}")

token=$(echo "${response}" | jq -r '.token')

if [[ -n "${token}" ]]; then
    echo "LOGIN OK!"
else
    echo "FAIL ..."
fi

echo ""

echo " ---------------------------------------- "