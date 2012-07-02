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

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) die('Sorry, you don&#39;t have direct access to this page.');

define( 'AE_PATH', plugin_dir_path(__FILE__) );

if (!class_exists('Ads_Easy_Widget')) require_once AE_PATH.'class-lib/AE_WidgetClass.php';
if (!class_exists('A5_WidgetControlClass')) require_once AE_PATH.'class-lib/A5_WidgetControlClass.php';

class AdsEasy {
	
	static $language_file = 'adseasy', $ba_tag;
	
	function AdsEasy() {
		
		// import laguage files
	
		load_plugin_textdomain(self::$language_file, false , basename(dirname(__FILE__)).'/languages');
		
		register_activation_hook(  __FILE__, array($this, 'ae_set_option') );
		register_deactivation_hook(  __FILE__, array($this, 'ae_unset_option') );
		add_action('admin_enqueue_scripts', array($this, 'ae_js_sheet'));
		add_filter('plugin_row_meta', array($this, 'ae_register_links'),10,2);
		add_filter( 'plugin_action_links', array($this, 'ae_plugin_action_links'), 10, 2 );
		add_action('admin_init', array($this, 'ads_easy_init'));
		add_action('admin_menu', array($this, 'ae_admin_menu'));
	
		/**
		 *
		 * Getting the Adsense Tags in the defined areas of the code and create hooks for other plugins
		 *
		 */
		if (!defined('AE_AD_TAGS')) :
				 
			$ae_options = get_option('ae_options');
			
			$ae_google = (!empty($ae_options)) ? $ae_options['use_google_tags'] : false;
			
			define('AE_AD_TAGS', $ae_google);
			
		endif;
		
		if (AE_AD_TAGS == 1) :
		
			add_action( 'wp_head', array($this, 'ae_header'), 1000);
			add_action( 'loop_start', array($this, 'ae_loop_start'));
			add_action( 'get_sidebar', array($this, 'ae_sidebar'));
			add_action( 'dynamic_sidebar', array($this, 'ae_sidebar'));
			add_action( 'wp_footer', array($this, 'ae_footer'));
			add_action( 'wp_footer', array($this, 'ae_end_tag'), 1000);
			
			// hooks for other plugins
			
			add_action( 'google_start_tag', array($this, 'ae_start_tag'));
			add_action( 'google_ignore_tag', array($this, 'ae_ignore_tag'));
			add_action( 'google_end_tag', array($this, 'ae_end_tag'));
			
			// adding short code 
			
			add_shortcode( 'ae_ignore_tag', array($this, 'ae_wrap_ignore'));
			
			// get the tinymce plugin
			if (!class_exists('A5_AddMceButton')) require_once AE_PATH.'tinymce/A5_MCEButtonClass.php';
			
			$tinymce_button = new A5_AddMceButton ('adseasy', 'AdsEasy', 'mce_buttons');
			
		endif;
	
	}
	
	/* attach JavaScript file for textarea resizing */
	
	function ae_js_sheet($hook) {
		
		if ($hook != 'widgets.php') return;
		
		wp_register_script('ta-expander-script', plugins_url('ta-expander.js', __FILE__), array('jquery'), '2.0', true);
		wp_enqueue_script('ta-expander-script');
	
	}
	
	//Additional links on the plugin page
	
	function ae_register_links($links, $file) {
		
		$base = plugin_basename(__FILE__);
		
		if ($file == $base) :
			
			$links[] = '<a href="http://wordpress.org/extend/plugins/adseasy/faq/" target="_blank">'.__('FAQ', self::$language_file).'</a>';
			$links[] = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=VRMSV3NXQDXSA" target="_blank">'.__('Donate', self::$language_file).'</a>';
		
		endif;
		
		return $links;
	
	}
	
	function ae_plugin_action_links( $links, $file ) {
		
		$base = plugin_basename(__FILE__);
		
		if ($file == $base) array_unshift($links, '<a href="'.admin_url( 'plugins.php?page=ads-easy-settings' ).'">'.__('Settings', self::$language_file).'</a>');
	
		return $links;
	
	}
	
	/**
	 *
	 * Getting the Adsense Tags in the defined areas of the code and create hooks for other plugins
	 *
	 */
	
	function ae_header() {
		
		$ae_options = get_option('ae_options');
		
		echo "<!-- Google AdSense Tags powered by Waldemar Stoffel's AdEasy http://wasistlos.waldemarstoffel.com/plugins-fur-wordpress/ads-easy -->\r\n";
		
		if ($ae_options['ae_header'] == '1') do_action('google_start_tag');
		
		else do_action('google_ignore_tag');
		
	}
	
	function ae_loop_start() {
		
		$ae_options = get_option('ae_options');
		
		if ($ae_options['ae_loop'] == '1' && self::$ba_tag == 'ignore') : 
		
			do_action('google_end_tag');
			
			do_action('google_start_tag');
			
		endif;
		
		if ($ae_options['ae_loop'] == false && self::$ba_tag == 'start') : 
		
			do_action('google_end_tag');
			
			do_action('google_ignore_tag');
			
		endif;
		
	}
	
	function ae_sidebar() {
		
		$ae_options = get_option('ae_options');
		
		if ($ae_options['ae_sidebar'] == '1' && self::$ba_tag == 'ignore') : 
		
			do_action('google_end_tag');
			
			do_action('google_start_tag');
			
		endif;
		
		if ($ae_options['ae_sidebar'] == false && self::$ba_tag == 'start') : 
		
			do_action('google_end_tag');
			
			do_action('google_ignore_tag');
			
		endif;
		
	}
	
	function ae_footer() {
		
		$ae_options = get_option('ae_options');
		
		if ($ae_options['ae_footer'] == '1' && self::$ba_tag == 'ignore') : 
		
			do_action('google_end_tag');
			
			do_action('google_start_tag');
			
		endif;
		
		if ($ae_options['ae_footer'] == false && self::$ba_tag == 'start') : 
		
			do_action('google_end_tag');
			
			do_action('google_ignore_tag');
			
		endif;
		
	}
	
	function ae_start_tag() {
		
		$eol = "\r\n";
	
		echo '<!-- google_ad_section_start -->'.$eol;
		
		self::$ba_tag = 'start';
		
	}
	
	function ae_ignore_tag() {
		
		$eol = "\r\n";
	
		echo '<!-- google_ad_section_start(weight=ignore) -->'.$eol;
		
		self::$ba_tag = 'ignore';
		
	}
	
	function ae_end_tag() {
		
		$eol = "\r\n";
	
		echo $eol.'<!-- google_ad_section_end -->'.$eol;
		
		self::$ba_tag = 'end';
		
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
	 * init
	 *
	 */
	function ads_easy_init() {
		
		register_setting( 'ae_options', 'ae_options', array($this, 'ae_validate') );
		
		add_settings_section('ads_easy_google', __('Use the Google AdSense Tags', self::$language_file), array($this, 'ae_display_use_google'), 'ae_use_adsense');
		
		add_settings_field('use_google_tags', 'Tags:', array($this, 'ae_display_tags'), 'ae_use_adsense', 'ads_easy_google', array(' '.__('Check to use the Google AdSense Tags', self::$language_file)));
		
		add_settings_section('ads_easy_settings', __('What to wrap in the tags', self::$language_file), array($this, 'ae_display_choices'), 'ae_check_fields');
		
		add_settings_field('ae_header', 'Header:', array($this, 'ae_display_header'), 'ae_check_fields', 'ads_easy_settings', array(' '.__('Check to include the header', self::$language_file)));
		add_settings_field('ae_loop', 'Loop:', array($this, 'ae_display_loop'), 'ae_check_fields', 'ads_easy_settings', array(' '.__('Check to include the loop', self::$language_file)));
		add_settings_field('ae_sidebar', 'Sidebar(s):', array($this, 'ae_display_sidebar'), 'ae_check_fields', 'ads_easy_settings', array(' '.__('Check to include the sidebar(s)', self::$language_file)));
		add_settings_field('ae_footer', 'Footer:', array($this, 'ae_display_footer'), 'ae_check_fields', 'ads_easy_settings', array(' '.__('Check to include the footer', self::$language_file)));
	
	}
	
	function ae_display_use_google() {
		
		echo '<p>'.__('To activate the use of the tags, check the box. The other boxes are there for the specific parts of the code.', self::$language_file).'</p>';
	
	}
	
	function ae_display_choices() {
	
		echo '<p>'.__('Unchecked means, that the ignore tag is placed instead of the start tag. E.g. if you have ads from someone else than Google in the header, it might make sense to ignore it while if you have widgets in the footer, you definitely should include those.', self::$language_file).'</p>';
		echo '<p>'.__('There will be a button in the editor to mark sections of your text, that you want to have ignored by Google Adsense.', self::$language_file).'</p>';
	
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
	
	function ae_set_option() {
		
		add_option('ae_options', $ae_options);
		
	}
	
	// Deleting the options
	
	function ae_unset_option() {
		
		delete_option('ae_options');
		
	}
	
	// Installing options page
	
	function ae_admin_menu() {
		
		add_plugins_page('Ads Easy', 'Ads Easy', 'administrator', 'ads-easy-settings', array($this, 'ae_options_page'));
		
	}
	
	// Calling the options page
	
	function ae_options_page() {
		
		?>
		
		<div>
		<h2>Ads Easy</h2>
		<?php settings_errors(); ?>
		
		<?php _e('Do you use Google Adsense in the widget?', self::$language_file); ?>
		
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

} // end of class

$adeasy = new AdsEasy;

?>