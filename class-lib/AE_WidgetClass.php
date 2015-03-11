<?php

/**
 *
 * Class Ads Easy Widget
 *
 * @ Ads Easy
 *
 * building the actual widget
 *
 */
class Ads_Easy_Widget extends WP_Widget {
	
	const language_file = 'adseasy';
	
	private static $options;
	
	function __construct() {
			
		$widget_opts = array( 'description' => __('You can show ads in your sidebars and other widgetareas with this widget. Define, on what kind of pages they will show up.', self::language_file) );
		$control_opts = array ( 'width' => 400 );
		
		parent::WP_Widget(false, $name = 'Ads Easy', $widget_opts, $control_opts);
		
		self::$options = get_option('ae_options');
	
	}

	function form($instance) {
		
		$defaults = array(
			'title' => NULL,
			'name' => NULL,
			'adblock' => NULL,
			'style' => NULL,
			'homepage' => 1,
			'frontpage' => false,
			'page' => false,
			'category' => 1,
			'single' => false,
			'date' => false,
			'archive' => false,
			'tag' => false,
			'attachment' => false,
			'taxonomy' => false,
			'author' => false,
			'search' => false,
			'not_found' => false,
			'login_page' => false,
			'logged_in' => 1,
			'search_engine' => 1,
			'normal' => 1
		);
		
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
		$archive=esc_attr($instance['archive']);
		$tag=esc_attr($instance['tag']);
		$attachment=esc_attr($instance['attachment']);
		$taxonomy=esc_attr($instance['taxonomy']);
		$author=esc_attr($instance['author']);
		$search=esc_attr($instance['search']);
		$not_found=esc_attr($instance['not_found']);
		$login_page=esc_attr($instance['login_page']);
		$logged_in=esc_attr($instance['logged_in']);
		$search_engine=esc_attr($instance['search_engine']);
		$normal=esc_attr($instance['normal']);
		
		$base_id = 'widget-'.$this->id_base.'-'.$this->number.'-';
		$base_name = 'widget-'.$this->id_base.'['.$this->number.']';
		
		$options = array (
			array($base_id.'homepage', $base_name.'[homepage]', $homepage, __('Homepage', self::language_file)),
			array($base_id.'frontpage', $base_name.'[frontpage]', $frontpage, __('Frontpage (e.g. a static page as homepage)', self::language_file)),
			array($base_id.'page', $base_name.'[page]', $page, __('&#34;Page&#34; pages', self::language_file)),
			array($base_id.'category', $base_name.'[category]', $category, __('Category pages', self::language_file)),
			array($base_id.'single', $base_name.'[single]', $single, __('Single post pages', self::language_file)),
			array($base_id.'date', $base_name.'[date]', $date, __('Archive pages', self::language_file)),
			array($base_id.'archive', $base_name.'[archive]', $archive, __('Post type archives', self::language_file)),
			array($base_id.'tag', $base_name.'[tag]', $tag, __('Tag pages', self::language_file)),
			array($base_id.'attachment', $base_name.'[attachment]', $attachment, __('Attachments', self::language_file)),
			array($base_id.'taxonomy', $base_name.'[taxonomy]', $taxonomy, __('Custom Taxonomy pages (only available, if having a plugin)', self::language_file)),
			array($base_id.'author', $base_name.'[author]', $author, __('Author pages', self::language_file)),
			array($base_id.'search', $base_name.'[search]', $search, __('Search Results', self::language_file)),
			array($base_id.'not_found', $base_name.'[not_found]', $not_found, __('&#34;Not Found&#34;', self::language_file)),
			array($base_id.'login_page', $base_name.'[login_page]', $login_page, __('Login Page (only available, if having a plugin)', self::language_file))
		);
		
		$checkall = array($base_id.'checkall', $base_name.'[checkall]', __('Check all', self::language_file));
		
		a5_text_field($base_id.'name', $base_name.'[name]', $name, __('Title (will be displayed in blog):', self::language_file), array('space' => true, 'class' => 'widefat'));
		a5_text_field($base_id.'title', $base_name.'[title]', $title, __('Adname (internal widgettitle):', self::language_file), array('space' => true, 'class' => 'widefat'));
		a5_checkgroup(false, false, $options, __('Check, where you want to show the widget. By default, it is showing on the homepage and the category pages:', self::language_file), $checkall);
		a5_checkbox($base_id.'logged_in', $base_name.'[logged_in]', $logged_in, __('Show to logged in users.', self::language_file), array('space' => true));
		a5_checkbox($base_id.'search_engine', $base_name.'[search_engine]', $search_engine, __('Show to visitors, who come from search engines.', self::language_file), array('space' => true));
		a5_checkbox($base_id.'normal', $base_name.'[normal]', $normal, __('Show to other visitors.', self::language_file), array('space' => true));
		a5_textarea($base_id.'adblock', $base_name.'[adblock]', $adblock, __('Just paste the code of your ad here.', self::language_file), array('space' => true, 'style' => 'height: 60px;', 'class' => 'widefat'));
		if (empty(self::$options['ae_css'])) a5_textarea($base_id.'style', $base_name.'[style]', $style, sprintf(__('Here you can finally style the widget. Simply type something like%1$s%2$sborder: 1px solid;%1$sborder-color: #000000;%3$s%1$sto get just a black line around the widget. If you leave that section empty, your theme will style the widget.', self::language_file), '<br />', '<strong>', '</strong>'), array('space' => true, 'style' => 'height: 60px;', 'class' => 'widefat'));
		a5_resize_textarea(array($base_id.'adblock', $base_id.'style'), true);
		
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
		 $instance['archive'] = strip_tags($new_instance['archive']);
		 $instance['attachment'] = strip_tags($new_instance['attachment']);
		 $instance['taxonomy'] = strip_tags($new_instance['taxonomy']);
		 $instance['author'] = strip_tags($new_instance['author']);
		 $instance['search'] = strip_tags($new_instance['search']);
		 $instance['not_found'] = strip_tags($new_instance['not_found']);
		 $instance['login_page'] = @$new_instance['login_page'];
		 $instance['logged_in'] = strip_tags($new_instance['logged_in']);
		 $instance['search_engine'] = strip_tags($new_instance['search_engine']);
		 $instance['normal'] = strip_tags($new_instance['normal']);
		 
		 return $instance;
	}
	 
	function widget($args, $instance) {
		
	$visitor = $this->get_visitor();
	
	if (!empty($instance[$visitor])) :
		
		// get the type of page, we're actually on
	
		if (is_front_page()) $pagetype[]='frontpage';
		if (is_home()) $pagetype[]='homepage';
		if (is_page()) $pagetype[]='page';
		if (is_category()) $pagetype[]='category';
		if (is_single()) $pagetype[]='single';
		if (is_date()) $pagetype[]='date';
		if (is_archive()) $pagetype[]='archive';
		if (is_tag()) $pagetype[]='tag';
		if (is_attachment()) $pagetype[]='attachment';
		if (is_tax()) $pagetype[]='taxonomy';
		if (is_author()) $pagetype[]='author';
		if (is_search()) $pagetype[]='search';
		if (is_404()) $pagetype[]='not_found';
		if (!isset($pagetype)) $pagetype[]='login_page';
		
		// display only, if said so in the settings of the widget
		
		foreach ($pagetype as $page) if ($instance[$page]) $show_widget = true;
	
		if ($show_widget) :
			
			// the widget is displayed
			
			extract( $args );
			
			$title = apply_filters('widget_title', $instance['name']);	
			
			if (empty($instance['style'])) :
				
				$ae_before_widget=$before_widget;
				$ae_after_widget=$after_widget;
			
			else :
				
				$ae_style=str_replace(array("\r\n", "\n", "\r"), '', $instance['style']);
				
				$ae_before_widget='<div id="'.$widget_id.'" style="'.$ae_style.'" class="widget_ads_easy_widget">';
				$ae_after_widget='</div>';
				
			endif;
			
			echo $ae_before_widget;
			
			if ( $title ) echo $before_title . $title . $after_title;
		 
			/* This is the actual function of the plugin, it fills the widget with the ad */
				
			echo $instance['adblock'];
			
			echo $ae_after_widget;
		
		endif;
	
	endif;
	
	} // function widget
	
	/**
	 *
	 * Checking ip address to store in case, visitor comes from search engine
	 *
	 */
	function get_ip_address() {
		
    	foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) :
	
			if (array_key_exists($key, $_SERVER) === true) :
			
				foreach (explode(',', $_SERVER[$key]) as $ip) :
					
					if (filter_var($ip, FILTER_VALIDATE_IP) !== false) return $ip;
				
				endforeach;
			
			endif;
		
    	endforeach;
	
	}
	
	/**
	 *
	 * Trying to find out, what kind of visitor we have
	 *
	 * there are three possible types:
	 *
	 * 'normal' visitor (just surfing the site)
	 * logged in user
	 * visitor that comes from a search engine
	 *
	 */
	function get_visitor() {
		
		$options = get_option('ae_options');
		
		$visitor = (is_user_logged_in()) ? 'logged_in' : false;
		
		if (false === $visitor) : // not logged in
		
			$ip_address = $this->get_ip_address();
			
			$visitor = (is_multisite()) ? get_site_transient($ip_address) : get_transient($ip_address); // unknown ip
			
			if (false === $visitor) :
			
				if (!isset($_SERVER['HTTP_REFERER'])) :
				
					$visitor = false;
					
				else :
	
					$search_engines = array('/search?', 'web.info.com', 'search.', 'del.icio.us/search', 'soso.com', '/search/', '.yahoo.', 'google');
					
					foreach ($search_engines as $engine) :
					
						if (strpos($_SERVER['HTTP_REFERER'], $engine) !== false ) :
						
							$visitor = 'search_engine';
							
							// setting transient for half an hour, when coming from search engine
							
							if (is_multisite()) :
							
								set_site_transient($ip_address, 'search_engine', 60 * $options['ae_time']);
								
							else:
							
								set_transient($ip_address, 'search_engine', 60 * $options['ae_time']);
								
							endif;
							
						else :
						
							$vistor = false;
						
						endif;
						
					endforeach;
				
				endif;
				
			endif;
			
		endif;
		
		if (false === $visitor) $visitor = 'normal'; // not logged in and not from search engine
		
		return $visitor;
		
	}

} // class widget

add_action('widgets_init', create_function('', 'return register_widget("Ads_Easy_Widget");'));


?>