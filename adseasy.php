<?php
/*
Plugin Name: Ads Easy
Plugin URI: http://wasistlos.waldemarstoffel.com/plugins-fur-wordpress/ads-easy
Description: If you don't want to have Ads in your posts and you don't need other stats than those you get from wordpress and your adservers, this is the most easy solution. Place the code you get to the widget, style the widget and define, on what pages it shows up and to what kind of visitors. 
Version: 2.8
Author: Waldemar Stoffel
Author URI: http://www.atelier-fuenf.de
License: GPL3
Text Domain: adseasy 
*/

/*  Copyright 2011 - 2014 Waldemar Stoffel  (email : stoffel@atelier-fuenf.de)

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

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) die('Sorry, you don\'t have direct access to this page.');

define( 'AE_PATH', plugin_dir_path(__FILE__) );

if (!class_exists('Ads_Easy_Widget')) require_once AE_PATH.'class-lib/AE_WidgetClass.php';
if (!class_exists('A5_FormField')) require_once AE_PATH.'class-lib/A5_FormFieldClass.php';
if (!class_exists('A5_OptionPage')) require_once AE_PATH.'class-lib/A5_OptionPageClass.php';

class AdsEasy {
	
	const language_file = 'adseasy';
	
	private static $options;
	
	function __construct() {
		
		self::$options = get_option('ae_options');
		
		// import laguage files
	
		load_plugin_textdomain(self::language_file, false , basename(dirname(__FILE__)).'/languages');
		
		register_activation_hook(__FILE__, array($this, 'install'));
		register_deactivation_hook(__FILE__, array($this, 'uninstall'));
		
		add_filter('plugin_row_meta', array($this, 'register_links'), 10, 2);
		add_filter('plugin_action_links', array($this, 'register_action_links'), 10, 2);
		
		add_action('admin_enqueue_scripts', array($this, 'ae_js_sheet'));
		add_action('admin_init', array($this, 'ads_easy_init'));
		add_action('admin_menu', array($this, 'ae_admin_menu'));
	
		/**
		 *
		 * Attaching stylesheet, if neccessary
		 *
		 */
		
		if (!empty(self::$options['ae_css'])) :
		
			if (!class_exists('A5_DynamicCSS')) :
	
				require_once AE_PATH.'class-lib/A5_DynamicCSSClass.php';
				
				$dynamic_css = new A5_DynamicCSS;
				
			endif;
				
			$eol = "\r\n";
			$tab = "\t";
			
			A5_DynamicCSS::$styles .= $eol.'/* CSS portion of Ads Easy */'.$eol.$eol;
			
			$style=str_replace('; ', ';'.$eol.$tab, str_replace(array("\r\n", "\n", "\r"), ' ', self::$options['ae_css']));
	
			A5_DynamicCSS::$styles.='div[id^="ads_easy_widget"].widget_ads_easy_widget {'.$eol.$tab.$style.$eol.'}'.$eol;
		
		endif;
		
		/**
		 *
		 * Getting the Adsense Tags and the new button to the editor
		 *
		 */
		
		if (isset(self::$options['use_google_tags'])) :
		
			// add the button to the editor and the shortcode to wp
		
			if (!class_exists('A5_AddMceButton')) require_once AE_PATH.'class-lib/A5_MCEButtonClass.php';
		
			add_action('wp_head', array($this, 'write_header_info'), 1000);
			
			add_shortcode('ae_ignore_tag', array($this, 'set_ignore_tags'));
			
			add_filter('loop_start', array($this, 'google_start'));
			add_filter('loop_end', array($this, 'google_end'));
			
			$tinymce_button = new A5_AddMceButton ('adseasy', 'AdsEasy', 'mce_buttons');
			
		endif;
	
	}
	
	/* attach JavaScript file for textarea resizing */
	
	function ae_js_sheet($hook) {
		
		if ($hook != 'widgets.php' && $hook != 'plugins_page_ads-easy-settings') return;
		
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
	
	function register_action_links( $links, $file ) {
		
		$base = plugin_basename(__FILE__);
		
		if ($file == $base) array_unshift($links, '<a href="'.admin_url( 'plugins.php?page=ads-easy-settings' ).'">'.__('Settings', self::language_file).'</a>');
	
		return $links;
	
	}
	
	/**
	 *
	 * Getting the Adsense Tags in the defined areas of the code and create hooks for other plugins
	 *
	 */
	
	function write_header_info() {
		
		echo "<!-- Google AdSense Tags powered by Waldemar Stoffel's AdEasy ".__('http://wasistlos.waldemarstoffel.com/plugins-fur-wordpress/ads-easy', self::language_file)." -->\r\n";
		
	}
	
	function google_start() {
		
		echo "<!-- google_ad_section_start -->\r\n";
		
	}
	
	function google_end() {
		
		echo "<!-- google_ad_section_end -->\r\n";
		
	}
	
	/**
	 *
	 * shortcode for the ignore tags
	 *
	 */
	function set_ignore_tags($atts, $content = null){
		
		$eol = "\r\n";
		
		return $eol.'<!-- google_ad_section_end -->'.$eol.'<!-- google_ad_section_start(weight=ignore) -->'.$eol.do_shortcode($content).$eol.'<!-- google_ad_section_end -->'.$eol.'<!-- google_ad_section_start -->'.$eol;
	}
	
	/**
	 *
	 * init
	 *
	 */
	function ads_easy_init() {
		
		register_setting( 'ae_options', 'ae_options', array($this, 'validation') );
		
		add_settings_section('ads_easy_google', __('Use the Google AdSense Tags', self::language_file), array($this, 'ae_display_use_google'), 'ae_use_adsense');
		
		add_settings_field('use_google_tags', 'Tags:', array($this, 'ae_display_tags'), 'ae_use_adsense', 'ads_easy_google', array(' '.__('Check to use the Google AdSense Tags', self::language_file)));
		
		add_settings_field('ae_engine_time', __('Search Engines:', self::language_file), array($this, 'ae_display_time'), 'ae_use_adsense', 'ads_easy_google', array(__('How long should the widget be displayed to visitors from search engines (in minutes)?', self::language_file).'<br/>'));
		
		add_settings_field('use_own_css', 'CSS:', array($this, 'ae_display_css'), 'ae_use_adsense', 'ads_easy_google', array(__('You can enter your own style for the widgets here. This will overwrite the styles of your theme.', self::language_file), __('If you leave this empty, you can still style every instance of the widget individually.', self::language_file)));
		
		add_settings_field('ae_resize', false, array($this, 'resize_field'), 'ae_use_adsense', 'ads_easy_google');
	
	}
	
	function ae_display_use_google() {
		
		echo '<p>'.__('To activate the use of the tags, check the box.', self::language_file).'</p>';
	
	}
	
	function ae_display_tags($labels) {
		
		a5_checkbox('use_google_tags', 'ae_options[use_google_tags]', @self::$options['use_google_tags'], $labels[0]);
		
	}
	
	function ae_display_time($labels) {
		
		a5_number_field('ae_time', 'ae_options[ae_time]', self::$options['ae_time'], $labels[0], array('step' => 1));
		
	}
	
	function ae_display_css($labels) {
		
		echo $labels[0].'</br>'.$labels[1].'</br>';
		
		a5_textarea('ae_css', 'ae_options[ae_css]', @self::$options['ae_css'], false, array('rows' => 10, 'cols' => 35));
		
	}
	
	function resize_field() {
		
		a5_resize_textarea(array('ae_css'));
		
	}
	
	// Adding the options
	
	static function install() {
		
		$options = array('ae_time' => 5);
		
		add_option('ae_options', $options);
		
	}
	
	// Deleting the options
	
	static function uninstall() {
		
		delete_option('ae_options');
		
	}
	
	// Installing options page
	
	function ae_admin_menu() {
		
		add_plugins_page('Ads Easy '.__('Settings', self::language_file), '<img alt="" src="'.plugins_url('adseasy/img/a5-icon-11.png').'"> Ads Easy', 'administrator', 'ads-easy-settings', array($this, 'ae_options_page'));
		
	}
	
	// Calling the options page
	
	function ae_options_page() {
		
		A5_OptionPage::open_page('Ads Easy', __('http://wasistlos.waldemarstoffel.com/plugins-fur-wordpress/ads-easy', self::language_file), 'adseasy', __('Plugin Support', self::language_file));
		
		settings_errors();
		
		_e('Do you use Google Adsense in the widget?', self::language_file); 
		
		A5_OptionPage::open_form('options.php');
		
		settings_fields('ae_options');
		do_settings_sections('ae_use_adsense');
		
		submit_button();
		
		A5_OptionPage::close_page();
		
	}
	
	function validation($input) {
		
		if (isset($newinput['use_google_tags'])) $newinput['use_google_tags'] = $input['use_google_tags'];
		$newinput['ae_time'] = trim($input['ae_time']);
		$newinput['ae_css'] = trim($input['ae_css']);
		
		if (!is_numeric($newinput['ae_time'])) :
		
			add_settings_error('ae_settings', 'wrong-time', __('Please give numeric value for the minutes.', self::language_file), 'error');
			
			unset($newinput['ae_time']);
			
		endif;
	
		return $newinput;
	
	}

} // end of class

$adseasy = new AdsEasy;

?>