<?php

/**
 *
 * Class Recent Post Widget Admin
 *
 * @ A5 Recent Post Widget
 *
 * building admin page
 *
 */
class AE_Admin extends A5_OptionPage {
	
	const language_file = 'adseasy';
	
	static $options;
	
	function __construct() {
	
		add_action('admin_init', array(&$this, 'initialize_settings'));
		add_action('admin_menu', array(&$this, 'add_admin_menu'));
		if (defined('WP_DEBUG') && WP_DEBUG == true) add_action('admin_enqueue_scripts', array(&$this, 'enqueue_scripts'));
		
		self::$options = get_option('ae_options');
		
	}
	
	/**
	 *
	 * Make debug info collapsable
	 *
	 */
	function enqueue_scripts($hook){
		
		if ('plugins_page_ads-easy-settings' != $hook) return;
		
		wp_enqueue_script('dashboard');
		
	}
	
	/**
	 *
	 * Add options-page for single site
	 *
	 */
	function add_admin_menu() {
		
		add_plugins_page('Ads Easy '.__('Settings', self::language_file), '<img alt="" src="'.plugins_url('adseasy/img/a5-icon-11.png').'"> Ads Easy', 'administrator', 'ads-easy-settings', array($this, 'build_options_page'));
		
	}
	
	/**
	 *
	 * Actually build the option pages
	 *
	 */
	function build_options_page() {
		
		parent::open_page('Ads Easy', __('http://wasistlos.waldemarstoffel.com/plugins-fur-wordpress/ads-easy', self::language_file), 'adseasy', __('Plugin Support', self::language_file));
		
		settings_errors();
		
		_e('Do you use Google Adsense in the widget?', self::language_file); 
		
		parent::open_form('options.php');
		
		settings_fields('ae_options');
		do_settings_sections('ae_use_adsense');
		
		submit_button();
		
		if (WP_DEBUG === true) :
			
			echo '<div id="poststuff">';
			
			parent::open_draggable(__('Debug Info', self::language_file), 'debug-info');
			
			echo '<pre>';
			
			var_dump(self::$options);
			
			echo '</pre>';
			
			parent::close_draggable();
			
			echo '</div>';
		
		endif;
		
		parent::close_page();
		
	}
	
	/**
	 *
	 * Initialize the admin screen of the plugin
	 *
	 */
	function initialize_settings() {
		
		register_setting( 'ae_options', 'ae_options', array(&$this, 'validate') );
		
		add_settings_section('ads_easy_google', __('Use the Google AdSense Tags', self::language_file), array(&$this, 'ae_display_use_google'), 'ae_use_adsense');
		
		add_settings_field('use_google_tags', 'Tags:', array(&$this, 'ae_display_tags'), 'ae_use_adsense', 'ads_easy_google', array(' '.__('Check to use the Google AdSense Tags', self::language_file)));
		
		add_settings_field('ae_engine_time', __('Search Engines:', self::language_file), array(&$this, 'ae_display_time'), 'ae_use_adsense', 'ads_easy_google', array(__('How long should the widget be displayed to visitors from search engines (in minutes)?', self::language_file).'<br/>'));
		
		add_settings_field('use_own_css', 'CSS:', array(&$this, 'ae_display_css'), 'ae_use_adsense', 'ads_easy_google', array(__('You can enter your own style for the widgets here. This will overwrite the styles of your theme.', self::language_file), __('If you leave this empty, you can still style every instance of the widget individually.', self::language_file)));
		
		add_settings_field('ae_compress', __('Compress Style Sheet:', self::language_file), array(&$this, 'compress_field'), 'ae_use_adsense', 'ads_easy_google', array(__('Click here to compress the style sheet.', self::language_file)));
		
		add_settings_field('ae_inline', __('Debug:', self::language_file), array(&$this, 'inline_field'), 'ae_use_adsense', 'ads_easy_google', array(__('If you can&#39;t reach the dynamical style sheet, you&#39;ll have to display the styles inline. By clicking here you can do so.', self::language_file)));
		
		add_settings_field('ae_resize', false, array(&$this, 'resize_field'), 'ae_use_adsense', 'ads_easy_google');
	
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
		
		a5_textarea('ae_css', 'ae_options[ae_css]', @self::$options['ae_css'], false, array('rows' => 7, 'cols' => 35));
		
	}
	
	function compress_field($labels) {
		
		a5_checkbox('compress', 'ae_options[compress]', @self::$options['compress'], $labels[0]);
		
	}
	
	function inline_field($labels) {
		
		a5_checkbox('inline', 'ae_options[inline]', @self::$options['inline'], $labels[0]);
		
	}
	
	function resize_field() {
		
		a5_resize_textarea(array('ae_css'));
		
	}
		
	function validate($input) {
		
		self::$options['use_google_tags'] = isset($input['use_google_tags']) ? true : NULL;
		
		self::$options['ae_time'] = trim($input['ae_time']);
		self::$options['ae_css'] = trim($input['ae_css']);
		
		if (!is_numeric(self::$options['ae_time'])) :
		
			add_settings_error('ae_settings', 'wrong-time', __('Please give numeric value for the minutes.', self::language_file), 'error');
			
			unset(self::$options['ae_time']);
			
		endif;
		
		self::$options['compress'] = isset($input['compress']) ? true : false;
		self::$options['inline'] = isset($input['inline']) ? true : false;
		
		return self::$options;
	
	}

} // end of class

?>