<?php

/*
Plugin Name: Cleaner Image Markup
Plugin URI: http://www.wearepixel8.com
Description: A simple plugin that will clean up the HTML image markup produced by WordPress.
Version: 1.0.4
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
 * @return $content
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
/* Remove Width And Height Attributes
/*----------------------------------------------------------------------------*/

add_filter( 'post_thumbnail_html', 'wap8_remove_width_height_attr', 10, 1 );
add_filter( 'the_content', 'wap8_remove_width_height_attr', 10, 1 );

/**
 * Remove Width And Height Attributes
 *
 * Remove the width and height attributes from the img tag.
 *
 * @param $html
 * @return $html
 *
 * @package Cleaner Image Markup
 * @version 1.0.0
 * @since 1.0.3
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
 * Return valid HTML5 markup for images with captions by filtering the WordPress
 * Caption shortcode.
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
		'id'      => '',
		'align'   => '',
		'width'   => '',
		'caption' => '',
	), $attr ) );

	if ( 1 > ( int ) $width || empty( $caption ) ) {
		
		return $val;
	
	}

	if ( $id )
		$id = 'id="' . esc_attr( $id ) . '" ';

	return '<figure ' . $id . 'class="wp-caption ' . esc_attr( $align ) . '">' . do_shortcode( $content ) . '<figcaption class="wp-caption-text">'  . $caption . '</figcaption></figure>';

}

/*----------------------------------------------------------------------------*/
/* Tidy Gallery
/*----------------------------------------------------------------------------*/

add_filter( 'post_gallery', 'wap8_tidy_gallery', 10, 2 );

/**
 * Tidy Gallery
 *
 * Remove inline styles for the default WordPress Gallery by filtering the
 * WordPress Gallery shortcode.
 *
 * @param $output
 * @param $attr
 * @return $output
 *
 * @package Cleaner Image Markup
 * @version 1.0.1
 * @since 1.0.4
 * @author Erik Ford for We Are Pixel8 <@notdivisible>
 *
 */



function wap8_tidy_gallery( $output, $attr ) {

	global $post;
	
	static $instance = 0;
	$instance++;
	
	// we're trusting author input, so let's at least make sure it looks like a valid orderby statement
	if ( isset( $attr['orderby'] ) ) {
	
		$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
		
		if ( !$attr['orderby'] ) unset( $attr['orderby'] );
			
	}
	
	extract( shortcode_atts( array(
		'order'      => 'ASC',
		'orderby'    => 'menu_order ID',
		'id'         => $post->ID,
		'itemtag'    => 'dl',
		'icontag'    => 'dt',
		'captiontag' => 'dd',
		'columns'    => 3,
		'size'       => 'thumbnail',
		'include'    => '',
		'exclude'    => ''
	), $attr ) );
	
	$id = intval( $id );
	
	if ( 'RAND' == $order ) $orderby = 'none';
	
	if ( !empty( $include ) ) {
	
		$include = preg_replace( '/[^0-9,]+/', '', $include );
		
		// arguments for included images
		$incimgs = array(
			'include'        => $include,
			'post_status'    => 'inherit',
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'order'          => $order,
			'orderby'        => $orderby
		);
		
		$_attachments = get_posts( $incimgs );
		
		$attachments = array();
		
		foreach ( $_attachments as $key => $val ) {
		
			$attachments[$val->ID] = $_attachments[$key];
			
		}
	
	} elseif ( !empty( $exclude ) ) {
	
		$exclude = preg_replace( '/[^0-9,]+/', '', $exclude );
		
		// arguments for excluded images
		$eximgs = array(
			'post_parent'    => $id,
			'exclude'        => $exclude,
			'post_status'    => 'inherit',
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'order'          => $order,
			'orderby'        => $orderby
		);
		
		$attachments = get_children( $eximgs );
		
	} else {
	
		// arguments for all images
		$allimgs = array(
			'post_parent'    => $id,
			'post_status'    => 'inherit',
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'order'          => $order,
			'orderby'        => $orderby
		);
	
		$attachments = get_children( $allimgs );
		
	}
	
	if ( empty( $attachments ) ) return '';

	if ( is_feed() ) {
	
		$output = "\n";
		
		foreach ( $attachments as $att_id => $attachment )
		
			$output .= wp_get_attachment_link( $att_id, $size, true ) . "\n";
			
		return $output;
		
	}
	
	// escape the gallery tags
	$itemtag = tag_escape( $itemtag );
	$icontag = tag_escape( $icontag );
	$captiontag = tag_escape( $captiontag );
	
	// store and sanitize the set columns - props @bradyvercher
	$columns = ( absint( $columns ) ) ? absint( $columns ) : 1;
	$i = 0;
	
	// the wrapper that contains the opening gallery div with the unique gallery instance and id - props @bradyvercher
	$wrapper = "\n\t\t\t<div id='gallery-{$instance}' class='gallery gallery-{$id}'>";
	
	// allow devs to filter the output - props @bradybercher
	$output = apply_filters( 'wap8_tidy_gallery_output', $wrapper, $attachments, $attr, $instance );
	
	// skip the output generation, if a hook modified the output - props @bradyvercher
	if ( empty( $output ) || $wrapper == $output ) {
	
		// if $output is empty for some reason, restart the output with the default wrapper - props @bradyvercher
		if ( empty( $output ) ) {
			
			$output = $wrapper;
			
		}
	
	// open the gallery div
	$output = $wrapper;
	
		// loop through each attachment
		foreach ( $attachments as $id => $attachment ) {
	
			// open each gallery row
			if ( $i % $columns == 0 )
				$output .= "\n\t\t\t\t<div class='gallery-row tidy-gallery-col-{$columns} clear'>";
			
			// open each gallery item
			$output .= "\n\t\t\t\t\t<{$itemtag} class='gallery-item'>";
		
			// open the element that wraps the image
			$output .= "\n\t\t\t\t\t\t<{$icontag} class='gallery-icon'>";
	
			// add the image
			$link = ( ( isset( $attr['link'] ) && 'file' == $attr['link'] ) ? wp_get_attachment_link( $id, $size, false, false ) : wp_get_attachment_link( $id, $size, true, false ) );
			$output .= $link;
		
			// close the element that wraps the image
			$output .= "</{$icontag}>";
		
			// get the caption
			$caption = wptexturize( $attachment->post_excerpt );
		
			// if caption is set
			if ( !empty( $caption ) )
				$output .= "\n\t\t\t\t\t\t<{$captiontag} class='wp-caption-text gallery-caption'>{$caption}</{$captiontag}>";
		
			// close individual gallery item
			$output .= "\n\t\t\t\t\t</{$itemtag}>";
		
			// close gallery row
			if ( ++$i % $columns == 0 )
				$output .= "\n\t\t\t\t</div>";
	
		}
	
		// close gallery row
		if ( $i % $columns !== 0 )
			$output .= "\n\t\t\t</div>";

		// close gallery div
		$output .= "\n\t\t\t</div><!-- .gallery -->\n";
	
	}
	
    return $output;

}

?>