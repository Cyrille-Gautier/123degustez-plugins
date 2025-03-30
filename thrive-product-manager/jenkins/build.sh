#!/bin/sh

#
# Arguments:
# $1 = VERSION to build - replace the placeholder in code with this string
#

product_version=$1

if [ "$product_version" = "" ]; then
  case "$BRANCH" in
    *beta*)
      product_version=$(wget -qO- "https://service-api.thrivethemes.com/latest-version?api_slug=thrive_product_manager&channel=beta")
    ;;
    *master*)
      product_version=$(wget -qO- "https://service-api.thrivethemes.com/latest-version?api_slug=thrive_product_manager&channel=stable")
    ;;
    *)
      product_version=$(wget -qO- "https://service-api.thrivethemes.com/next-version?api_slug=thrive_product_manager&channel=stable")
    ;;
  esac
fi;

if [ "$product_version" ]; then
  #replace product version
  cmd_arg="s/0\.dev/$product_version/g";
  sed -i -e "$cmd_arg" thrive-product-manager.php

  case "$product_version" in
    *beta*)
      sed -i -e '/Plugin Name/s/Thrive Product Manager/Thrive Product Manager (BETA)/g' thrive-product-manager.php
      ;;
  esac
fi;

set +x
export NVM_DIR="$HOME/.nvm"
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh" # This loads nvm
nvm use v14.15.5
set -x

# install all node modules required for js / css compilation / minification
rm -rf node_modules && \
ln -s /var/lib/thrive/webpack5-build/node_modules node_modules && \
npm run production && \
rm -f node_modules && \
rm -rf jenkins && \
rm -rf css/scss && \
rm -rf css/*.css.map && \
rm -rf js/admin && \
rm -rf js/dist/tpm-admin.js && \
rm -rf thrive-dashboard && \
unzip thrive-dashboard.zip > /dev/null && \
rm -rf thrive-dashboard.zip && \
rm -rf thrive-product-manager && \
rm -rf thrive-product-manager.zip && \
mkdir thrive-product-manager && \
rsync -avz --exclude "thrive-product-manager" * thrive-product-manager/ > /dev/null && \
zip -r --exclude=*.git* --exclude="*node_modules*" --exclude="*Gruntfile*" --exclude="*package-lock.json*" --exclude="*package.json*" --exclude="*.sass-cache*" thrive-product-manager.zip thrive-product-manager > /dev/null
