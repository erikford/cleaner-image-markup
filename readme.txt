=== Cleaner Image Markup ===
Contributors: wearepixel8
Tags: images, image caption, figure, figcaption, gallery
Requires at least: 3.1
Compatible up to: 3.4.2
Tested up to: 3.4.2
Stable tag: 1.0.2
License: GPLv2

A simple plugin that will clean up the HTML image markup produced by WordPress.

== Description ==

Cleaner Image Markup will tidy up the HTML image markup for the following:

* Filter out the automatic wrapping of images with a `p` tag when inserted into posts
* Filter out the `width` and `height` attributes from post images.
* Return valid HTML5 caption markup using `figure` and `figcaption` with using the `[caption]` shortcode.
* Filter out the inline styles printed when using the `[gallery]` shortcode.

**Please note that Cleaner Image Markup will not alter the appearance of images within your post or pages.**

== Installation ==

You can install Cleaner Image Markup either via the WordPress Dashboard or by uploading the extracted `cleaner-image-markup` folder to your `/wp-conten/plugins/` directory. Once the plugin has been successfully installed, simply activate the plugin through the Plugins menu in your WordPress Dashboard.

== Frequently Asked Questions ==

== Screenshots ==

== Changelog ==

= 1.0.0 =
* Initial release

= 1.0.1 =
* Removed the filter reducing the amount of classes applied to images in a post
* Added a filter to prevent inline styles being printed when using the `[gallery]` shortcode.

= 1.0.1 =
* Developers can now filter the gallery shortcode output.
* Improved sanitization for gallery column settings.