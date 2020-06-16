#!/bin/bash

# WARNING - this packages what is in the development branch
#  and is intended only for testing purposes.
#
# Use mkzip.sh to package for production, or better yet,
#  install from WordPress.

# creates a zip archive from master from github.

CWD=`pwd`

DIR=`mktemp -d -p /tmp plugin.XXXXXXXX`

pushd ${DIR}

git clone https://github.com/pipfrosch/pipfrosch-jquery.git

cd pipfrosch-jquery
git checkout pipjq

# git specific file / directory
rm -f .gitignore
rm -rf .git
# These two files are not needed in packaged plugin
rm -f mkzip.sh
rm -f README.md

VERSION=`grep "Version:" pipfrosch-jquery.php |head -1 |cut -d':' -f2 |tr -d '[:space:]'`

cd ..

zip -r pipfrosch-jquery.zip pipfrosch-jquery

mv pipfrosch-jquery.zip "${CWD}"/pipfrosch-jquery-v${VERSION}.zip
rm -rf pipfrosch-jquery

popd
rmdir ${DIR}

exit 0
