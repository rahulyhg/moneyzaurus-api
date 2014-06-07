#!/bin/sh
# https://github.com/facebook/hhvm/wiki/fastcgi

hhvm --version
hhvm --mode server -vServer.Type=fastcgi -vServer.Port=8100 &

service nginx start