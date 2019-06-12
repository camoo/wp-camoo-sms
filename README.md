

# WP-CAMOO-SMS Plugin
A simple and powerful texting plugin for WordPress

You can add to WordPress, the ability to send SMS, member of SMS newsletter and send to the SMS.

To every events in WordPress, you can send sms through this plugin.

The usage of this plugin is completely free. You have to just have an account from service in the gateway lists that we support them.

Very easy Send SMS by PHP code:

```php
$to = array('01000000000');
$msg = "Hello kmer World! Déjà vu!";
wp_camoo_sms_send( $to, $msg );
```


# Features

* Send SMS to number(s), subscribers and wordpress users.
* Subscribe newsletter SMS.
* Send activation code to subscribe for complete subscription.
* Notification SMS when published new post to subscribers.
* Notification SMS when the new release of WordPress.
* Notification SMS when registering a new User.
* Notification SMS when get new comment.
* Notification SMS when user logged into wordpress.
* Notification SMS when user registered to subscription form.
* Integrate with (Contact form 7, WooCommerce, Easy Digital Downloads)
* Supported WP Widget for newsletter subscribers.
* Support Wordpress Hooks.
* Support WP REST API
* Import/Export Subscribers.
* Support GPG encyption to ensure an end  to end encryption between your server and ours.
* Handle SMS status rapport


# Installation

1. Upload `wp-camoo-sms` to the `/wp-content/plugins/` directory

Install Using Composer
```sh
cd wp-content/plugins

composer require camoo/wp-camoo-sms

mv vendor/camoo/wp-camoo-sms .

```

Install Manually

If you do not use Composer to manage plugins or other dependencies, you can install the plugin manually. Download the wp-camoo-sms-Full.zip file from the Releases page and extract the ZIP file to your plugins directory.

You can also git clone this repository, and run composer install in the plugin folder to pull in it's dependencies.

2. Activate the plugin through the 'Plugins' menu in WordPress
3. To display Subscribe goto Themes -> Widgets, and adding `SMS newsletter form` into your sidebar Or using this functions: `<?php wp_camoo_sms_subscribes(); ?>` into theme.
or using this Shortcode `[wp-sms-subscriber-form]` in Posts pages or Widget.
4. Using this functions for send manual SMS:

* First:`$to = array('Mobile Number');`
* `$msg = "Your Message";`
* `$isflash = true; // Only if wants to send flash SMS, else you can remove this parameter from function.`
* Send SMS: `wp_camoo_sms_send( $to, $msg, $isflash )`

# Actions
Run the following action when sending SMS with this plugin.
```php
wp_camoo_sms_send
```

Example: Send mail when send sms.
```php
function send_mail_when_send_sms($message_info) {
	wp_mail('you@mail.com', 'Send SMS', $message_info);
}
add_action('wp_camoo_sms_send', 'send_mail_when_send_sms');
```

Run the following action when subscribing a new user.
```php
wp_camoo_sms_add_subscriber
```

Example: Send sms to user when register a new subscriber.
```php
function send_sms_when_subscribe_new_user($name, $mobile) {
    $to = array($mobile);
    $msg = "Hi {$name}, Thanks for subscribe.";
    wp_camoo_sms_send( $to, $msg )
}
add_action('wp_camoo_sms_add_subscriber', 'send_sms_when_subscribe_new_user', 10, 2);
```

# Filters
You can use the following filter for modifying from the number.
```php
wp_camoo_sms_from
```

Example: Add 0 to the end sender number.
```php
function wp_camoo_sms_modify_from($from) {
	$from = $from . ' 0';
	
	return $val;
}
add_filter('wp_camoo_sms_from', 'wp_camoo_sms_modify_from');
```

You can use the following filter for modifying receivers number.
```php
wp_camoo_sms_to
```

Example: Add new number to get message.
```php
function wp_camoo_sms_modify_receiver($numbers) {
	$numbers[] = '67xxxxxxxx';
	
	return $numbers;
}
add_filter('wp_camoo_sms_to', 'wp_camoo_sms_modify_receiver');
```

You can use the following filter for modifying text message.
```php
wp_camoo_sms_msg
```

Example: Add signature to messages that are sent.
```php
function wp_camoo_sms_modify_message($message) {
	$message = $message . ' /n Powerby: WP-CAMOO-SMS';
	
	return $message;
}
add_filter('wp_camoo_sms_msg', 'wp_camoo_sms_modify_message');
```

# Rest API Endpoints
Add new subscribe to SMS newsletter
```sh
POST /camoosms/v1/subscriber/add
```
