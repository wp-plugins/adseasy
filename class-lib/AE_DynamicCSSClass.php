<?php

/**
 *
 * Class AE Dynamic CSS
 *
 * Extending A5 Dynamic Files
 *
 * Presses the dynamical CSS of the Ads Easy Widget into a virtual style sheet
 *
 */

class AE_DynamicCSS extends A5_DynamicFiles {
	
	private static $options;
	
	function __construct() {
		
		self::$options =  get_option('ae_options');
		
		if (!isset(self::$options['inline'])) self::$options['inline'] = false;
		
		if (!array_key_exists('compress', self::$options)) self::$options['compress'] = false;
		
		parent::__construct('wp', 'css', 'all', false, self::$options['inline']);
		
		$eol = (self::$options['compress']) ? '' : "\n";
		$tab = (self::$options['compress']) ? ' ' : "\t";
		
		$css_selector = 'widget_ads_easy_widget[id^="ads_easy_widget"]';
		
		parent::$wp_styles .= (!self::$options['compress']) ? $eol.'/* CSS portion of Ads Easy */'.$eol.$eol : '';
		
		$style = str_replace('; ', ';'.$eol.$tab, str_replace(array("\r\n", "\n", "\r"), ' ', self::$options['ae_css']));

		parent::$wp_styles .= parent::build_widget_css($css_selector, '').'{'.$eol.$tab.$style.$eol.'}'.$eol;

	}
	
} // AE_Dynamic CSS

?>