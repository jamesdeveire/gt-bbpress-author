<?php
/**
 * Plugin Name: GT | bbPress Author Drop Down
 * Plugin URI: http://genbutheme.com/
 * Description: Enable author drop down in BBPress admin to change user for topics and reply.
 * Version: 0.1
 * Author: David Chandra Purnama
 * Author URI: http://shellcreeper.com/
 *
 * Sometimes you need to change the author of topic and reply in BBPress,
 * this plugin will add an author metabox in topic and reply edit screen
 * in WordPress admin so you can select topic or reply poster from all registered member.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms  
 * of the GNU General Public License version 2, as published by the Free Software Foundation.
 * You may NOT assume that you can use any other version of the GPL.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @version 0.1
 * @author David Chandra Purnama <david@turtlepod.org>
 * @copyright Copyright (c) 2012, David Chandra Purnama
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* hook post type feature to init, as recomendation in codex */
add_action( 'init', 'gt_bbpress_add_author' );

/**
 * Add Author Feature in BBPress Topics and Replay
 * @since 0.1
 */
function gt_bbpress_add_author() {

	/* add author support for post type "topic" and "reply" */
	add_post_type_support( 'topic', 'author' );
	add_post_type_support( 'reply', 'author' );
}


/* filter to change drop down user in author metabox */
add_filter('wp_dropdown_users', 'gt_bbpress_author_metabox');

/**
 * Add all member in Author Meta Box
 * as default WordPress only add member with "author" capability
 * in author metabox
 *
 * BBPress use function "bbp_get_topic_post_type" and "bbp_get_reply_post_type"
 * to determined post type used as topic and reply, ideally this should be used as
 * conditional check. 
 * we wont use that,and set it directly to "topic" and "reply"
 *
 * The one we are filtering is 'post_author_meta_box'
 * defined in wp-admin\includes\meta-boxes.php
 *
 * @since 0.1
 */
function gt_bbpress_author_metabox( $output ){

	/* globalize post and post type object. */
	global $post,$post_type;

	/* conditional check if it's a topic or reply post type, 
	 * so it only apply in replay and topic edit screen
	 */
	if ( is_admin() && ($post_type == 'topic' ||  $post_type == 'reply') ) {

		/* get all user. */
		$users = get_users();

		/* start with select output */
		$output = '<select id="post_author_override" name="post_author_override" class="">';

		/* for each user create an option drop down */
		foreach($users as $user){

			/* get topic/reply submitter as selected author */
			$selected = ( $post->post_author == $user->ID )?"selected='selected'":'';

			/*  create the options drop down  */
			$output .= '<option value="'.$user->ID.'"'.$selected.'>'.$user->user_login.'</option>';
		}
		/* wrap it up. */
		$output .= "</select>";
	}
	/* and return the output. */
	return $output;
}

/* activate WP Github Plugin Updater to init hook */
add_action('init', 'gt_bbpress_author_updater_init');

/**
 * WP Github Plugin Updater Class
 * Semi-automated plugin updater for GitHub hosted plugin
 * 
 * @link https://github.com/jkudish/WordPress-GitHub-Plugin-Updater
 * @author Joachim Kudish
 * @version: 1.4
 *
 * @since 0.2.1
 */
function gt_bbpress_author_updater_init() {

	/* load plugin updater class */
	include_once( trailingslashit( plugin_dir_path( __FILE__) ) . 'updater.php' );

	/* test only: force to delete transient for every page load */
	//define('WP_GITHUB_FORCE_UPDATE', true);

	/**
	 * note the use of is_admin() to double check
	 * that this is happening in the admin
	 */
	if (is_admin()) {

		$config = array(
			'slug' => plugin_basename(__FILE__),
			'proper_folder_name' => 'gt-bbpress-author',
			'api_url' => 'https://api.github.com/repos/turtlepod/gt-bbpress-author',
			'raw_url' => 'https://raw.github.com/turtlepod/gt-bbpress-author/master',
			'github_url' => 'https://github.com/turtlepod/gt-bbpress-author',
			'zip_url' => 'https://github.com/turtlepod/gt-bbpress-author/zipball/master',
			'sslverify' => true,
			'requires' => '3.0.0',
			'tested' => '3.4.2',
			'readme' => 'README.md'
		);

		new WPGitHubUpdater($config);
	}
}