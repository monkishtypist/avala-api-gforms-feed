# Gravity Forms to Avala API Add-On

A custom Gravity Forms add-on to submit Gravity Forms entries to Avala Aimbase CRM

## Installation ##

1. Download plugin Zip file
2. Unpack files to `avala-api-gforms-feed` directory in your WordPress plugins directory
3. Activate plugin
4. Go to Gravity Forms settings > Avala API and enter your API information
5. Go to Form Settings and create a new connection

## Requirements ##

This plugin requires a subscription to Aimbase CRM that supports API POSTs, as well as the Gravity Forms plugin for WordPress.

## Notes ##

This plugin is not officially supported (or appreciated) by Avala. They, however, do not have any such plugin of their own. So you can use this!

Monkishtypist offers zero warranty or support around this plugin. Some customization may be necessary for your instance.

## Customize this plugin ##

You can hard-code field mappings to match your needs. Simple edit the `feed_settings_fields` array in `avala-api-gforms-feed.php` around line 89.
