<?php

/**
 *
 * Class A5 Widget Control Class
 *
 * @ A5 Plugin Framework
 *
 * Gets the control elements of a widget
 *
 */

class A5_WidgetControlClass {
	
	public $menu_item;
	
	function A5_WidgetControlClass($args){
		
		extract($args);
		
		$eol = "\r\n";
		$tab = "\t";
		
		$style = ($style) ? ' style="'.$style.'"' : '';
		$class = ($class) ? ' class="'.$class.'"' : '';
		$cols = ($cols) ? ' cols="'.$cols.'"' : '';
		$rows = ($rows) ? ' rows="'.$rows.'"' : '';
		$min = ($min) ? ' min="'.$min.'"' : '';
		$max = ($max) ? ' max="'.$max.'"' : '';
		$step = ($step) ? ' step="'.$step.'"' : '';
		$size = ($size) ? ' size="'.$size.'"' : '';


		switch ($type) :
		
			case 'textarea' :
			
				$output = $eol.$tab.'<label for="'.$id_base.$field_name.'">'.$eol.$tab.$label.$eol.$tab.'<textarea'.$class.' id="'.$id_base.$field_name.'" name="'.$name_base.'['.$field_name.']"'.$cols.$rows.$style.'>'.$value.'</textarea>'.$eol.$tab.'</label>';
			
				break;
				
			case 'checkbox' :
			
				$output = $eol.$tab.'<label for="'.$id_base.$field_name.'">'.$eol.$tab.'<input id="'.$id_base.$field_name.'" name="'.$name_base.'['.$field_name.']" type="checkbox" value="1" '.checked( 1, $value, false ).'/>&nbsp;'.$label.$eol.$tab.'</label>';
			
				break;
				
			case 'radio' :
			
				$output = '';
			
				foreach ($label as $id => $text) :
			
					$output .= $eol.$tab.'<label for="'.$id_base.$field_name.'-'.$id.'">'.$eol.$tab.'<input id="'.$id_base.$field_name.'-'.$id.'" name="'.$name_base.'['.$field_name.']" type="radio" value="'.$options[$id].'" '.checked( $options[$id], $value, false ).'/>&nbsp;'.$text.$eol.$tab.'</label><br />';
					
				endforeach;
			
				break;
				
			case 'select' :
			
				$output = $eol.$tab.'<label for="'.$id_base.$field_name.'">'.$label.'</label>'.$eol.$tab.'<select id="'.$id_base.$field_name.'" name="'.$name_base.'['.$field_name.']"'.$class.$style.'>';
				
				if ($default) $output .= $eol.$tab.'<option value="" '.selected( $value, false, false ).'>'.$default.'</option>';
				
				foreach ($options as $option) :
				
					$output .= '<option value="'.$option[0].'" '.selected( $value, $option[0], false ).' >'.$option[1].'</option>';
				
				endforeach;
				
				$output .= $eol.$tab.'</select>';
			
				break;
				
			case 'checkgroup' :
			
				$output = ($label) ? '<p>'.$label.'</p>' : '';
				$output .= $eol.'<fieldset>'.$eol.'<p>'.$eol.$tab;
				
				foreach ($options as $option) :
				
					$output .= '<label for="'.$id_base.$option[0].'">'.$eol.$tab.'<input id="'.$id_base.$option[0].'" name="'.$name_base.'['.$option[0].']" type="checkbox" value="1" '.checked( 1, $option[1], false ).'/>&nbsp;'.$option[2].$eol.$tab.'</label><br />'.$eol.$tab;
					
				endforeach;
				
				$output .= $eol.'</p>'.$eol;
				
				$output .= ($checkall) ? '<p>'.$eol.$tab.'<label for="'.$id_base.'checkall">'.$eol.$tab.'<input id="'.$id_base.'checkall" name="'.$name_base.'[checkall]" type="checkbox" />&nbsp;'.$checkall.$eol.$tab.'</label>'.$eol.'</p>'.$eol.'</fieldset>'.$eol : $eol.'</fieldset>'.$eol;
			
				break;
				
			case 'resize' :
			
				$output = $eol.'<script type="text/javascript"><!--'.$eol.'jQuery(document).ready(function() {';
																										   
				foreach ($field_name as $field) :
				
					$output .= $eol.$tab.'jQuery("#'.$id_base.$field.'").autoResize();';
				
				endforeach;
				
				$output .= $eol.'});'.$eol.'--></script>'.$eol;
			
				break;
				
			default :
			
				$output = $eol.$tab.'<label for="'.$id_base.$field_name.'">'.$label.$eol.$tab.'<input'.$class.' id="'.$id_base.$field_name.'" name="'.$name_base.'['.$field_name.']" type="'.$type.'"'.$min.$max.$step.$size.' value="'.$value.'"'.$style.' />'.$eol.$tab.'</label>'.$eol;
			
				break;
		
		endswitch;
		
	$this->menu_item = ($space) ? '<p>'.$output.$eol.'</p>'.$eol : $output;
	
	echo $this->menu_item;
		
	}
	
} // A5_WidgetControls

?>