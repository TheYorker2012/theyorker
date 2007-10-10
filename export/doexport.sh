#!/bin/sh
echo "Updating checkout..."
pushd .. > /dev/null
svn up
popd > /dev/null

echo "Getting revision number..."
let rev=`svn info | awk '/Revision/ { print $2 }'`

echo -n "Exporting SVN tree..."
svn export .. r${rev}

echo "Copying config files into tree"
cp -R template/* r${rev}/

echo "Creating symlink"
ln -s r${rev}/public_html new_html

echo "Creating tarball..."
tar -cf yorker.tar r${rev} new_html

echo "Copying tarball..."
scp yorker.tar yorker@www.theyorker.co.uk:

echo "Deleting files..."
rm -rf r${rev} > /dev/null
rm yorker.tar > /dev/null
rm new_html > /dev/null
