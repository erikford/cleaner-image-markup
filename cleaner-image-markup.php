<?php

/*
Plugin Name: Cleaner Image Markup
Plugin URI: http://www.wearepixel8.com
Description: A simple plugin that will clean up the HTML image markup produced by WordPress.
Version: 1.0.0
Author: We Are Pixel8
Author URI: http://www.wearepixel8.com
License:
	Copyright 2012 We Are Pixel8 <hello@wearepixel8.com>
	
	This program is free software; you can redistribute it and/or modify it under
	the terms of the GNU General Public License, version 2, as published by the Free
	Software Foundation.
	
	This program is distributed in the hope that it will be useful, but WITHOUT ANY
	WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
	PARTICULAR PURPOSE. See the GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software Foundation, Inc.,
	51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/

/*----------------------------------------------------------------------------*/
/* Remove Automatic Paragraph
/*----------------------------------------------------------------------------*/

add_filter( 'the_content', 'wap8_remove_autop', 10, 1 );

/**
 * Remove Automatic Paragraph
 *
 * Remove the automatic wrapping of images with a paragraph tag.
 *
 * @param $content
 *
 * @package Cleaner Image Markup
 * @version 1.0.0
 * @since 1.0.0
 * @author Erik Ford for We Are Pixel8 <@notdivisible>
 *
 */

function wap8_remove_autop( $content ) {
	
	$content = preg_replace( '/<p>\\s*?(<a .*?><img.*?><\\/a>|<img.*?>)?\\s*<\\/p>/s', '\1', $content );
	
	return $content;

}

/*----------------------------------------------------------------------------*/
/* Fewer Image Classes
/*----------------------------------------------------------------------------*/

add_filter( 'get_image_tag_class', 'wap8_fewer_image_classes', 10, 4 );

/**
 * Fewer Image Classes
 *
 * Reduce the amount of classes, applied to an image in a post, to simply
 * alignleft, alignright, aligncenter or alignnone.
 *
 * @param $class
 * @param $id
 * @param $align
 * @param $size
 *
 * @package Cleaner Image Markup
 * @version 1.0.0
 * @since 1.0.0
 * @author Erik Ford for We Are Pixel8 <@notdivisible>
 *
 */

function wap8_fewer_image_classes( $class, $id, $align, $size ) {
	
	$align = 'align' . esc_attr( $align );
	
	return $align;

}

/*----------------------------------------------------------------------------*/
/* Remove Width And Height Attributes
/*----------------------------------------------------------------------------*/

add_filter( 'post_thumbnail_html', 'wap8_remove_width_height_attr', 10, 1 );
add_filter( 'image_send_to_editor', 'wap8_remove_width_height_attr', 10, 1 );
add_filter( 'the_content', 'wap8_remove_width_height_attr', 10, 1 );

/**
 * Remove Width And Height Attributes
 *
 * Remove the width and height attributes from the img tag.
 *
 * @param $html
 *
 * @package Cleaner Image Markup
 * @version 1.0.0
 * @since 1.0.0
 * @author Erik Ford for We Are Pixel8 <@notdivisible>
 *
 */

function wap8_remove_width_height_attr( $html ) {

	$html = preg_replace( '/(width|height)=\"\d*\"\s/', "", $html );
    
	return $html;

}

/*----------------------------------------------------------------------------*/
/* HTML5 Image Figure And Figcaption Markup
/*----------------------------------------------------------------------------*/

add_filter( 'img_caption_shortcode', 'wap8_html5_image_caption', 10, 3 );

/**
 * HTML5 Image Figure And Figcaption Markup
 *
 * Return valid HTML5 markup for images with captions.
 *
 * @param $val
 * @param $attr
 * @param $content
 *
 * @package Cleaner Image Markup
 * @version 1.0.0
 * @since 1.0.0
 * @author Erik Ford for We Are Pixel8 <@notdivisible>
 *
 */

function wap8_html5_image_caption( $val, $attr, $content = null ) {
	
	extract( shortcode_atts( array(
		'id'		=> '',
		'align'		=> '',
		'width'		=> '',
		'caption'	=> '',
	), $attr ) );

	if ( 1 > ( int ) $width || empty( $caption ) ) {
		
		return $val;
	
	}

	if ( $id )
		$id = 'id="' . esc_attr( $id ) . '" ';

	return '<figure ' . $id . 'class="wp-caption ' . esc_attr( $align ) . '">' . do_shortcode( $content ) . '<figcaption class="wp-caption-text">'  . $caption . '</figcaption></figure>';

}

?>