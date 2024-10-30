<?php
/*
Plugin Name: Comment Notifications via SMS / text messages
Plugin URI: http://suhastech.com/comment-notifications-via-sms
Description: This plugin will send comment notifications via SMS / text messages.
Version: 0.1.1
Author: Suhas Sharma
Author URI: http://suhastech.com
*/

/*  Copyright 2010 Suhas Sharma <sharma@suhastech.com>

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


/*
 * Main action handler
 *
 * @package Comment Notifications via SMS
 * @since 0.1
 *
 * This plugin will send comment notifications via SMS / text messages.
 */
 
 
// Note: Most of the code is reused from the wordpress framework. 
function suhas_commentnotif() {
        add_options_page('Comment Notifications via SMS', 'Comment Notifications via SMS', 'manage_options',
            __FILE__, 'suhas_commentnotif_admin_page');
    }
	
	function suhas_commentnotif_admin_page() {

	
	?>
	<div class="wrap">
	<div id="icon-plugins" class="icon32"></div>
	<h2>Comment Notifications</h2>
	<h3>Powered by txtweb.com</h3>
	<?php	if(isset($_POST['ui']))
	{
	update_option('suhas_commentnotifui',$_POST['ui'] );
	?><div id="message" class="updated">Success, identifier code saved. You'll get SMS notifications if this is set right.</div><?php
	}
	?>
	<p>SMS "@comment" (without quotes) to "9243342000" [India] or "898932" [US or Canada] to acquire this Identifier code.</p>
	<form method="POST" action="">
    <input type="hidden" name="page" value="<?php echo $_GET['page'];?>" />
	The Identifier Code: <input type="text" maxlength="4" size="5" name="ui" value="<?php echo get_option('suhas_commentnotifui');?>" />
	<input type="submit" name="save" value="Save Option" class="button-primary"/>
	</form>
	
	</div>
	<?php
	}
add_action('admin_menu', 'suhas_commentnotif');


function sms_notifs_suhas($comment_id) {
	global $wpdb;

	$comment = get_comment($comment_id);
	$post = get_post($comment->comment_post_ID);    
	
	$notify_message  = sprintf( __('A new comment on the post #%1$s "%2$s" is waiting for your approval'), $post->ID, $post->post_title ) . "<br />";
	$notify_message .= sprintf( __('Author : %1$s'), $comment->comment_author ) . "<br />";
	$notify_message .= sprintf( __('E-mail : %s'), $comment->comment_author_email ) . "<br />";
	$notify_message .= __('Comment: ') . "<br />" . $comment->comment_content . "<br /><br />";

	$subject = sprintf( __('[%1$s] Please moderate: "%2$s"'), get_option('blogname'), $post->post_title );

	$notify_message = apply_filters('comment_moderation_text', $notify_message, $comment_id);
	$subject = apply_filters('comment_moderation_subject', $subject, $comment_id);
	
	
file_get_contents('http://suhastech.com/misc/comment/wpcomment.php?ui='.get_option ('suhas_commentnotifui').'&subject='.urlencode($subject).'&message='.urlencode($notify_message));
	
	
	return true;
}

add_action('comment_post', 'sms_notifs_suhas');