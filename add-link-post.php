<?php
/*
Plugin Name: Add Link Post
Plugin URI: http://dev.coziplace.com/free-wordpress-plugins/add-link-post
Description: A plugin that allows you to post any article or news or the likes from any webpage to your blog simply by specifying the URL of that webpage from a widget. 
Version: 1.0
Author: Narin Olankijanan
Author URI: http://dev.coziplace.com
License: GPLv2
Copyright 2012 Narin Olankijanan (email: narin@dekisugi.net)This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or any later version.This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.You should have received a copy of the GNU General Public License along with this progam; if not, write to the Free Software Foundation, Inc. 51 Franklin St, Fifth floor, Boston MA 02110-1301 USA
*/

add_action( 'widgets_init', 'dk_link_widgets' );
function dk_link_widgets() {	register_widget( 'Add_Link_Post' );	}

class Add_Link_Post extends WP_Widget {	

function __construct () {
		parent::__construct( 'add_link_post', 'Add_Link_Post', array ('description' => 'Add link post' ) );	
		}

function widget( $args, $instance ) {
	    extract( $args );
        echo $before_widget;
		if (current_user_can('publish_posts')) {
		?> <form method="post" action="">
		<?php wp_nonce_field('dk_rev_form','dk_u_form'); 
		?>
		Input URL of the webpage to share:<input type="text" name="url" placeholder="http://" ><input type="hidden" name="checker" value="1" ><input type="submit" name="publish" value="publish"></form>	    
		<?php	
		echo $after_widget;
		}
		}
}
add_action('template_redirect', 'dk_matching');

function dk_matching( $template ) {
  if (!empty($_POST['checker'])) {       
  dk_process();  
  } else {
  return $template;  }
  }
  
function dk_process() {
if(!empty($_POST['url']) && current_user_can('publish_posts') && wp_verify_nonce($_POST['dk_u_form'],'dk_rev_form')) {
  $url = $_POST['url'];
  $content = @file_get_contents($url);
  if ($content) {
  preg_match_all('/<[title|TITLE]>(.*?)<\/[title|TITLE]>/',$content,$output, PREG_PATTERN_ORDER);
  $title = $output[1][0];
  preg_match_all('/<[Pp].*>(.*?)<\/[Pp]>/', $content, $output,PREG_PATTERN_ORDER );
  $expt = (!empty($output[0][0])) ?  strip_tags($output[0][0]) : '';
  $expt = substr($expt, 0 ,300);
  $expt .= " <a href='{$url}'>Read more...</a>";
  $my_post = array(  'post_title'    => $title,  'post_content'  => $expt,  'post_status'   => 'publish');
  wp_insert_post( $my_post);
  wp_redirect( home_url() );
  exit;
  }
}
}