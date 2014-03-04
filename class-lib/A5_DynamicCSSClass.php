<?php

/**
 *
 * Class A5 Dynamic CSS
 *
 * @ A5 Plugin Framework
 * Version: 0.9.9 alpha
 *
 * Presses the dynamical CSS of all plugins into one virtual stylesheet
 * 
 * parameter $place = 'wp' selects where to attach the stylesheet (wp, admin, login)
 *
 */

class A5_DynamicCSS {
	
	public static $styles = '';
	
	private static $hooks;
	
	function __construct($place = 'wp', $hooks = false) {
		
		if ($hooks === false) :
		
			self::$hooks = $hooks;
			
		else :
		
			foreach ($hooks as $hook) self::$hooks[] = $hook;
		
		endif;
		
		add_action('init', array ($this, 'add_rewrite'));
		add_action('template_redirect', array ($this, 'css_template'));
		
		add_action ($place.'_enqueue_scripts', array ($this, $place.'_enqueue_css'));

	}
	
	function add_rewrite() {
		
		global $wp;
		$wp->add_query_var('A5_file');
	
	}
	
	function css_template() {
		
		if (get_query_var('A5_file') == 'wp_css' || get_query_var('A5_file') == 'admin_css' || get_query_var('A5_file') == 'login_css') :
		   
			header('Content-type: text/css');
			
			echo $this->write_dss();
			
			exit;
		
		endif;
	
	}
	
	function wp_enqueue_css () {
		
		$A5_css_file=get_bloginfo('url').'/?A5_file=wp_css';
			
		wp_register_style('A5-framework', $A5_css_file, false, '0.9.9 alpha', 'all');
		wp_enqueue_style('A5-framework');
		
	}
	
	function admin_enqueue_css ($hook) {
		
		if (self::$hooks !== false) :
		
			if (!in_array($hook, self::$hooks)) return;
			
		endif;
		
		$A5_css_file=get_bloginfo('url').'/?A5_file=admin_css';
			
		wp_register_style('A5-framework', $A5_css_file, false, '0.9.9 alpha', 'all');
		wp_enqueue_style('A5-framework');
		
	}
	
	function login_enqueue_css () {
		
		$A5_css_file=get_bloginfo('url').'/?A5_file=login_css';
			
		wp_register_style('A5-framework', $A5_css_file, false, '0.9.9 alpha', 'all');
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