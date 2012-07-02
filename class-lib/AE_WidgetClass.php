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
	
function Ads_Easy_Widget() {
		
	$widget_opts = array( 'description' => __('You can show ads in your sidebars and other widgetareas with this widget. Define, on what kind of pages they will show up.', 'adseasy') );
	$control_opts = array( 'width' => 400 );
	
	parent::WP_Widget(false, $name = 'Ads Easy', $widget_opts, $control_opts);

}
    
	
function form($instance) {
	
	$defaults = array( 'homepage' => 1, 'category' => 1 );
	
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
	$tag=esc_attr($instance['tag']);
	$attachment=esc_attr($instance['attachment']);
	$taxonomy=esc_attr($instance['taxonomy']);
	$author=esc_attr($instance['author']);
	$search=esc_attr($instance['search']);
	$not_found=esc_attr($instance['not_found']);
	$logged_in=esc_attr($instance['logged_in']);
	
	$language_file = 'adseasy';
	
	$base_id = 'widget-'.$this->id_base.'-'.$this->number.'-';
	$base_name = 'widget-'.$this->id_base.'['.$this->number.']';
	
	$options = array (array('homepage', $homepage, __('Homepage', $language_file)), array('frontpage', $frontpage, __('Frontpage (e.g. a static page as homepage)', $language_file)), array('page', $page, __('&#34;Page&#34; pages', $language_file)), array('category', $category, __('Category pages', $language_file)), array('single', $single, __('Single post pages', $language_file)), array('date', $date, __('Archive pages', $language_file)), array('tag', $tag, __('Tag pages', $language_file)), array('attachment', $attachment, __('Attachments', $language_file)), array('taxonomy', $taxonomy, __('Custom Taxonomy pages (only available, if having a plugin)', $language_file)), array('author', $author, __('Author pages', $language_file)), array('search', $search, __('Search Results', $language_file)), array('not_found', $not_found, __('&#34;Not Found&#34;', $language_file)));

	$field[] = array ('type' => 'text', 'id_base' => $base_id, 'name_base' => $base_name, 'field_name' => 'name', 'label' => __('Title (will be displayed in blog):', $language_file), 'value' => $name, 'class' => 'widefat', 'space' => 1);
	$field[] = array ('type' => 'text', 'id_base' => $base_id, 'name_base' => $base_name, 'field_name' => 'title', 'label' => __('Adname (internal widgettitle):', $language_file), 'value' => $title, 'class' => 'widefat', 'space' => 1);
	$field[] = array ('type' => 'checkgroup', 'id_base' => $base_id, 'name_base' => $base_name, 'label' => __('Check, where you want to show the widget. By default, it is showing on the homepage and the category pages:', $language_file), 'options' => $options, 'checkall' => __('Check all', $language_file));
	$field[] = array ('type' => 'checkbox', 'id_base' => $base_id, 'name_base' => $base_name, 'field_name' => 'logged_in', 'label' => __('Don&#39;t show the ad to logged in users.', $language_file), 'value' => $logged_in, 'space' => 1);	
	$field[] = array ('type' => 'textarea', 'id_base' => $base_id, 'name_base' => $base_name, 'field_name' => 'adblock', 'class' => 'widefat', 'label' => __('Just paste the code of your ad here.', $language_file), 'value' => $adblock, 'space' => 1);
	$field[] = array ('type' => 'textarea', 'id_base' => $base_id, 'name_base' => $base_name, 'field_name' => 'style', 'class' => 'widefat', 'label' => sprintf(__('Here you can finally style the widget. Simply type something like%1$s%2$sborder-left: 1px dashed;%1$sborder-color: #000000;%3$s%1$sto get just a dashed black line on the left. If you leave that section empty, your theme will style the widget.', $language_file), '<br />', '<strong>', '</strong>'), 'value' => $style, 'space' => 1);
	$field[] = array ('type' => 'resize', 'id_base' => $base_id, 'field_name' => array('adblock', 'style'));
	
	foreach ($field as $args) :
	
		$menu_item = new A5_WidgetControlClass($args);
 
 	endforeach;
	
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
	 $instance['attachment'] = strip_tags($new_instance['attachment']);
	 $instance['taxonomy'] = strip_tags($new_instance['taxonomy']);
	 $instance['author'] = strip_tags($new_instance['author']);
	 $instance['search'] = strip_tags($new_instance['search']);
	 $instance['not_found'] = strip_tags($new_instance['not_found']);
	 $instance['logged_in'] = strip_tags($new_instance['logged_in']);
	 
	 return $instance;
}
 
function widget($args, $instance) {
	
// user logged in and do we show ads to them?

if (!$instance['logged_in'] || ($instance['logged_in'] && !is_user_logged_in())) :
	
	// get the type of page, we're actually on
	
	if (is_front_page()) $ae_pagetype='frontpage';
	if (is_home()) $ae_pagetype='homepage';
	if (is_page()) $ae_pagetype='page';
	if (is_category()) $ae_pagetype='category';
	if (is_single()) $ae_pagetype='single';
	if (is_date()) $ae_pagetype='date';
	if (is_tag()) $ae_pagetype='tag';
	if (is_attachment()) $ae_pagetype='attachment';
	if (is_tax()) $ae_pagetype='taxonomy';
	if (is_author()) $ae_pagetype='author';
	if (is_search()) $ae_pagetype='search';
	if (is_404()) $ae_pagetype='not_found';
	
	// display only, if said so in the settings of the widget
	
	if ($instance[$ae_pagetype]) :
		
		// the widget is displayed
		
		extract( $args );
		
		$title = apply_filters('widget_title', $instance['name']);	
		
		if (empty($instance['style'])) :
			
			$ae_before_widget=$before_widget;
			$ae_after_widget=$after_widget;
		
		else :
			
			$ae_style=str_replace(array("\r\n", "\n", "\r"), '', $instance['style']);
			
			$ae_before_widget='<div id="'.$widget_id.'" style="'.$ae_style.'">';
			$ae_after_widget='</div>';
			
		endif;
		
		echo $ae_before_widget;
		
		if ( $title ) echo $before_title . $title . $after_title;
	 
		/* This is the actual function of the plugin, it fills the sidebar with the customized excerpts */
			
		echo $instance['adblock'];
		
		echo $ae_after_widget;
	
	endif;

endif;

} // function widget

} // class widget

add_action('widgets_init', create_function('', 'return register_widget("Ads_Easy_Widget");'));


?>