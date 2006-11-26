#!/bin/bash

processDirectory() {
	baseName=`basename $1`;
	echo Entering dir : $baseName;
	for i in $1/*; do
		# get basename
		baseName=`basename $i`;
		# empty directory
		if [ "$i" = "$1/*" ]; then
			echo $baseName is an empty dir;
			break;
		fi
		# test skip
		if [ ! "$skip" = "" ]; then
			res=`echo $i | egrep $skip`;
			if [ ! $res = "" ]; then
				echo Skipping : $baseName;
				continue;
			fi
		fi
		# is this a directory?
		if [ -d "$i" ]; then
			processDirectory $i;
			continue;
		fi
		# is this a js script?
		res=`echo $i | egrep "\.js$"`;
		if [ $res = "" ]; then
			continue;
		fi
		msg="Processing $baseName...";
		ret=`php -q $scriptDir/jso.php $i`;
		if [ "$ret" = "OK" ]; then
			chmod 755 $i;
			echo $msg : OK;
		else
			cat <<error-message
Error processing $i:
----------------------------
$ret
error-message
			exit;
		fi
	done
}


# test parameter count
if [ $# -lt 3 ]; then
	echo Wrong parameter count. Aborting...;
	exit;
fi

# test source and target parameters
if [ ! -d $1 ] || [ ! -d $2 ] || [ ! -d $3 ]; then
	echo Script base, source and target must be directories. Aborting...;
	exit;
fi

# normalize "skip" attribute
if [ $# -eq 4 ]; then
	skip=$4;
else
	skip="";
fi

# prepare variables
scriptDir=`echo $1 | sed "s/\/$//"`;
scriptDir=$(cd "$scriptDir" && pwd);
baseSourceDir=`echo $2 | sed "s/\/$//"`;
baseTargetDir=`echo $3 | sed "s/\/$//"`;

# get user confirmation
cat <<user-confirmation
-------------------------------------
PHP2Go Web Development Framework
JavaScript Obfuscator

Please check the variables below and press [y] to start.
-------------------------------------
Base script directory: $scriptDir
Source JS directory: $baseSourceDir
Target JS directory: $baseTargetDir
Skip pattern: $skip
-------------------------------------
user-confirmation

read -e char;
if [ "$char" = "y" ]; then
	# change to source dir
	cd $baseSourceDir;

	# copy all files to the target dir
	cp -rf * $baseTargetDir;
	chmod 755 $baseTargetDir/* -R;

	# change back to the script dir
	cd $scriptDir;

	# enter recursion
	processDirectory $baseTargetDir;
	echo "Done. Exiting...";
else
	echo "Aborting...";
fi;
exit;