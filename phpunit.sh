#!/usr/bin/env bash

# @TODO: WHAT ABOUT packages installed on `vendor` directory?

dir=`pwd`
cd ./../../../
./vendor/bin/phpunit --configuration="$dir" "$@"
