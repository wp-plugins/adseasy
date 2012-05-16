<?php
/*
Plugin Name: Ads Easy
Plugin URI: http://wasistlos.waldemarstoffel.com/plugins-fur-wordpress/ads-easy
Description: If you don't want to have Ads in your posts and you don't need other stats than those you get from wordpress and your adservers, this is the most easy solution. Place the code you get to the widget, style the widget and define, on what pages it shows up. 
Version: 2.2
Author: Waldemar Stoffel
Author URI: http://www.atelier-fuenf.de
License: GPL3
Text Domain: adseasy 
*/

/*  Copyright 2011 - 2012 Waldemar Stoffel  (email : stoffel@atelier-fuenf.de)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

/* Stop direct call */

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) die(__('Sorry, you don&#39;t have direct access to this page.'));

/* attach JavaScript file for textarea resizing */

function ae_js_sheet() {
	
	wp_enqueue_script('ta-expander-script', plugins_url('ta-expander.js', __FILE__), array('jquery'), '2.0', true);

}

add_action('admin_print_scripts-widgets.php', 'ae_js_sheet');

//Additional links on the plugin page

add_filter('plugin_row_meta', 'ae_register_links',10,2);

function ae_register_links($links, $file) {
	
	$base = plugin_basename(__FILE__);
	if ($file == $base) {
		$links[] = '<a href="http://wordpress.org/extend/plugins/adseasy/faq/" target="_blank">'.__('FAQ', 'adseasy').'</a>';
		$links[] = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=VRMSV3NXQDXSA" target="_blank">'.__('Donate', 'adseasy').'</a>';
	}
	
	return $links;

}

add_filter( 'plugin_action_links', 'ae_plugin_action_links', 10, 2 );

function ae_plugin_action_links( $links, $file ) {
	
	$base = plugin_basename(__FILE__);
	
	if ($file == $base) array_unshift($links, '<a href="'.admin_url( 'plugins.php?page=ads-easy-settings' ).'">'.__('Settings', 'adseasy').'</a>');

	return $links;

}

/**
 *
 * Getting the Adsense Tags in the defined areas of the code and create hooks for other plugins
 *
 */
if (!defined('AE_AD_TAGS')) :
		 
	$ae_options = get_option('ae_options');
	
	define('AE_AD_TAGS', $ae_options['use_google_tags']);
	
endif;

if (AE_AD_TAGS == 1) :

	add_action( 'wp_head', 'ae_header', 1000);
	add_action( 'loop_start', 'ae_loop_start');
	add_action( 'get_sidebar', 'ae_sidebar');
	add_action( 'dynamic_sidebar', 'ae_sidebar');
	add_action( 'wp_footer', 'ae_footer');
	add_action( 'wp_footer', 'ae_end_tag', 1000);
	
	// hooks for other plugins
	
	add_action( 'google_start_tag', 'ae_start_tag');
	add_action( 'google_ignore_tag', 'ae_ignore_tag');
	add_action( 'google_end_tag', 'ae_end_tag');
	
	// adding short code 
	
	add_shortcode( 'ae_ignore_tag', 'ae_wrap_ignore');
	
	// get the tinymce plugin
	include_once('tinymce/tinymce.php');
	
endif;

/**
 *
 * Getting the Adsense Tags in the defined areas of the code and create hooks for other plugins
 *
 */

// Header
function ae_header() {
	
	$ae_options = get_option('ae_options');
	
	echo "<!-- Google AdSense Tags powered by Waldemar Stoffel's AdEasy http://wasistlos.waldemarstoffel.com/plugins-fur-wordpress/ads-easy -->\r\n";
	
	if ($ae_options['ae_header'] == '1') do_action('google_start_tag');
	
	else do_action('google_ignore_tag');
	
}

function ae_loop_start() {
	
	global $ba_tag;
	
	$ae_options = get_option('ae_options');
	
	if ($ae_options['ae_loop'] == '1' && $ba_tag == 'ignore') : 
	
		do_action('google_end_tag');
		
		do_action('google_start_tag');
		
	endif;
	
	if ($ae_options['ae_loop'] == false && $ba_tag == 'start') : 
	
		do_action('google_end_tag');
		
		do_action('google_ignore_tag');
		
	endif;
	
}

function ae_sidebar() {
	
	global $ba_tag;
	
	$ae_options = get_option('ae_options');
	
	if ($ae_options['ae_sidebar'] == '1' && $ba_tag == 'ignore') : 
	
		do_action('google_end_tag');
		
		do_action('google_start_tag');
		
	endif;
	
	if ($ae_options['ae_sidebar'] == false && $ba_tag == 'start') : 
	
		do_action('google_end_tag');
		
		do_action('google_ignore_tag');
		
	endif;
	
}

function ae_footer() {
	
	global $ba_tag;
	
	$ae_options = get_option('ae_options');
	
	if ($ae_options['ae_footer'] == '1' && $ba_tag == 'ignore') : 
	
		do_action('google_end_tag');
		
		do_action('google_start_tag');
		
	endif;
	
	if ($ae_options['ae_footer'] == false && $ba_tag == 'start') : 
	
		do_action('google_end_tag');
		
		do_action('google_ignore_tag');
		
	endif;
	
}

// Hook functions
function ae_start_tag() {
	
	global $ba_tag;
	
	$eol = "\r\n";

  	echo '<!-- google_ad_section_start -->'.$eol;
	
	$ba_tag = 'start';
	
}

function ae_ignore_tag() {
	
	global $ba_tag;
	
	$eol = "\r\n";

  	echo '<!-- google_ad_section_start(weight=ignore) -->'.$eol;
	
	$ba_tag = 'ignore';
	
}

function ae_end_tag() {
	
	global $ba_tag;
	
	$eol = "\r\n";

  	echo $eol.'<!-- google_ad_section_end -->'.$eol;
	
	$ba_tag = 'end';
	
}

/**
 *
 * shortcode for the ignore tags
 *
 */
function ae_wrap_ignore($atts, $content = null){
	
	$eol = "\r\n";
	
	return $eol.'<!-- google_ad_section_end -->'.$eol.'<!-- google_ad_section_start(weight=ignore) -->'.$eol.do_shortcode($content).$eol.'<!-- google_ad_section_end -->'.$eol.'<!-- google_ad_section_start -->'.$eol;
}
 
/**
 *
 * Extending the widget class
 *
 */

class Ads_Easy_Widget extends WP_Widget {
	
	function Ads_Easy_Widget() {
		
		$widget_opts = array( 'description' => __('You can show ads in your sidebars and other widgetareas with this widget. Define, on what kind of pages they will show up.', 'adseasy') );
		$control_opts = array( 'width' => 400 );
		
        parent::WP_Widget(false, $name = 'Ads Easy', $widget_opts, $control_opts);
    }
    
	
	function form($instance) {
	
	$defaults = array( 'homepage' => true, 'category' => true );
	
	$instance = wp_parse_args( (array) $instance, $defaults );
	
	$title = esc_attr($instance['title']);
	$name = esc_attr($instance['name']);
	$adblock = $instance['adblock'];
	$style=esc_attr($instance['style']);
	$homepage=esc_attr($instance['homepage']);
	$frontpage=esc_attr($instance['frontpage']);
	$page=esc_attr($instance['page']);
	$category=esc_attr($instance['category']);
	$single=esc_attr($instance['single']);
	$date=esc_attr($instance['date']);
	$tag=esc_attr($instance['tag']);
	$attachment=esc_attr($instance['attachment']);
	$taxonomy=esc_attr($instance['taxonomy']);
	$author=esc_attr($instance['author']);
	$search=esc_attr($instance['search']);
	$not_found=esc_attr($instance['not_found']);
	$logged_in=esc_attr($instance['logged_in']);
	
?>
 
<p>
 <label for="<?php echo $this->get_field_id('name'); ?>">
 <?php _e('Title (will be displayed in blog):', 'adseasy'); ?>
 <input id="<?php echo $this->get_field_id('name'); ?>" name="<?php echo $this->get_field_name('name'); ?>" type="text" value="<?php echo $name; ?>" />
 </label>
</p>
<p>
 <label for="<?php echo $this->get_field_id('title'); ?>">
 <?php _e('Adname (internal widgettitle):', 'adseasy'); ?>
 <input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
 </label>
</p>
<p>
  <?php _e('Check, where you want to show the widget. By default, it is showing on the homepage and the category pages:', 'adseasy'); ?>
</p>
<fieldset>
<p>
  <label for="<?php echo $this->get_field_id('homepage'); ?>">
    <input id="<?php echo $this->get_field_id('homepage'); ?>" name="<?php echo $this->get_field_name('homepage'); ?>" <?php if(!empty($homepage)) echo 'checked="checked"'; ?> type="checkbox" />
    &nbsp;
    <?php _e('Homepage', 'adseasy'); ?>
  </label>
  <br />
  <label for="<?php echo $this->get_field_id('frontpage'); ?>">
    <input id="<?php echo $this->get_field_id('frontpage'); ?>" name="<?php echo $this->get_field_name('frontpage'); ?>" <?php if(!empty($frontpage)) echo 'checked="checked"'; ?> type="checkbox" />
    &nbsp;
    <?php _e('Frontpage (e.g. a static page as homepage)', 'adseasy'); ?>
  </label>
  <br />
  <label for="<?php echo $this->get_field_id('page'); ?>">
    <input id="<?php echo $this->get_field_id('page'); ?>" name="<?php echo $this->get_field_name('page'); ?>" <?php if(!empty($page)) echo 'checked="checked"'; ?> type="checkbox" />
    &nbsp;
    <?php _e('&#34;Page&#34; pages', 'adseasy'); ?>
  </label>
  <br />
  <label for="<?php echo $this->get_field_id('category'); ?>">
    <input id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('category'); ?>" <?php if(!empty($category)) echo 'checked="checked"'; ?> type="checkbox" />
    &nbsp;
    <?php _e('Category pages', 'adseasy'); ?>
  </label>
  <br />
  <label for="<?php echo $this->get_field_id('single'); ?>">
    <input id="<?php echo $this->get_field_id('single'); ?>" name="<?php echo $this->get_field_name('single'); ?>" <?php if(!empty($single)) echo 'checked="checked"'; ?> type="checkbox" />
    &nbsp;
    <?php _e('Single post pages', 'adseasy'); ?>
  </label>
  <br />
  <label for="<?php echo $this->get_field_id('date'); ?>">
    <input id="<?php echo $this->get_field_id('date'); ?>" name="<?php echo $this->get_field_name('date'); ?>" <?php if(!empty($date)) echo 'checked="checked"'; ?> type="checkbox" />
    &nbsp;
    <?php _e('Archive pages', 'adseasy'); ?>
  </label>
  <br />
  <label for="<?php echo $this->get_field_id('tag'); ?>">
    <input id="<?php echo $this->get_field_id('tag'); ?>" name="<?php echo $this->get_field_name('tag'); ?>" <?php if(!empty($tag)) echo 'checked="checked"'; ?> type="checkbox" />
    &nbsp;
    <?php _e('Tag pages', 'adseasy'); ?>
  </label>
  <br />
  <label for="<?php echo $this->get_field_id('attachment'); ?>">
    <input id="<?php echo $this->get_field_id('attachment'); ?>" name="<?php echo $this->get_field_name('attachment'); ?>" <?php if(!empty($attachment)) echo 'checked="checked"'; ?> type="checkbox" />
    &nbsp;
    <?php _e('Attachments', 'adseasy'); ?>
  </label>
  <br />
  <label for="<?php echo $this->get_field_id('taxonomy'); ?>">
    <input id="<?php echo $this->get_field_id('taxonomy'); ?>" name="<?php echo $this->get_field_name('taxonomy'); ?>" <?php if(!empty($taxonomy)) echo 'checked="checked"'; ?> type="checkbox" />
    &nbsp;
    <?php _e('Custom Taxonomy pages (only available, if having a plugin)', 'adseasy'); ?>
  </label>
  <br />
  <label for="<?php echo $this->get_field_id('author'); ?>">
    <input id="<?php echo $this->get_field_id('author'); ?>" name="<?php echo $this->get_field_name('author'); ?>" <?php if(!empty($author)) echo 'checked="checked"'; ?> type="checkbox" />
    &nbsp;
    <?php _e('Author pages', 'adseasy'); ?>
  </label>
  <br />
  <label for="<?php echo $this->get_field_id('search'); ?>">
    <input id="<?php echo $this->get_field_id('search'); ?>" name="<?php echo $this->get_field_name('search'); ?>" <?php if(!empty($search)) echo 'checked="checked"'; ?> type="checkbox" />
    &nbsp;
    <?php _e('Search Results', 'adseasy'); ?>
  </label>
  <br />
  <label for="<?php echo $this->get_field_id('not_found'); ?>">
    <input id="<?php echo $this->get_field_id('not_found'); ?>" name="<?php echo $this->get_field_name('not_found'); ?>" <?php if(!empty($not_found)) echo 'checked="checked"'; ?> type="checkbox" />
    &nbsp;
    <?php _e('&#34;Not Found&#34;', 'adseasy'); ?>
  </label>
</p>
<p>
  <label for="checkall">
    <input id="checkall" name="checkall" type="checkbox" />
    &nbsp;
    <?php _e('Check all', 'adseasy'); ?>
  </label>
</p>    
</fieldset>
<p>
  <label for="<?php echo $this->get_field_id('logged_in'); ?>">
    <input id="<?php echo $this->get_field_id('logged_in'); ?>" name="<?php echo $this->get_field_name('logged_in'); ?>" <?php if(!empty($logged_in)) echo 'checked="checked"'; ?> type="checkbox" />
    &nbsp;
    <?php _e('Don&#39;t show the ad to logged in users.', 'adseasy'); ?>
  </label>
</p>
<p>
 <label for="<?php echo $this->get_field_id('adblock'); ?>">
 <?php _e('Just paste the code of your ad here.', 'adseasy'); ?>
 <textarea class="widefat" id="<?php echo $this->get_field_id('adblock'); ?>" name="<?php echo $this->get_field_name('adblock'); ?>"><?php echo $adblock; ?></textarea>
 </label>
</p>
<p>
 <label for="<?php echo $this->get_field_id('style'); ?>">
 <?php _e('Here you can finally style the widget. Simply type something like<br /><strong>border-left: 1px dashed;<br />border-color: #000000;</strong><br />to get just a dashed black line on the left. If you leave that section empty, your theme will style the widget.', 'adseasy'); ?>
 <textarea class="widefat" id="<?php echo $this->get_field_id('style'); ?>" name="<?php echo $this->get_field_name('style'); ?>"><?php echo $style; ?></textarea>
 </label>
</p>
<script type="text/javascript"><!--
jQuery(document).ready(function() {
	jQuery("#<?php echo $this->get_field_id('adblock'); ?>").autoResize();
	jQuery("#<?php echo $this->get_field_id('style'); ?>").autoResize();
});
--></script>
<?php 
	}

function update($new_instance, $old_instance) {
	 
	 $instance = $old_instance;
	 
	 $instance['title'] = strip_tags($new_instance['title']);
	 $instance['name'] = strip_tags($new_instance['name']);
	 $instance['adblock'] = trim($new_instance['adblock']);
	 $instance['style'] = strip_tags($new_instance['style']);
	 $instance['homepage'] = strip_tags($new_instance['homepage']);
	 $instance['frontpage'] = strip_tags($new_instance['frontpage']);
	 $instance['page'] = strip_tags($new_instance['page']);
	 $instance['category'] = strip_tags($new_instance['category']);
	 $instance['single'] = strip_tags($new_instance['single']);
	 $instance['date'] = strip_tags($new_instance['date']); 
	 $instance['tag'] = strip_tags($new_instance['tag']);
	 $instance['attachment'] = strip_tags($new_instance['attachment']);
	 $instance['taxonomy'] = strip_tags($new_instance['taxonomy']);
	 $instance['author'] = strip_tags($new_instance['author']);
	 $instance['search'] = strip_tags($new_instance['search']);
	 $instance['not_found'] = strip_tags($new_instance['not_found']);
	 $instance['logged_in'] = strip_tags($new_instance['logged_in']);
	 
	 return $instance;
}
 
function widget($args, $instance) {
	
// user logged in and do we show ads to them?

if (!$instance['logged_in'] || ($instance['logged_in'] && !is_user_logged_in())) {
	
// get the type of page, we're actually on

if (is_front_page()) $ae_pagetype='frontpage';
if (is_home()) $ae_pagetype='homepage';
if (is_page()) $ae_pagetype='page';
if (is_category()) $ae_pagetype='category';
if (is_single()) $ae_pagetype='single';
if (is_date()) $ae_pagetype='date';
if (is_tag()) $ae_pagetype='tag';
if (is_attachment()) $ae_pagetype='attachment';
if (is_tax()) $ae_pagetype='taxonomy';
if (is_author()) $ae_pagetype='author';
if (is_search()) $ae_pagetype='search';
if (is_404()) $ae_pagetype='not_found';

// display only, if said so in the settings of the widget

if ($instance[$ae_pagetype]) {
	
	// the widget is displayed
	
	extract( $args );
	
	$title = apply_filters('widget_title', $instance['name']);	
	
	
	if (empty($instance['style'])) {
		
		$ae_before_widget=$before_widget;
		$ae_after_widget=$after_widget;
		
	}
	
	else {
		
		$ae_style=str_replace(array("\r\n", "\n", "\r"), '', $instance['style']);
		
		$ae_before_widget='<div id="'.$widget_id.'" style="'.$ae_style.'">';
		$ae_after_widget='</div>';
		
	}
	
	echo $ae_before_widget;
	
	if ( $title ) {
		
		echo $before_title . $title . $after_title;
		
	}
 
/* This is the actual function of the plugin, it fills the sidebar with the customized excerpts */
		
	echo $instance['adblock'];
	
	echo $ae_after_widget;

}}}

}

add_action('widgets_init', create_function('', 'return register_widget("Ads_Easy_Widget");'));


// import laguage files

load_plugin_textdomain('adseasy', false , basename(dirname(__FILE__)).'/languages');


/**
 *
 * init
 *
 */
add_action('admin_init', 'ads_easy_init');

function ads_easy_init() {
	
	register_setting( 'ae_options', 'ae_options', 'ae_validate' );
	
	add_settings_section('ads_easy_google', __('Use the Google AdSense Tags', 'adseasy'), 'ae_display_use_google', 'ae_use_adsense');
	
	add_settings_field('use_google_tags', 'Tags:', 'ae_display_tags', 'ae_use_adsense', 'ads_easy_google', array(' '.__('Check to use the Google AdSense Tags', 'adseasy')));
	
	add_settings_section('ads_easy_settings', __('What to wrap in the tags', 'adseasy'), 'ae_display_choices', 'ae_check_fields');
	
	add_settings_field('ae_header', 'Header:', 'ae_display_header', 'ae_check_fields', 'ads_easy_settings', array(' '.__('Check to include the header', 'adseasy')));
	add_settings_field('ae_loop', 'Loop:', 'ae_display_loop', 'ae_check_fields', 'ads_easy_settings', array(' '.__('Check to include the loop', 'adseasy')));
	add_settings_field('ae_sidebar', 'Sidebar(s):', 'ae_display_sidebar', 'ae_check_fields', 'ads_easy_settings', array(' '.__('Check to include the sidebar(s)', 'adseasy')));
	add_settings_field('ae_footer', 'Footer:', 'ae_display_footer', 'ae_check_fields', 'ads_easy_settings', array(' '.__('Check to include the footer', 'adseasy')));

}

function ae_display_use_google() {
	
	echo '<p>'.__('To activate the use of the tags, check the box. The other boxes are there for the specific parts of the code.', 'adseasy').'</p>';

}

function ae_display_choices() {

	echo '<p>'.__('Unchecked means, that the ignore tag is placed instead of the start tag. E.g. if you have ads from someone else than Google in the header, it might make sense to ignore it while if you have widgets in the footer, you definitely should include those.', 'adseasy').'</p>';
	echo '<p>'.__('There will be a button in the editor to mark sections of your text, that you want to have ignored by Google Adsense.', 'adseasy').'</p>';

}

function ae_display_tags($lable) {
	
	$ae_options = get_option('ae_options');
	
	echo "<input id='use_google_tags' name='ae_options[use_google_tags]' type='checkbox' value='1' ". checked( 1, $ae_options['use_google_tags'], false ) ." /><label for='use_google_tags'>" . $lable[0] . "</label>";
	
}

function ae_display_header($lable) {
	
	$ae_options = get_option('ae_options');
	
	echo "<input id='ae_header' name='ae_options[ae_header]' type='checkbox' value='1' ". checked( 1, $ae_options['ae_header'], false ) ." /><label for='ae_header'>" . $lable[0] . "</label>";
	
}

function ae_display_loop($lable) {
	
	$ae_options = get_option('ae_options');
	
	echo "<input id='ae_loop' name='ae_options[ae_loop]' type='checkbox' value='1' ". checked( 1, $ae_options['ae_loop'], false ) ." /><label for='ae_loop'>" . $lable[0] . "</label>";

}

function ae_display_sidebar($lable) {
	
	$ae_options = get_option('ae_options');
	
	echo "<input id='ae_sidebar' name='ae_options[ae_sidebar]' type='checkbox' value='1' ". checked( 1, $ae_options['ae_sidebar'], false ) ." /><label for='ae_sidebar'>" . $lable[0] . "</label>";

}

function ae_display_footer($lable) {
	
	$ae_options = get_option('ae_options');
	
	echo "<input id='ae_footer' name='ae_options[ae_footer]' type='checkbox' value='1' ". checked( 1, $ae_options['ae_footer'], false ) ." /><label for='ae_footer'>" . $lable[0] . "</label>";
	
}

// Adding the options

register_activation_hook(  __FILE__, 'ae_set_option' );

function ae_set_option() {
	
	add_option('ae_options', $ae_options);
	
}

// Deleting the options

register_deactivation_hook(  __FILE__, 'ae_unset_option' );

function ae_unset_option() {
	
	delete_option('ae_options');
	
}

// Installing options page

add_action('admin_menu', 'ae_admin_menu');

function ae_admin_menu() {
	
	add_plugins_page('Ads Easy', 'Ads Easy', 'administrator', 'ads-easy-settings', 'ae_options_page');
	
}

// Calling the options page

function ae_options_page() {
	
	?>
    
    <div>
    <h2>Ads Easy</h2>
    <?php settings_errors(); ?>
    
	<?php _e('Do you use Google Adsense in the widget?', 'adseasy'); ?>
    
    <form action="options.php" method="post">
	
	<?php
    
	settings_fields('ae_options');
	do_settings_sections('ae_use_adsense');
	do_settings_sections('ae_check_fields');
	
	?>
    
    <input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
    </form></div>
	
	<?php
}

function ae_validate($input) {
	
	$newinput['use_google_tags'] = trim($input['use_google_tags']);
	$newinput['ae_header'] = trim($input['ae_header']);
	$newinput['ae_loop'] = trim($input['ae_loop']);
	$newinput['ae_sidebar'] = trim($input['ae_sidebar']);
	$newinput['ae_footer'] = trim($input['ae_footer']);

	return $newinput;

}

?>