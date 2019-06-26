#!/bin/bash
# INSTALL WP-CAMOO-SMS DEPENDENCIES
# -------------------------------------------------------------------------
# Copyright (c) 2019 CAMOO SARL
# -------------------------------------------------------------------------
PHP=$@
COMPOSER=`which composer`
SRC=`pwd`

# Find a CLI version of PHP
IN_PHP=(php php-cli /usr/local/bin/php)
getCliPhp() {
	for TESTEXEC in "${IN_PHP[@]}"
	do
		SAPI=`echo "<?= PHP_SAPI ?>" | $TESTEXEC 2>/dev/null`
		if [ "$SAPI" = "cli" ]
		then
			echo $TESTEXEC
			return
		fi
	done
	echo "Failed to find a CLI version of PHP; falling back to system standard php executable" >&2
	echo "php";
}
if [ -z "$PHP" ]
then
	PHP=$(getCliPhp)
fi

getPhp7Version()
{
	PHP7=$(${PHP} -v | grep cli |grep --only-matching --perl-regexp "^PHP 7\.\\d+\.\\d+" | awk '{print $2}')
	echo $(( ${PHP7//./0} ))
}

if [ -z "$COMPOSER" ]; then
	echo "Composer not found!"
	echo "['FAILED']"
	exit 0
fi

PHP7=$(getPhp7Version)
if [ -z "$PHP7" -o $PHP7 -lt 70100 ]; then

	if [ -d "${SRC}/camoo-sms/includes/gateways/libraries/camoo-legacy" ]; then
		cd ${SRC}/camoo-sms/includes/gateways/libraries/camoo-legacy
		${PHP} ${COMPOSER} require camoo/sms
	fi
else

	if [ -d "${SRC}/camoo-sms/includes/gateways/libraries/camoo" ]; then
		cd ${SRC}/camoo-sms/includes/gateways/libraries/camoo
		${PHP} ${COMPOSER} require camoo/sms
	fi
fi
echo "[DONE]"
exit 0;
