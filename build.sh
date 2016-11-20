#!/bin/bash -e

# install php dependencies
composer install

# copy backend
cp -rv src/{backend.php,config.sample.php} dist/


echo "done"