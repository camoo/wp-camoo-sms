<?php

namespace CAMOO_SMS;

if (! defined('ABSPATH')) {
    exit;
} // Exit if accessed directly
use CAMOO_SMS\Admin\Helper;
class Option
{

	public const MAIN_SETTING_KEY = 'wp_camoo_sms_settings';

    /**
     * Get the whole Plugin Options
     *
     * @param string $setting_name setting name
     *
     * @return mixed|void
     */
    public static function getOptions($setting_name=null)
    {
		if (null === $setting_name) {
			$setting_name = static::MAIN_SETTING_KEY;
		}
        return get_option($setting_name);
    }

    /**
     * Get the only Option that we want
     *
     * @param $option_name
     * @param string $setting_name
     *
     * @return string
     */
    public static function getOption($option_name, $setting_name=null)
    {
        if (null === $setting_name) {

            $wpcamoosms_option = self::getOptions();

            return isset($wpcamoosms_option[ $option_name ]) ? $wpcamoosms_option[ $option_name ] : '';
        }
        $options = self::getOptions($setting_name);

        return isset($options[ $option_name ]) ? $options[ $option_name ] : '';
    }

    /**
     * Add an option
     *
     * @param $option_name
     * @param $option_value
     */
    public static function addOption($option_name, $option_value)
    {
        add_option($option_name, $option_value);
    }

    /**
     * Update Option
     *
     * @param $key
     * @param $value
     */
    public static function updateOption($key, $value)
    {
        $options         = self::getOptions();
        $options[ $key ] = $value;

        update_option(static::MAIN_SETTING_KEY, $options);
    }

    public static function afterFind($xData)
    {
        if (is_array($xData)) {
            if (!empty($xData['gateway_username'])) {
                $xData['gateway_username'] = Helper::decrypt($xData['gateway_username']);
            }
            if (!empty($xData['gateway_password'])) {
                $xData['gateway_password'] = Helper::decrypt($xData['gateway_password']);
            }
        }
        return $xData;
    }

    public static function beforeSave($option=[])
    {
        // ENCRYPT API SECRET KEY
        if (!empty($option['gateway_password'])) {
            $option['gateway_password'] = Helper::encrypt($option['gateway_password']);
        }
        if (!empty($option['gateway_username'])) {
            $option['gateway_username'] = Helper::encrypt($option['gateway_username']);
        }
		return $option;
    }
}
