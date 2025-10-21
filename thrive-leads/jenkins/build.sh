#!/bin/sh

#
# Arguments:
# $1 = VERSION to build - replace the placeholder in code with this string
#

product_version=$1

if [ "$product_version" = "" ]; then
  case "$BRANCH" in
    *beta*)
      product_version=$(wget -qO- "https://service-api.thrivethemes.com/latest-version?api_slug=thrive_leads&channel=beta")
    ;;
    *master*)
      product_version=$(wget -qO- "https://service-api.thrivethemes.com/latest-version?api_slug=thrive_leads&channel=stable")
    ;;
    *)
      product_version=$(wget -qO- "https://service-api.thrivethemes.com/next-version?api_slug=thrive_leads&channel=stable")
    ;;
  esac
fi;

if [ "$product_version" ]; then
  #replace product version
  cmd_arg="s/0\.dev/$product_version/g";
  sed -i -e "$cmd_arg" thrive-leads.php
  sed -i -e "$cmd_arg" inc/constants.php

  case "$product_version" in
    *beta*)
      sed -i -e '/Plugin Name/s/Thrive Leads/Thrive Leads (BETA)/g' thrive-leads.php
      ;;
  esac

  rm -f thrive-leads.php-e inc/constants.php-e
fi;

rm -rf jenkins

set +x
export NVM_DIR="$HOME/.nvm"
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh" # This loads nvm
nvm use v14.16.0
set -x

# delete the tcb folder - git clone will error out if this folder exists
rm -rf tcb

# unzip TCB and rename the folder to tcb
unzip thrive-architect.zip > /dev/null
rm -f thrive-architect.zip
mv thrive-visual-editor tcb

cd tcb

# remove unused files (e.g. landing pages)
rm -rf landing-page/js
rm -rf landing-page/lightboxes
rm -rf landing-page/menu
rm -rf landing-page/templates/css
rm -rf landing-page/templates/thumbnails
find landing-page/templates/ -type f \( ! -iname "_config.php" \) -delete
rm -rf thrive-dashboard

#remove the TCB main plugin file
rm -f thrive-visual-editor.php

# move back to the parent folder
cd ../

# install all node modules required for js / css compilation / minification
# npm install
rm -rf node_modules
ln -s /var/lib/global_node_modules_webpack_general/node_modules/ node_modules
npm run production || exit 33

rm -f node_modules
rm -rf .idea

# remove un-minified JS/CSS files
rm -rf admin/js
rm -rf admin/css/sass admin/css/.sass-cache

rm -rf editor-layouts/css/sass
rm -rf editor-templates/_form_css/sass

rm -rf thrive-leads
rm -rf thrive-dashboard
rm -f thrive-leads.zip
mkdir thrive-leads

unzip thrive-dashboard.zip > /dev/null

rm -rf thrive-dashboard.zip

rsync -avz --exclude "thrive-leads" * thrive-leads/ > /dev/null
zip -r --exclude=*.git* --exclude="*node_modules*" --exclude="*webpack.config*" --exclude="*package.json*" --exclude="*.sass-cache*" thrive-leads.zip thrive-leads > /dev/null
rm -rf thrive-leads
