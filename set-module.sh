#!/bin/bash

# Set colors for formatting output
GREEN='\033[0;32m'
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

echo "${GREEN}## 1/5. Setup-update${NC}"
sudo bin/magento s:upg
echo "${GREEN}## 2/5. Di-compile${NC}"
sudo bin/magento setup:di:compile

echo "${GREEN}## 3/5. s:s:d${NC}"
sudo bin/magento s:s:d -f
echo
echo "${GREEN}## 4/5. Flush cache${NC}"
sudo bin/magento cache:flush

# Print a blank line
echo

echo "${GREEN}## 5/5. Setup permission${NC}"
sh ./set-permission.sh

print_center "SUCCESS"
