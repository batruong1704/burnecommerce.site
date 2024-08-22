#!/bin/bash

# Set colors for formatting output
YELLOW='\033[0;33m'
NC='\033[0m' # No Color

# Function to print centered text
print_center() {
    local text="$1"
    local cols=$(tput cols)  # Get number of columns in terminal
    local len=${#text}       # Get length of text
    local pos=$(( (cols - len) / 2 ))  # Calculate starting position for centering
    printf "%*s\n" $pos "$text"
}
echo
echo
print_center "Start Deploy"

echo "${YELLOW}## 1/5. Upgrade site${NC}"
sudo bin/magento setup:install \
--base-url=https://burnecommerce.site/ \
--db-host=localhost \
--db-name=burn \
--db-user=root \
--db-password=admin \
--admin-firstname=admin \
--admin-lastname=admin \
--admin-email=admin@admin.com \
--admin-user=admin \
--admin-password=admin123 \
--language=en_US \
--currency=USD \
--timezone=America/Chicago \
--use-rewrites=1 \
--search-engine=elasticsearch7 \
--elasticsearch-host=localhost \
--elasticsearch-port=9200 \
--elasticsearch-index-prefix=magento2 \
--elasticsearch-timeout=15

sudo php bin/magento setup:upgrade
echo

echo "${YELLOW}## 2/5. Di compile${NC}"
sudo bin/magento setup:di:compile
echo
echo "${YELLOW}## 3/5. Static content deploy${NC}"
sudo bin/magento setup:static-content:deploy -f

echo
echo "${YELLOW}## 4/5. Flush cache${NC}"
sudo bin/magento cache:flush

# Print a blank line
echo

echo "${YELLOW}## 5/5. Setup permission${NC}"
sh ./set-permission.sh

print_center "SUCCESS"
