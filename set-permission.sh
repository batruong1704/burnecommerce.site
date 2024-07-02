#!/bin/bash

GREEN='\033[0;32m'
NC='\033[0m' # No Color
# Set permissions recursively to 777 for the 'devilbox' directory
echo "Setting permissions and ownership for Magento directories and files..."

sudo chmod -R 777 /var/www/
sudo chown -R www-data:www-data /var/www/
sudo chmod 750 /var/www/burn/htdocs/app/etc/env.php
echo -e "${GREEN} ==> Permissions and ownership set successfully.${NC}"
