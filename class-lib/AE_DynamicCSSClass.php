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
		
		if (!array_key_exists('inline', self::$options)) self::$options['inline'] = NULL;
		
		parent::A5_DynamicFiles('wp', 'css', false, self::$options['inline']);
		
		$eol = "\r\n";
		$tab = "\t";
		
		$css_selector = '[id^="ads_easy_widget"].widget_ads_easy_widget';
		
		parent::$styles .= $eol.'/* CSS portion of Ads Easy */'.$eol.$eol;
		
		$style=str_replace('; ', ';'.$eol.$tab, str_replace(array("\r\n", "\n", "\r"), ' ', self::$options['ae_css']));

		parent::$styles.='div'.$css_selector.','.$eol.'li'.$css_selector.','.$eol.'aside'.$css_selector.' {'.$eol.$tab.$style.$eol.'}'.$eol;

	}
	
} // AE_Dynamic CSS

?>