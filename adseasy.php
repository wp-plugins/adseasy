<?php
/*
Plugin Name: Ads Easy
Plugin URI: http://wasistlos.waldemarstoffel.com/plugins-fur-wordpress/ads-easy
Description: If you don't want to have Ads in your posts and you don't need other stats than those you get from wordpress and your adservers, this is the most easy solution. Place the code you get to the widget, style the widget and define, on what pages it shows up and to what kind of visitors. 
Version: 2.9.1
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

defined('ABSPATH') OR exit;

define( 'AE_PATH', plugin_dir_path(__FILE__) );
define( 'AE_BASE', plugin_basename(__FILE__) );

# loading the framework
if (!class_exists('A5_FormField')) require_once AE_PATH.'class-lib/A5_FormFieldClass.php';
if (!class_exists('A5_OptionPage')) require_once AE_PATH.'class-lib/A5_OptionPageClass.php';
if (!class_exists('A5_DynamicFiles')) require_once AE_PATH.'class-lib/A5_DynamicFileClass.php';

#loading plugin specific classes
if (!class_exists('AE_Admin')) require_once AE_PATH.'class-lib/AE_AdminClass.php';
if (!class_exists('AE_DynamicCSS')) require_once AE_PATH.'class-lib/AE_DynamicCSSClass.php';
if (!class_exists('Ads_Easy_Widget')) require_once AE_PATH.'class-lib/AE_WidgetClass.php';

class AdsEasy {
	
	const language_file = 'adseasy';
	
	private static $options;
	
	function __construct() {
		
		self::$options = get_option('ae_options');
		
		// import laguage files
	
		load_plugin_textdomain(self::language_file, false , basename(dirname(__FILE__)).'/languages');
		
		register_activation_hook(__FILE__, array(&$this, '_install'));
		register_deactivation_hook(__FILE__, array(&$this, '_uninstall'));
		
		add_filter('plugin_row_meta', array(&$this, 'register_links'), 10, 2);
		add_filter('plugin_action_links', array(&$this, 'register_action_links'), 10, 2);
		
		add_action('admin_enqueue_scripts', array(&$this, 'enqueue_scripts'));
		
		/**
		 *
		 * Attaching stylesheet, if neccessary
		 *
		 */
		
		if (!empty(self::$options['ae_css'])) $AE_DynamicCSS = new AE_DynamicCSS;
				
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
		
		$AE_Admin = new AE_Admin;
	
	}
	
	/* attach JavaScript file for textarea resizing */
	
	function enqueue_scripts($hook) {
		
		if ($hook != 'widgets.php' && $hook != 'plugins_page_ads-easy-settings') return;
		
		wp_register_script('ta-expander-script', plugins_url('ta-expander.js', __FILE__), array('jquery'), '3.0', true);
		wp_enqueue_script('ta-expander-script');
	
	}
	
	//Additional links on the plugin page
	
	function register_links($links, $file) {
		
		if ($file == AE_BASE) :
			
			$links[] = '<a href="http://wordpress.org/extend/plugins/adseasy/faq/" target="_blank">'.__('FAQ', self::language_file).'</a>';
			$links[] = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=VRMSV3NXQDXSA" target="_blank">'.__('Donate', self::language_file).'</a>';
		
		endif;
		
		return $links;
	
	}
	
	function register_action_links( $links, $file ) {
		
		if ($file == AE_BASE) array_unshift($links, '<a href="'.admin_url( 'plugins.php?page=ads-easy-settings' ).'">'.__('Settings', self::language_file).'</a>');
	
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
	
	// Adding the options
	
	static function _install() {
		
		$options = array(
			'ae_time' => 5,
			'inline' => false
		);
		
		add_option('ae_options', $options);
		
	}
	
	// Deleting the options
	
	static function _uninstall() {
		
		delete_option('ae_options');
		
	}

} // end of class

$AdsEasy = new AdsEasy;

?>