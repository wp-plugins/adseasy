<?php
/*
Plugin Name: Ads Easy
Plugin URI: http://wasistlos.waldemarstoffel.com/plugins-fur-wordpress/ads-easy
Description: If you don't want to have Ads in your posts and you don't need other stats than hose you get from wordpress and your adservers, his is the most easy sollution. Place the code you get to the widget, style the widget and define, on what pages it shows up. 
Version: 1.0
Author: Waldemar Stoffel
Author URI: http://www.waldemarstoffel.com
License: GPL3
*/

/*  Copyright 2011  Waldemar Stoffel  (email : w-stoffel@gmx.net)

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

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die("Sorry, you don't have direct access to this page."); }

/* attach JavaScript file for textarea reszing */

$ae_path = WP_CONTENT_URL.'/plugins/'.plugin_basename(dirname(__FILE__)).'/';

function ae_js_sheet() {
   global $ae_path;
   wp_enqueue_script('ta-resize-script', $ae_path.'ta-expander.js', false, false, true);
}

add_action('admin_print_scripts-widgets.php', 'ae_js_sheet');
add_action('admin_footer-widgets.php', 'ae_write_script');

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

// extending the widget class

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
	
	if (empty($style)) {
		
		$style_height=25;
	
	}
	
	else {
		
		$ae_elements=str_replace(array("\r\n", "\n", "\r"), '|', $style);
		$style_height=count(explode('|', $ae_elements))*21;
		
	}
	
	if (empty($adblock)) {
		
		$adblock_height=25;
	
	}
	
	else {
		
		$ae_elements=str_replace(array("\r\n", "\n", "\r"), '|', $adblock);
		$adblock_height=count(explode('|', $ae_elements))*21;
		
	}	
	
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
<p>
  <label for="<?php echo $this->get_field_id('homepage'); ?>">
    <input id="<?php echo $this->get_field_id('homepage'); ?>" name="<?php echo $this->get_field_name('homepage'); ?>" <?php if(!empty($homepage)) {echo "checked=\"checked\""; } ?> type="checkbox" />
    &nbsp;
    <?php _e('Homepage', 'adseasy'); ?>
  </label>
  <br />
  <label for="<?php echo $this->get_field_id('frontpage'); ?>">
    <input id="<?php echo $this->get_field_id('frontpage'); ?>" name="<?php echo $this->get_field_name('frontpage'); ?>" <?php if(!empty($frontpage)) {echo "checked=\"checked\""; } ?> type="checkbox" />
    &nbsp;
    <?php _e('Frontpage (e.g. a static page as homepage)', 'adseasy'); ?>
  </label>
  <br />
  <label for="<?php echo $this->get_field_id('page'); ?>">
    <input id="<?php echo $this->get_field_id('page'); ?>" name="<?php echo $this->get_field_name('page'); ?>" <?php if(!empty($page)) {echo "checked=\"checked\""; } ?> type="checkbox" />
    &nbsp;
    <?php _e('&#34;Page&#34; pages', 'adseasy'); ?>
  </label>
  <br />
  <label for="<?php echo $this->get_field_id('category'); ?>">
    <input id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('category'); ?>" <?php if(!empty($category)) {echo "checked=\"checked\""; } ?> type="checkbox" />
    &nbsp;
    <?php _e('Category pages', 'adseasy'); ?>
  </label>
  <br />
  <label for="<?php echo $this->get_field_id('single'); ?>">
    <input id="<?php echo $this->get_field_id('single'); ?>" name="<?php echo $this->get_field_name('single'); ?>" <?php if(!empty($single)) {echo "checked=\"checked\""; } ?> type="checkbox" />
    &nbsp;
    <?php _e('Single post pages', 'adseasy'); ?>
  </label>
  <br />
  <label for="<?php echo $this->get_field_id('date'); ?>">
    <input id="<?php echo $this->get_field_id('date'); ?>" name="<?php echo $this->get_field_name('date'); ?>" <?php if(!empty($date)) {echo "checked=\"checked\""; } ?> type="checkbox" />
    &nbsp;
    <?php _e('Archive pages', 'adseasy'); ?>
  </label>
  <br />
  <label for="<?php echo $this->get_field_id('tag'); ?>">
    <input id="<?php echo $this->get_field_id('tag'); ?>" name="<?php echo $this->get_field_name('tag'); ?>" <?php if(!empty($tag)) {echo "checked=\"checked\""; } ?> type="checkbox" />
    &nbsp;
    <?php _e('Tag pages', 'adseasy'); ?>
  </label>
  <br />
  <label for="<?php echo $this->get_field_id('attachment'); ?>">
    <input id="<?php echo $this->get_field_id('attachment'); ?>" name="<?php echo $this->get_field_name('attachment'); ?>" <?php if(!empty($attachment)) {echo "checked=\"checked\""; } ?> type="checkbox" />
    &nbsp;
    <?php _e('Attachments', 'adseasy'); ?>
  </label>
  <br />
  <label for="<?php echo $this->get_field_id('taxonomy'); ?>">
    <input id="<?php echo $this->get_field_id('taxonomy'); ?>" name="<?php echo $this->get_field_name('taxonomy'); ?>" <?php if(!empty($taxonomy)) {echo "checked=\"checked\""; } ?> type="checkbox" />
    &nbsp;
    <?php _e('Custom Taxonomy pages (only available, if having a plugin)', 'adseasy'); ?>
  </label>
  <br />
  <label for="<?php echo $this->get_field_id('author'); ?>">
    <input id="<?php echo $this->get_field_id('author'); ?>" name="<?php echo $this->get_field_name('author'); ?>" <?php if(!empty($author)) {echo "checked=\"checked\""; } ?> type="checkbox" />
    &nbsp;
    <?php _e('Author pages', 'adseasy'); ?>
  </label>
  <br />
  <label for="<?php echo $this->get_field_id('search'); ?>">
    <input id="<?php echo $this->get_field_id('search'); ?>" name="<?php echo $this->get_field_name('search'); ?>" <?php if(!empty($search)) {echo "checked=\"checked\""; } ?> type="checkbox" />
    &nbsp;
    <?php _e('Search Results', 'adseasy'); ?>
  </label>
  <br />
  <label for="<?php echo $this->get_field_id('not_found'); ?>">
    <input id="<?php echo $this->get_field_id('not_found'); ?>" name="<?php echo $this->get_field_name('not_found'); ?>" <?php if(!empty($not_found)) {echo "checked=\"checked\""; } ?> type="checkbox" />
    &nbsp;
    <?php _e('&#34;Not Found&#34;', 'adseasy'); ?>
  </label>
  <br />
</p>
<p>
  <label for="<?php echo $this->get_field_id('logged_in'); ?>">
    <input id="<?php echo $this->get_field_id('logged_in'); ?>" name="<?php echo $this->get_field_name('logged_in'); ?>" <?php if(!empty($logged_in)) {echo "checked=\"checked\""; } ?> type="checkbox" />
    &nbsp;
    <?php _e('Don&#39;t show the ad to logged in users.', 'adseasy'); ?>
  </label>
</p>
<p>
 <label for="<?php echo $this->get_field_id('adblock'); ?>">
 <?php _e('Just paste the code of your ad here.', 'adseasy'); ?>
 <textarea class="widefat expand<?php echo $adblock_height; ?>-1000" id="<?php echo $this->get_field_id('adblock'); ?>" name="<?php echo $this->get_field_name('adblock'); ?>"><?php echo $adblock; ?></textarea>
 </label>
</p>
<p>
 <label for="<?php echo $this->get_field_id('style'); ?>">
 <?php _e('Here you can finally style the widget. Simply type something like<br /><strong>border-left: 1px dashed;<br />border-color: #000000;</strong><br />to get just a dashed black line on the left. If you leave that section empty, your theme will style the widget.', 'adseasy'); ?>
 <textarea class="widefat expand<?php echo $style_height; ?>-1000" id="<?php echo $this->get_field_id('style'); ?>" name="<?php echo $this->get_field_name('style'); ?>"><?php echo $style; ?></textarea>
 </label>
</p>
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

if (is_front_page()) { $ae_pagetype='frontpage'; }
if (is_home()) { $ae_pagetype='homepage'; }
if (is_page()) { $ae_pagetype='page'; }
if (is_category()) { $ae_pagetype='category'; }
if (is_single()) { $ae_pagetype='single'; }
if (is_date()) { $ae_pagetype='date'; }
if (is_tag()) { $ae_pagetype='tag'; }
if (is_attachment()) { $ae_pagetype='attachment'; }
if (is_tax()) { $ae_pagetype='taxonomy'; }
if (is_author()) { $ae_pagetype='author'; }
if (is_search()) { $ae_pagetype='search'; }
if (is_404()) { $ae_pagetype='not_found'; }

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
		
		$ae_before_widget="<div id=\"".$widget_id."\" style=\"".$ae_style."\">";
		$ae_after_widget="</div>";
		
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

?>