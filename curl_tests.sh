#!/usr/bin/env bash

API_URL="http://localhost:8580/api"
LANGUAGE="en"
APP_VERSION="1.0.0"
CODE_VERSION="1.0.0"

DEFAULT_QUERY="language=${LANGUAGE}&app_version=${APP_VERSION}&code_version=${CODE_VERSION}"

########################################################################################################
########################################################################################################
############################### -- Users
########################################################################################################
########################################################################################################

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


ACCEPTS_FLAG="accept: application/json"
CONTENT_FLAG="Content-Type: application/json"
AUTH_FLAG="Authorization: BEARER ${token}"


echo " ---------------------------------------- Create user "

user_timestamp=$(date -u +%s)
password="chepe"
payload=$(printf '{"name": "Chepe Alvarez", "mobile_phone": "+330695507415", "country_code": "1", "email": "%s", "password": "%s"}' "chepeftw${user_timestamp}@gmail.com" "${password}")

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


echo " ---------------------------------------- Forgot password"

payload=$(printf '{"email": "%s"}' "chepeftw${user_timestamp}@gmail.com")

response=$(curl -s -XPOST "${API_URL}/forgotPassword?${DEFAULT_QUERY}" \
    -H "${ACCEPTS_FLAG}" -H "${CONTENT_FLAG}" -H "${AUTH_FLAG}" \
    -d "${payload}")

echo ${response}

echo " ---------------------------------------- "



########################################################################################################
########################################################################################################
############################### -- Admin
########################################################################################################
########################################################################################################



echo " ---------------------------------------- Terms and conditions"

response=$(curl -s -XGET "${API_URL}/termsConditions?${DEFAULT_QUERY}" \
    -H "${ACCEPTS_FLAG}" -H "${CONTENT_FLAG}" -H "${AUTH_FLAG}" )

echo "${response}" | jq -r '.message'
echo "${response}" | jq -r '.data'

echo " ---------------------------------------- "


echo " ---------------------------------------- Countries"

response=$(curl -s -XGET "${API_URL}/countries?${DEFAULT_QUERY}" \
    -H "${ACCEPTS_FLAG}" -H "${CONTENT_FLAG}" -H "${AUTH_FLAG}" )

echo "${response}"
echo "${response}" | jq -r '.message'
echo "${response}" | jq -r '.data'

echo " ---------------------------------------- "


echo " ---------------------------------------- v1"

response=$(curl -s -XGET "${API_URL}/v1/test?${DEFAULT_QUERY}&pid=1" \
    -H "${ACCEPTS_FLAG}" -H "${CONTENT_FLAG}" -H "${AUTH_FLAG}" )

echo "${response}"
echo "${response}" | jq -r '.message'
echo "${response}" | jq -r '.data'

echo " ---------------------------------------- "




########################################################################################################
########################################################################################################
############################### -- Property
########################################################################################################
########################################################################################################




echo " ---------------------------------------- listProperties"

response=$(curl -s -XGET "${API_URL}/v1/properties/1?${DEFAULT_QUERY}" \
    -H "${ACCEPTS_FLAG}" -H "${CONTENT_FLAG}" -H "${AUTH_FLAG}" )

echo "${response}"
echo "${response}" | jq -r '.message'
echo "${response}" | jq -r '.metadata'
echo "${response}" | jq -r '.data'

echo " ---------------------------------------- "


echo " ---------------------------------------- welcomePrivateKey"

PROPERTY_CODE="000001"

payload=$(printf '{"property_code": "%s"}' "${PROPERTY_CODE}")

response=$(curl -s -XPOST "${API_URL}/v1/welcomePrivateKey?${DEFAULT_QUERY}" \
    -H "${ACCEPTS_FLAG}" -H "${CONTENT_FLAG}" -H "${AUTH_FLAG}" \
    -d "${payload}")

echo "${response}"
echo "${response}" | jq -r '.message'
echo "${response}" | jq -r '.data'

echo " ---------------------------------------- "


echo " ---------------------------------------- listProperties"

response=$(curl -s -XGET "${API_URL}/v1/properties/1?${DEFAULT_QUERY}" \
    -H "${ACCEPTS_FLAG}" -H "${CONTENT_FLAG}" -H "${AUTH_FLAG}" )

echo "${response}"
echo "${response}" | jq -r '.message'
echo "${response}" | jq -r '.metadata'
echo "${response}" | jq -r '.data'

echo " ---------------------------------------- "


echo " ---------------------------------------- propertyDetail"

response=$(curl -s -XGET "${API_URL}/v1/propertyDetail/1?${DEFAULT_QUERY}" \
    -H "${ACCEPTS_FLAG}" -H "${CONTENT_FLAG}" -H "${AUTH_FLAG}" )

echo "${response}"
echo "${response}" | jq -r '.message'
echo "${response}" | jq -r '.data'

echo " ---------------------------------------- "



########################################################################################################
########################################################################################################
############################### -- Inbox
########################################################################################################
########################################################################################################


echo " ---------------------------------------- listInbox"

response=$(curl -s -XGET "${API_URL}/v1/inbox/1?${DEFAULT_QUERY}" \
    -H "${ACCEPTS_FLAG}" -H "${CONTENT_FLAG}" -H "${AUTH_FLAG}" )

echo "${response}"
echo "${response}" | jq -r '.message'
echo "${response}" | jq -r '.metadata'
echo "${response}" | jq -r '.data'

echo " ---------------------------------------- "



########################################################################################################
########################################################################################################
############################### -- Ticket
########################################################################################################
########################################################################################################


echo " ---------------------------------------- ticketCategory"

response=$(curl -s -XGET "${API_URL}/v1/ticketCategory/1/1?${DEFAULT_QUERY}" \
    -H "${ACCEPTS_FLAG}" -H "${CONTENT_FLAG}" -H "${AUTH_FLAG}" )

echo "${response}"
echo "${response}" | jq -r '.message'
echo "${response}" | jq -r '.metadata'
echo "${response}" | jq -r '.data'

echo " ---------------------------------------- "


echo " ---------------------------------------- listFeed"

response=$(curl -s -XGET "${API_URL}/v1/feed/1/1/1?${DEFAULT_QUERY}" \
    -H "${ACCEPTS_FLAG}" -H "${CONTENT_FLAG}" -H "${AUTH_FLAG}" )

echo "${response}"
echo "${response}" | jq -r '.message'
echo "${response}" | jq -r '.metadata'
echo "${response}" | jq -r '.data'

echo " ---------------------------------------- "


echo " ---------------------------------------- createTicket"

payload=$(printf '{"title": "%s", "description": "%s", "photos": [], "solution": "Solution", "is_public": false, "category_id": 1, "sector_id": 1, "property_id": 1, "tenant_contract_id": 1}' "Test Ticket!" "Lorem ipsum lorem ipsum lorem ipsum")

response=$(curl -s -XPOST "${API_URL}/v1/ticket?${DEFAULT_QUERY}" \
    -H "${ACCEPTS_FLAG}" -H "${CONTENT_FLAG}" -H "${AUTH_FLAG}" \
    -d "${payload}")

echo "${response}"

ticketId=$(echo "${response}" | jq -r '.debug')

echo " ---------------------------------------- "


echo " ---------------------------------------- singleTicket"

response=$(curl -s -XGET "${API_URL}/v1/ticket/${ticketId}?${DEFAULT_QUERY}" \
    -H "${ACCEPTS_FLAG}" -H "${CONTENT_FLAG}" -H "${AUTH_FLAG}" )

echo "${response}"
echo "${response}" | jq -r '.message'
echo "${response}" | jq -r '.metadata'
echo "${response}" | jq -r '.data'

echo " ---------------------------------------- "


echo " ---------------------------------------- commentTicket"

payload=$(printf '{"ticket_id": "%s", "comment": "%s"}' "${ticketId}" "This is a comment!")

response=$(curl -s -XPOST "${API_URL}/v1/comment?${DEFAULT_QUERY}" \
    -H "${ACCEPTS_FLAG}" -H "${CONTENT_FLAG}" -H "${AUTH_FLAG}" \
    -d "${payload}")

echo "${response}"
echo "${response}" | jq -r '.message'
echo "${response}" | jq -r '.data'

echo " ---------------------------------------- "


echo " ---------------------------------------- commentTicket 2"

payload=$(printf '{"ticket_id": "%s", "comment": "%s"}' "${ticketId}" "This is a second comment!")

response=$(curl -s -XPOST "${API_URL}/v1/comment?${DEFAULT_QUERY}" \
    -H "${ACCEPTS_FLAG}" -H "${CONTENT_FLAG}" -H "${AUTH_FLAG}" \
    -d "${payload}")

echo "${response}"
echo "${response}" | jq -r '.message'
echo "${response}" | jq -r '.data'

echo " ---------------------------------------- "




echo " ---------------------------------------- singleTicket"

response=$(curl -s -XGET "${API_URL}/v1/ticket/${ticketId}?${DEFAULT_QUERY}" \
    -H "${ACCEPTS_FLAG}" -H "${CONTENT_FLAG}" -H "${AUTH_FLAG}" )

echo "${response}"
echo "${response}" | jq -r '.message'
echo "${response}" | jq -r '.metadata'
echo "${response}" | jq -r '.data'

echo " ---------------------------------------- "




echo " ---------------------------------------- listFeed"

response=$(curl -s -XGET "${API_URL}/v1/feed/1/1/1?${DEFAULT_QUERY}" \
    -H "${ACCEPTS_FLAG}" -H "${CONTENT_FLAG}" -H "${AUTH_FLAG}" )

echo "${response}"
echo "${response}" | jq -r '.message'
echo "${response}" | jq -r '.metadata'
echo "${response}" | jq -r '.data'

echo " ---------------------------------------- "


echo " ---------------------------------------- closeTicket"

payload=$(printf '{"ticket_id": "%s", "rating": 5}' "${ticketId}")

response=$(curl -s -XPUT "${API_URL}/v1/ticket?${DEFAULT_QUERY}" \
    -H "${ACCEPTS_FLAG}" -H "${CONTENT_FLAG}" -H "${AUTH_FLAG}" \
    -d "${payload}")

echo "${response}"

echo " ---------------------------------------- "

echo " ---------------------------------------- listFeed"

response=$(curl -s -XGET "${API_URL}/v1/feed/1/1/1?${DEFAULT_QUERY}" \
    -H "${ACCEPTS_FLAG}" -H "${CONTENT_FLAG}" -H "${AUTH_FLAG}" )

echo "${response}"
echo "${response}" | jq -r '.message'
echo "${response}" | jq -r '.metadata'
echo "${response}" | jq -r '.data'

echo " ---------------------------------------- "




########################################################################################################
########################################################################################################
############################### -- Common Area
########################################################################################################
########################################################################################################


echo " ---------------------------------------- listCommonAreas 1"

response=$(curl -s -XGET "${API_URL}/v1/commonAreas/1/1?${DEFAULT_QUERY}" \
    -H "${ACCEPTS_FLAG}" -H "${CONTENT_FLAG}" -H "${AUTH_FLAG}" )

echo "${response}"
echo "${response}" | jq -r '.message'
echo "${response}" | jq -r '.metadata'
echo "${response}" | jq -r '.data'

echo " ---------------------------------------- "

echo " ---------------------------------------- listCommonAreas 11"

response=$(curl -s -XGET "${API_URL}/v1/commonAreas/11/1?${DEFAULT_QUERY}" \
    -H "${ACCEPTS_FLAG}" -H "${CONTENT_FLAG}" -H "${AUTH_FLAG}" )

echo "${response}"
echo "${response}" | jq -r '.message'
echo "${response}" | jq -r '.metadata'
echo "${response}" | jq -r '.data'

echo " ---------------------------------------- "

echo " ---------------------------------------- commonAreaDetail"

response=$(curl -s -XGET "${API_URL}/v1/commonArea/2?${DEFAULT_QUERY}" \
    -H "${ACCEPTS_FLAG}" -H "${CONTENT_FLAG}" -H "${AUTH_FLAG}" )

echo "${response}"
echo "${response}" | jq -r '.message'
echo "${response}" | jq -r '.metadata'
echo "${response}" | jq -r '.data'

echo " ---------------------------------------- "

echo " ---------------------------------------- commonAreaAvailability"

response=$(curl -s -XGET "${API_URL}/v1/commonAreaAvailability/2?${DEFAULT_QUERY}" \
    -H "${ACCEPTS_FLAG}" -H "${CONTENT_FLAG}" -H "${AUTH_FLAG}" )

echo "${response}"
echo "${response}" | jq -r '.message'
echo "${response}" | jq -r '.metadata'
echo "${response}" | jq -r '.data'

echo " ---------------------------------------- "

echo " ---------------------------------------- commonAreaReservation"

# June, 10th 2019 10am -> June 11th 2019 10am
payload=$(printf '{"common_area_id": %d, "reservation_date_from": %d, "reservation_date_to": %d}' 2 1560160800 1560247200)

response=$(curl -s -XPOST "${API_URL}/v1/commonAreaReservation?${DEFAULT_QUERY}" \
    -H "${ACCEPTS_FLAG}" -H "${CONTENT_FLAG}" -H "${AUTH_FLAG}" \
    -d "${payload}")

echo "${response}"

echo " ---------------------------------------- "

echo " ---------------------------------------- commonAreaAvailability"

response=$(curl -s -XGET "${API_URL}/v1/commonAreaAvailability/2?${DEFAULT_QUERY}" \
    -H "${ACCEPTS_FLAG}" -H "${CONTENT_FLAG}" -H "${AUTH_FLAG}" )

echo "${response}"
echo "${response}" | jq -r '.message'
echo "${response}" | jq -r '.metadata'
echo "${response}" | jq -r '.data'

echo " ---------------------------------------- "



########################################################################################################
########################################################################################################
############################### -- Polls
########################################################################################################
########################################################################################################

echo " ---------------------------------------- commonAreaAvailability"

response=$(curl -s -XGET "${API_URL}/v1/polls/1?${DEFAULT_QUERY}" \
    -H "${ACCEPTS_FLAG}" -H "${CONTENT_FLAG}" -H "${AUTH_FLAG}" )

echo "${response}"
echo "${response}" | jq -r '.message'
echo "${response}" | jq -r '.metadata'
echo "${response}" | jq -r '.data'


echo " ---------------------------------------- "

# ToDo: Need more data for polls



########################################################################################################
########################################################################################################
############################### -- Polls
########################################################################################################
########################################################################################################