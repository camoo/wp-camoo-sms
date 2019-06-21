<?php

namespace CAMOO_SMS\Admin;

class Helper
{

    /**
     * Show Admin Wordpress Ui Notice
     *
     * @param string $text where Show Text Notification
     * @param string $model Type Of Model from list : error / warning / success / info
     * @param boolean $close_button Check Show close Button Or false for not
     * @param  boolean $echo Check Echo or return in function
     * @param string $style_extra add extra Css Style To Code
     *
     * @author Mehrshad Darzi
     * @return string Wordpress html Notice code
     */
    public static function notice($text, $model = "info", $close_button = true, $echo = true, $style_extra = 'padding:12px;')
    {
        $text = '
        <div class="notice notice-' . $model . '' . ($close_button === true ? " is-dismissible" : "") . '">
           <div style="' . $style_extra . '">' . $text . '</div>
        </div>
        ';
        if ($echo) {
            echo $text;
        } else {
            return $text;
        }
    }

    public static function adminUrl($args, $adminFile='admin.php')
    {
        return add_query_arg($args, WP_CAMOO_SMS_ADMIN_URL . $adminFile);
    }

    public static function getPhpVersion()
    {
        if (!defined('CAMOO_SMS_PHP_ID')) {
            $version = explode('.', PHP_VERSION);
            define('CAMOO_SMS_PHP_ID', (int) $version[0] * 10000 + (int) $version[1] * 100 + (int) $version[2]);
        }

        return CAMOO_SMS_PHP_ID;
    }

    public static function encrypt($string, $sCipher='AES-256-CBC')
    {
        if (empty($string)) {
            return '';
        }
        if (!defined('NONCE_SALT')) {
            return $string;
        }
        $key = hash('sha256', NONCE_SALT);
        $ivlen = openssl_cipher_iv_length($sCipher);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($string, $sCipher, $key, OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
        return base64_encode($iv.$hmac.$ciphertext_raw);
    }

    public static function decrypt($string, $sCipher='AES-256-CBC')
    {
        if (empty($string) || !self::isBase64Encoded($string)) {
            return '';
        }
        if (!defined('NONCE_SALT')) {
            return self::isBase64Encoded($string) ? null : $string;
        }
        $enc = base64_decode($string);
        $key = hash('sha256', NONCE_SALT);
        $ivlen = openssl_cipher_iv_length($sCipher);
        $iv = substr($enc, 0, $ivlen);
        $hmac = substr($enc, $ivlen, $sha2len=32);
        $ciphertext_raw = substr($enc, $ivlen+$sha2len);
        return openssl_decrypt($ciphertext_raw, $sCipher, $key, OPENSSL_RAW_DATA, $iv);
    }

    public static function isBase64Encoded($string)
    {
        return base64_encode(base64_decode($string)) === $string;
    }

    public static function sanitizer($value, $type=null)
    {
        $hMapTypes = [
            'number' => function ($value) {
                return (int) sanitize_key($value);
            },
            'text' => function ($value) {
                return sanitize_text_field($value);
            },
            'email' => function ($value) {
                return sanitize_email($value);
            },
            'textarea' => function ($value) {
                return sanitize_textarea_field($value);
            },
        ];

        if (null === $type || !array_key_exists($type, $hMapTypes)) {
            return sanitize_text_field($value);
        }
        return call_user_func($hMapTypes['text'], $value);
    }

    public static function satanizeRequest($filter, $input=[])
    {
        if (has_filter($filter, [\CAMOO_SMS\Admin\Helper::class, 'sanitizer'])) {
            foreach ($input as $key => $value) {
                if (is_array($value)) {
                    $input[$key] = self::satanizeRequest($filter, $value);
                    continue;
                }
                $input[$key] = apply_filters($filter, $value);
            }
        }
        return $input;
    }
}
