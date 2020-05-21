#!/bin/bash

# creates a zip archive from master from github.

CWD=`pwd`

DIR=`mktemp -d -p /tmp plugin.XXXXXXXX`

pushd ${DIR}

git clone https://github.com/pipfrosch/pipfrosch-jquery.git

cd pipfrosch-jquery

rm -f .gitignore
rm -rf .git
rm -f mkzip.sh
rm -f README.md

cd ..

zip -r pipfrosch-jquery.zip pipfrosch-jquery

mv pipfrosch-jquery.zip "${CWD}"/
rm -rf pipfrosch-jquery

popd
rmdir ${DIR}

exit 0
