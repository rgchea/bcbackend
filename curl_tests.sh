#!/usr/bin/env bash

API_URL="http://localhost:8580/api"
LANGUAGE="en"
APP_VERSION="1.0.0"
CODE_VERSION="1.0.0"

DEFAULT_QUERY="language=${LANGUAGE}&app_version=${APP_VERSION}&code_version=${CODE_VERSION}"

echo " ---------------------------------------- Invalid credentials "

response=$(curl -s -XPOST "${API_URL}/login_check?${DEFAULT_QUERY}" \
    -H  "accept: application/json" \
    -H  "Content-Type: application/json" \
    -d '{"_username": "adminuser", "_password": "adminuser2345"}')

echo "${response}"
echo " ---------------------------------------- "


echo " ---------------------------------------- Valid credentials "

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


echo ""
echo " ---------------------------------------- "


echo " ---------------------------------------- Create user "

user_timestamp=$(date -u +%s)
password="chepe"
payload=$(printf '{"name": "Chepe Alvarez", "mobile_phone": "+330695507415", "country_code": "1", "email": "%s", "password": "%s"}' "chepeftw${user_timestamp}@gmail.com" "${password}")

response=$(curl -s -XPOST "${API_URL}/register?${DEFAULT_QUERY}" \
    -H  "accept: application/json" \
    -H  "Content-Type: application/json" \
    -H  "Authorization: BEARER ${token}" \
    -d "${payload}")

echo ${response}

echo " - "
echo " - "

echo " ---------------------------------------- test login "

payload=$(printf '{"_username": "%s", "_password": "%s"}' "chepeftw${user_timestamp}@gmail.com" "${password}")
response=$(curl -s -XPOST "${API_URL}/login_check?${DEFAULT_QUERY}" \
    -H  "accept: application/json" \
    -H  "Content-Type: application/json" \
    -d "${payload}")

token=$(echo "${response}" | jq -r '.token')

if [[ -n "${token}" ]]; then
    echo "LOGIN OK!"
else
    echo "FAIL ..."
fi

echo ""

echo " ---------------------------------------- "


echo " ---------------------------------------- Forgot password"

payload=$(printf '{"email": "%s"}' "chepeftw${user_timestamp}@gmail.com")

response=$(curl -s -XPOST "${API_URL}/forgotPassword?${DEFAULT_QUERY}" \
    -H  "accept: application/json" \
    -H  "Content-Type: application/json" \
    -H  "Authorization: BEARER ${token}" \
    -d "${payload}")

echo ${response}

echo " ---------------------------------------- "


echo " ---------------------------------------- Terms and conditions"

response=$(curl -s -XGET "${API_URL}/termsConditions?${DEFAULT_QUERY}" \
    -H  "accept: application/json" \
    -H  "Content-Type: application/json" \
    -H  "Authorization: BEARER ${token}")

echo "${response}" | jq -r '.message'
echo "${response}" | jq -r '.data'

echo " ---------------------------------------- "


echo " ---------------------------------------- Countries"

response=$(curl -s -XGET "${API_URL}/countries?${DEFAULT_QUERY}" \
    -H  "accept: application/json" \
    -H  "Content-Type: application/json" \
    -H  "Authorization: BEARER ${token}")

echo "${response}"
echo "${response}" | jq -r '.message'
echo "${response}" | jq -r '.data'

echo " ---------------------------------------- "

echo " ---------------------------------------- listProperties"

response=$(curl -s -XGET "${API_URL}/properties/1?${DEFAULT_QUERY}" \
    -H  "accept: application/json" \
    -H  "Content-Type: application/json" \
    -H  "Authorization: BEARER ${token}" )

echo "${response}"
echo "${response}" | jq -r '.message'
echo "${response}" | jq -r '.metadata'
echo "${response}" | jq -r '.data'

echo " ---------------------------------------- "


echo " ---------------------------------------- welcomePrivateKey"

payload=$(printf '{"property_code": "%s"}' "000001")

response=$(curl -s -XPOST "${API_URL}/welcomePrivateKey?${DEFAULT_QUERY}" \
    -H  "accept: application/json" \
    -H  "Content-Type: application/json" \
    -H  "Authorization: BEARER ${token}" \
    -d "${payload}")

echo "${response}"
echo "${response}" | jq -r '.message'
echo "${response}" | jq -r '.data'

echo " ---------------------------------------- "


echo " ---------------------------------------- propertyInfo"

response=$(curl -s -XGET "${API_URL}/property/000001?${DEFAULT_QUERY}" \
    -H  "accept: application/json" \
    -H  "Content-Type: application/json" \
    -H  "Authorization: BEARER ${token}" )

echo "${response}"
echo "${response}" | jq -r '.message'
echo "${response}" | jq -r '.data'

echo " ---------------------------------------- "


echo " ---------------------------------------- listInbox"

response=$(curl -s -XGET "${API_URL}/inbox/1?${DEFAULT_QUERY}" \
    -H  "accept: application/json" \
    -H  "Content-Type: application/json" \
    -H  "Authorization: BEARER ${token}" )

echo "${response}"
echo "${response}" | jq -r '.message'
echo "${response}" | jq -r '.metadata'
echo "${response}" | jq -r '.data'

echo " ---------------------------------------- "


echo " ---------------------------------------- listInbox"

response=$(curl -s -XGET "${API_URL}/inbox/1?${DEFAULT_QUERY}" \
    -H  "accept: application/json" \
    -H  "Content-Type: application/json" \
    -H  "Authorization: BEARER ${token}" )

echo "${response}"
echo "${response}" | jq -r '.message'
echo "${response}" | jq -r '.metadata'
echo "${response}" | jq -r '.data'

echo " ---------------------------------------- "