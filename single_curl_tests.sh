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

ACCEPTS_FLAG="accept: application/json"
CONTENT_FLAG="Content-Type: application/json"
AUTH_FLAG="Authorization: BEARER ${token}"

echo " ---------------------------------------- "

#payload=$(printf '{"common_area_id": %d, "reservation_date_from": %d, "reservation_date_to": %d}' 1 1560160800 1560247200)
#
#response=$(curl -s -XPOST "${API_URL}/v1/commonAreaReservation?${DEFAULT_QUERY}" \
#    -H  "accept: application/json" \
#    -H  "Content-Type: application/json" \
#    -H  "Authorization: BEARER ${token}" \
#    -d "${payload}")
#
#echo "${response}"

URL="${API_URL}/v1/polls/1?${DEFAULT_QUERY}"
response=$(curl -s -XGET "${URL}" -H "${ACCEPTS_FLAG}" -H "${CONTENT_FLAG}" -H "${AUTH_FLAG}")

#echo "${response}"
echo "${response}" | jq -r '.message'
echo "${response}" | jq -r '.metadata'
echo "${response}" | jq -r '.data'


echo " ---------------------------------------- "