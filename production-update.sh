#!/bin/bash

git pull && rm -rf temp/cache && php www/index.php o:s:u --dump-sql --force