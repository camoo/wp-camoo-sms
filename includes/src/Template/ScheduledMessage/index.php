<?php
if (! defined('ABSPATH')) {
    exit;
} // Exit if accessed directly
?>

<div class="wrap">
    <h2><?php _e('Scheduled Message', 'wp-camoo-sms'); ?></h2>
    <div class="postbox-container wp-camoosms-container">
        <div class="meta-box-sortables">
            <div class="postbox">
                <h2 class="hndle wp-camoosms-header-form">
                    <span><?php _e('Schedule message form', 'wp-camoo-sms'); ?></span></h2>

                <div class="inside">
                    <form method="post" action="">
                        <?php wp_nonce_field('camoo_sms_scheduled', 'camoo_sms_schedueled'); ?>
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row">
                                    <label for="wp_get_sender"><?php _e('Send from', 'wp-camoo-sms'); ?>:</label>
                                </th>
                                <td>
                                    <input type="text" name="wp_get_sender" id="wp_get_sender" value="" maxlength="18"/>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">
                                    <label for="select_sender"><?php _e('Send to', 'wp-camoo-sms'); ?>:</label>
                                </th>
                                <td>
                                    <select name="wp_send_to" id="select_sender">
                                        <option value="wp_subscribe_username" id="wp_subscribe_username"><?php _e('Subscribe users', 'wp-camoo-sms'); ?></option>
                                        <option value="wp_users" id="wp_users"><?php _e('Wordpress Users', 'wp-camoo-sms'); ?></option>
                                        <option value="wp_role" id="wp_role"<?php $mobile_field = \CAMOO_SMS\Option::getOption('add_mobile_field');
                                        if (empty($mobile_field) or $mobile_field != 1) {
                                            echo 'disabled title="' . __('To enable this item, you should enable the Mobile number field in the Settings > Features', 'wp-camoo-sms') . '"';
                                        } ?>><?php _e('Role', 'wp-camoo-sms'); ?></option>
                                        <option value="wp_tellephone" id="wp_tellephone"><?php _e('Number(s)', 'wp-camoo-sms'); ?></option>
                                    </select>

                                    <?php if (! empty($mobile_field) or $mobile_field == 1) { ?>
                                        <select name="wpcamoosms_group_role" class="wpsms-value wprole-group">
                                            <?php
                                            foreach ($wpcamoosms_list_of_role as $key_item => $val_item) :
                                                ?>
                                                <option value="<?php echo $key_item; ?>"<?php if ($val_item['count'] < 1) {
                                                    echo " disabled";
                                                } ?>><?php _e($val_item['name'], 'wp-camoo-sms'); ?>
                                                    (<?php echo sprintf(__('<b>%s</b> Users have mobile number.', 'wp-camoo-sms'), $val_item['count']); ?>
                                                    )
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php } ?>

                                    <span class="wpsms-value wpsms-users">
                        <span><?php echo 'ddd';?></span>
                    </span>
                                    <span class="wpsms-value wpsms-numbers">
                                        <div class="clearfix"></div>
                                        <textarea cols="80" rows="5" style="direction:ltr;margin-top 5px;" id="wp_get_number" name="wp_get_number"></textarea>
                                        <div class="clearfix"></div>
                                        <span style="font-size: 14px">ddd</span>
                                    </span>
                                </td>
                            </tr>

                            <tr valign="top">
                                <th scope="row">
                                    <label for="wp_get_message"><?php _e('Message', 'wp-camoo-sms'); ?>:</label>
                                </th>
                                <td>
                                    <textarea dir="auto" cols="80" rows="5" name="wp_get_message" id="wp_get_message"></textarea><br/>
                                    <p class="number">
                                        <?php echo __('Your account credit', 'wp-camoo-sms') . ': XAF 00'; ?>
                                    </p>
                                </td>
                            </tr>
                                <tr>
                                    <td><?php _e('SMS route', 'wp-camoo-sms'); ?>:</td>
                                    <td>
                                        <input type="radio" id="route_premium" name="wp_route" value="premium" checked="checked"/>
                                        <label for="flash_yes"><?php _e('Premium', 'wp-camoo-sms'); ?></label>
                                        <input type="radio" id="route_classic" name="wp_route" value="classic"/>
                                        <label for="flash_no"><?php _e('Classic', 'wp-camoo-sms'); ?></label>
                                        <br/>
                                        <p class="description"><?php echo sprintf(__("The SMS route that is used to send the message. <span style='color:#ff0000;'>The classic route works only for cameroonian mobile phone numbers.</span> <a href='%s' target='_blank'>Check Wiki for more explanation</a>", 'wp-camoo-sms'), 'https://github.com/camoo/sms/wiki/Send-a-message#optional-parameters'); ?></p>
                                    </td>
                                </tr>
                            <tr>
                                <td>
                                    <p class="submit" style="padding: 0;">
                                        <input type="submit" class="button-primary" name="sendSMS" value="<?php _e('Send SMS', 'wp-camoo-sms'); ?>"/>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
