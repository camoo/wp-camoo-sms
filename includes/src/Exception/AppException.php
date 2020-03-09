<?php

namespace WP_CAMOO\SMS\Exception;

if (! defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

use RuntimeException;

/**
 * Class AppException
 * @author CamooSarl
 */
class AppException extends RuntimeException
{
}
