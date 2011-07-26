<?php

// FORM CONTROLS

class form {
	function textarea($name, $default = '', $attr = '') {
		$validator = redox::getValidator();
		$value = $validator->formVal($name);
		$value = ($value ? $value : $default);
		echo "<textarea name='$name' id='$name' ".attributeString($attr).">".$value."</textarea>";
	}

	function text($name, $default = '', $attr = '') {
		$validator = redox::getValidator();
		$value = $validator->formVal($name);
		$value = ($value ? $value : $default);
		echo "<input type='text' name='$name' id='$name' value='".$value."' ".attributeString($attr)."/>";
	}

	function password($name, $attr = '') {
		$validator = redox::getValidator();
		echo "<input type='password' name='$name' id='$name' value='' ".attributeString($attr)."/>";
	}

	function select($name, $values, $attr = '', $multiple = false, $setValue = '') {
		$validator = redox::getValidator();
		if ($multiple) {
			$attr = $attr . ' multiple = "multiple"';
		}
		$str = "<select name='$name" . ($multiple ? '[]' : '') . "' id='$name" . ($multiple ? '[]' : '') . "' $attr>\r\n";
		//echo $validator->formVal($name);
		if(is_assoc($values)){
			//echo 'assoc';
			foreach($values as $key => $val) {	
				if (is_array($validator->formVal($name))) {
					$selected = (in_array($key, $validator->formVal($name)) ? ' selected="selected"' : '');
					$str .= "<option value='$key'". $selected .">$val</option>";
				} else {
					$selected = (($validator->formVal($name) == $key) || ($setValue === $key) ? ' selected="selected"' : '');
					$str .= "<option value='$key'". $selected .">$val</option>";
				}
			}
		} else {
			print_r($validator->formVal($name));
			
			foreach($values as $val) {
				//echo 'val = ' . $val;
				//echo 'in array? ' . in_array($val, $validator->formVal($name));
				if (is_array($validator->formVal($name))) {
					$str .= "<option value='$val'".(in_array($val, $validator->formVal($name)) ? ' selected="selected"' : '').">$val</option>";
				} else {
					$selected = (($validator->formVal($name) === $val) || ($setValue === $val) ? ' selected="selected"' : '');
					$str .= "<option value='$val'". $selected .">$val</option>";
				}
			}
		}
		$str .= "</select>";
		echo $str;
	}

	function checkbox($name, $default = false, $attr = '', $value = 1) {
		$validator = redox::getValidator();
		echo "<input type='checkbox' name='$name' id='$name' value='$value' ".($validator->formVal($name) != false || $default ? 'checked=\'checked\'' : '')." ".attributeString($attr)."/>";
	}

	function radio($id, $group, $value, $default = false, $attr = '') {
		$validator = redox::getValidator();
		echo "<input type='radio' name='$group' id='$id' value='$value' ".($validator->formVal($group) == $value || ($default && $validator->formVal($group) == '') ? 'checked=\'checked\'' : '').attributeString($attr)."/>";
	}
}

class tag {
	function a($page, $text, $attr = '') {
		echo '<a href="'.FOLDER."/$page\" ".attributeString($attr).'>'.$text.'</a>';
	}
	
	function img($filename, $attributes = false) {
		echo '<img src="'.FOLDER."/public/_images/$filename\" ".attributeString($attributes).'/>';
	}
	
	function script($scriptname) {
		echo '<script type="text/javascript" src="'.FOLDER."/public/_js/$scriptname.js\"></script>";
	}

	function style($filename, $ie7 = false, $ie6 = false) {
		$tag = '<style type="text/css">'."@import url('".FOLDER."/public/_css/$filename.css');</style>";
		if($ie7) {
			echo '<!--[if IE 7]>'.$tag.'<![endif]-->';
		} else if ($ie6) {
			echo '<!--[if lt IE 7]>'.$tag.'<![endif]-->';
		} else {
			echo $tag;
		}		
	}
	
	function link($href, $text, $attributes = '') {
		echo '<a href="http://' . $href . '" ' . $attributes . '>' . $text . '</a>';
	}
}


?>
