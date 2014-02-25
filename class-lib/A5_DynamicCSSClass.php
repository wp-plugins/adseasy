<?php

/**
 *
 * Class A5 Dynamic CSS
 *
 * @ A5 Plugin Framework
 * Version: 0.9.8 alpha
 *
 * Presses the dynamical CSS of all plugins into one virtual stylesheet
 *
 */

class A5_DynamicCSS {
	
	public static $styles = '';
	
	function __construct() {
		
		add_action('init', array ($this, 'add_rewrite'));
		add_action('template_redirect', array ($this, 'css_template'));
		add_action ('wp_enqueue_scripts', array ($this, 'enqueue_css'));

	}
	
	function add_rewrite() {
		
		   global $wp;
		   $wp->add_query_var('A5_file');
	
	}
	
	function css_template() {
		
		   if (get_query_var('A5_file') == 'css') {
				   
				   header('Content-type: text/css');
				   echo $this->write_dss();
				   
				   exit;
		   }
	}

	function enqueue_css () {
		
		$A5_css_file=get_bloginfo('url').'/?A5_file=css';
			
		wp_register_style('A5-framework', $A5_css_file, false, '0.9.8 alpha', 'all');
		wp_enqueue_style('A5-framework');
		
	}
	
	function write_dss() {
	
		$eol = "\r\n";
		
		$css_text = '@charset "UTF-8";'.$eol.'/* CSS Document createtd by the A5 Plugin Framework */'.$eol;
		
		$css_text .= self::$styles;
		
		echo $css_text;	
		
	}
	
} // A5_Dynamic CSS

?>