#!/bin/bash
# INSTALL WP-CAMOO-SMS DEPENDENCIES
# -------------------------------------------------------------------------
# Copyright (c) 2019 CAMOO SARL
# -------------------------------------------------------------------------
PHP=$@
COMPOSER=`which composer`

# Find a CLI version of PHP
IN_PHP="php php-cli /usr/local/bin/php"
getCliPhp() {
    for TESTEXEC in "${IN_PHP}"
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

if [ -z "$COMPOSER" ]; then
    echo "Composer not found!"
    echo "['FAILED']"
    exit 0
fi

if [ -d "./vendor" ]; then
	mv vendor/camoo/wp-camoo-sms .
	if [ -d "wp-camoo-sms/includes/gateways/libraries/camoo" ]; then
		cd wp-camoo-sms/includes/gateways/libraries/camoo
		${PHP} ${COMPOSER} require camoo/sms
	fi
	if [ -d "wp-camoo-sms/includes/gateways/libraries/camoo-legacy" ]; then
		cd wp-camoo-sms/includes/gateways/libraries/camoo-legacy
		${PHP} ${COMPOSER} require camoo/sms
	fi
fi
