#!/bin/bash -e

# install php dependencies
composer install

# copy backend
cp -rv src/{backend.php,config.sample.php} dist/

# install javascript dependencies
npm install

# build Javascript frontend
npm run build

echo "done"