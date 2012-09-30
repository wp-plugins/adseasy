<?php
/*
Plugin Name: Ads Easy
Plugin URI: http://wasistlos.waldemarstoffel.com/plugins-fur-wordpress/ads-easy
Description: If you don't want to have Ads in your posts and you don't need other stats than those you get from wordpress and your adservers, this is the most easy solution. Place the code you get to the widget, style the widget and define, on what pages it shows up. 
Version: 2.5
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
if (!class_exists('A5_OptionPage')) require_once AE_PATH.'class-lib/A5_OptionPageClass.php';
if (!function_exists('a5_option_page_version')) require_once AE_PATH.'includes/admin-pages.php';

class AdsEasy {
	
	const language_file = 'adseasy';
	
	static $ba_tag, $options;
	
	function AdsEasy() {
		
		self::$options = get_option('ae_options');
		
		// import laguage files
	
		load_plugin_textdomain(self::language_file, false , basename(dirname(__FILE__)).'/languages');
		
		register_activation_hook(__FILE__, array($this, 'set_options'));
		register_deactivation_hook(__FILE__, array($this, 'delete_options'));
		
		add_filter('plugin_row_meta', array($this, 'register_links'), 10, 2);
		add_filter('plugin_action_links', array($this, 'plugin_action_links'), 10, 2);
		
		add_action('admin_enqueue_scripts', array($this, 'ae_js_sheet'));
		add_action('admin_init', array($this, 'ads_easy_init'));
		add_action('admin_menu', array($this, 'ae_admin_menu'));
	
		/**
		 *
		 * Getting the Adsense Tags in the defined areas of the code and create hooks for other plugins
		 *
		 */
		if (!defined('AE_AD_TAGS')) :
				 
			$ae_google = (!empty(self::$options)) ? self::$options['use_google_tags'] : false;
			
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
			if (!class_exists('A5_AddMceButton')) require_once AE_PATH.'class-lib/A5_MCEButtonClass.php';
			
			$tinymce_button = new A5_AddMceButton ('adseasy', 'AdsEasy', 'mce_buttons');
			
		endif;
	
	}
	
	/* attach JavaScript file for textarea resizing */
	
	function ae_js_sheet($hook) {
		
		if ($hook != 'widgets.php') return;
		
		wp_register_script('ta-expander-script', plugins_url('ta-expander.js', __FILE__), array('jquery'), '3.0', true);
		wp_enqueue_script('ta-expander-script');
	
	}
	
	//Additional links on the plugin page
	
	function register_links($links, $file) {
		
		$base = plugin_basename(__FILE__);
		
		if ($file == $base) :
			
			$links[] = '<a href="http://wordpress.org/extend/plugins/adseasy/faq/" target="_blank">'.__('FAQ', self::language_file).'</a>';
			$links[] = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=VRMSV3NXQDXSA" target="_blank">'.__('Donate', self::language_file).'</a>';
		
		endif;
		
		return $links;
	
	}
	
	function plugin_action_links( $links, $file ) {
		
		$base = plugin_basename(__FILE__);
		
		if ($file == $base) array_unshift($links, '<a href="'.admin_url( 'plugins.php?page=ads-easy-settings' ).'">'.__('Settings', self::language_file).'</a>');
	
		return $links;
	
	}
	
	/**
	 *
	 * Getting the Adsense Tags in the defined areas of the code and create hooks for other plugins
	 *
	 */
	
	function ae_header() {
		
		self::$options = get_option('ae_options');
		
		echo "<!-- Google AdSense Tags powered by Waldemar Stoffel's AdEasy ".__('http://wasistlos.waldemarstoffel.com/plugins-fur-wordpress/ads-easy', self::language_file)." -->\r\n";
		
		if (self::$options['ae_header'] == '1') do_action('google_start_tag');
		
		else do_action('google_ignore_tag');
		
	}
	
	function ae_loop_start() {
		
		self::$options = get_option('ae_options');
		
		if (self::$options['ae_loop'] == '1' && self::$ba_tag == 'ignore') : 
		
			do_action('google_end_tag');
			
			do_action('google_start_tag');
			
		endif;
		
		if (self::$options['ae_loop'] == false && self::$ba_tag == 'start') : 
		
			do_action('google_end_tag');
			
			do_action('google_ignore_tag');
			
		endif;
		
	}
	
	function ae_sidebar() {
		
		self::$options = get_option('ae_options');
		
		if (self::$options['ae_sidebar'] == '1' && self::$ba_tag == 'ignore') : 
		
			do_action('google_end_tag');
			
			do_action('google_start_tag');
			
		endif;
		
		if (self::$options['ae_sidebar'] == false && self::$ba_tag == 'start') : 
		
			do_action('google_end_tag');
			
			do_action('google_ignore_tag');
			
		endif;
		
	}
	
	function ae_footer() {
		
		if (self::$options['ae_footer'] == '1' && self::$ba_tag == 'ignore') : 
		
			do_action('google_end_tag');
			
			do_action('google_start_tag');
			
		endif;
		
		if (self::$options['ae_footer'] == false && self::$ba_tag == 'start') : 
		
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
		
		add_settings_section('ads_easy_google', __('Use the Google AdSense Tags', self::language_file), array($this, 'ae_display_use_google'), 'ae_use_adsense');
		
		add_settings_field('use_google_tags', 'Tags:', array($this, 'ae_display_tags'), 'ae_use_adsense', 'ads_easy_google', array(' '.__('Check to use the Google AdSense Tags', self::language_file)));
		
		add_settings_section('ads_easy_settings', __('What to wrap in the tags', self::language_file), array($this, 'ae_display_choices'), 'ae_check_fields');
		
		add_settings_field('ae_header', 'Header:', array($this, 'ae_display_header'), 'ae_check_fields', 'ads_easy_settings', array(' '.__('Check to include the header', self::language_file)));
		add_settings_field('ae_loop', 'Loop:', array($this, 'ae_display_loop'), 'ae_check_fields', 'ads_easy_settings', array(' '.__('Check to include the loop', self::language_file)));
		add_settings_field('ae_sidebar', 'Sidebar(s):', array($this, 'ae_display_sidebar'), 'ae_check_fields', 'ads_easy_settings', array(' '.__('Check to include the sidebar(s)', self::language_file)));
		add_settings_field('ae_footer', 'Footer:', array($this, 'ae_display_footer'), 'ae_check_fields', 'ads_easy_settings', array(' '.__('Check to include the footer', self::language_file)));
		add_settings_field('ae_engine_time', __('Search Engines:', self::language_file), array($this, 'ae_display_time'), 'ae_check_fields', 'ads_easy_settings', array(__('How long should the widget be displayed to visitors from search engines (in minutes)?', self::language_file).'<br/>'));
	
	}
	
	function ae_display_use_google() {
		
		echo '<p>'.__('To activate the use of the tags, check the box. The other boxes are there for the specific parts of the code.', self::language_file).'</p>';
	
	}
	
	function ae_display_choices() {
	
		echo '<p>'.__('Unchecked means, that the ignore tag is placed instead of the start tag. E.g. if you have ads from someone else than Google in the header, it might make sense to ignore it while if you have widgets in the footer, you definitely should include those.', self::language_file).'</p>';
		echo '<p>'.__('There will be a button in the editor to mark sections of your text, that you want to have ignored by Google Adsense.', self::language_file).'</p>';
	
	}
	
	function ae_display_tags($labels) {
		
		a5_checkbox('use_google_tags', 'ae_options[use_google_tags]', self::$options['use_google_tags'], $labels[0], false, false, true, true);
		
	}
	
	function ae_display_header($labels) {
		
		a5_checkbox('ae_header', 'ae_options[ae_header]', self::$options['ae_header'], $labels[0], false, false, true, true);
		
	}
	
	function ae_display_loop($labels) {
		
		a5_checkbox('ae_loop', 'ae_options[ae_loop]', self::$options['ae_loop'], $labels[0], false, false, true, true);
	
	}
	
	function ae_display_sidebar($labels) {
		
		a5_checkbox('ae_sidebar', 'ae_options[ae_sidebar]', self::$options['ae_sidebar'], $labels[0], false, false, true, true);
		
	}
	
	function ae_display_footer($labels) {
		
		a5_checkbox('ae_footer', 'ae_options[ae_footer]', self::$options['ae_footer'], $labels[0], false, false, true, true);
		
	}
	
	function ae_display_time($labels) {
		
		a5_text_field('ae_time', 'ae_options[ae_time]', self::$options['ae_time'], $labels[0], false, false, false, true, true);
		
	}
	
	// Adding the options
	
	function set_options() {
		
		$options = array('ae_time' => 5);
		
		add_option('ae_options', $options);
		
	}
	
	// Deleting the options
	
	function delete_options() {
		
		delete_option('ae_options');
		
	}
	
	// Installing options page
	
	function ae_admin_menu() {
		
		add_plugins_page('Ads Easy '.__('Settings', self::language_file), '<img alt="" src="'.plugins_url('adseasy/img/a5-icon-11.png').'"> Ads Easy', 'administrator', 'ads-easy-settings', array($this, 'ae_options_page'));
		
	}
	
	// Calling the options page
	
	function ae_options_page() {
		
		?>
		
		<div class="wrap">
        <a href="<?php _e('http://wasistlos.waldemarstoffel.com/plugins-fur-wordpress/ads-easy', self::language_file); ?>"><div id="a5-logo" class="icon32" style="background: url('<?php echo plugins_url('adseasy/img/a5-icon-34.png');?>');"></div></a>
		<h2>Ads Easy</h2>
		<?php settings_errors(); ?>
		
		<?php _e('Do you use Google Adsense in the widget?', self::language_file); ?>
		
		<form action="options.php" method="post">
		
		<?php
		
		settings_fields('ae_options');
		do_settings_sections('ae_use_adsense');
		do_settings_sections('ae_check_fields');
		
		submit_button();
		?>
		</form></div>
		
		<?php
	}
	
	function ae_validate($input) {
		
		$newinput['use_google_tags'] = trim($input['use_google_tags']);
		$newinput['ae_header'] = trim($input['ae_header']);
		$newinput['ae_loop'] = trim($input['ae_loop']);
		$newinput['ae_sidebar'] = trim($input['ae_sidebar']);
		$newinput['ae_footer'] = trim($input['ae_footer']);
		$newinput['ae_time'] = trim($input['ae_time']);
		
		if (!is_numeric($newinput['ae_time'])) :
		
			add_settings_error('ae_settings', 'wrong-time', __('Please give numeric value for the minutes.', self::language_file), 'error');
			
			unset($newinput['ae_time']);
			
		endif;
	
		return $newinput;
	
	}

} // end of class

$adseasy = new AdsEasy;

?>