#!/bin/sh
# https://github.com/facebook/hhvm/wiki/fastcgi

sudo apt-get install nginx

# Configure apache virtual hosts
sudo cp -f tests/ci/hhvm-nginx.conf /etc/nginx/nginx.conf
sudo sed -e "s?%TRAVIS_BUILD_DIR%?$(pwd)?g" --in-place /etc/nginx/nginx.conf