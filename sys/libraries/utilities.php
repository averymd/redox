<?php

class Utilities {
	
	function Utilities() {
		//nothing to do in here
	}
	
	function salEncryptPassword($password) {
		$salt ="";
		$salt = '$1$'.substr(str_shuffle("abcdefghijklmnopqrstuvwxyz0123456789"), 0, 8).'$';
		return $encpassword = crypt($password, $salt);
	}
	
	function saltEncryptCheckPassword($passToCheck, $passFromDB) {
		$encryptpassword = crypt($passToCheck, substr($passFromDB, 0, 12));
		return ($encryptpassword == $passFromDB);
	}
	
	function Paginator($url, $numLinks, $max, $circular = false) {
		$this->url = $url;
		$this->max = $max;
		$this->numLinks = $numLinks;
		$this->circular = $circular;
	}
	//TODO :: redo this?
	function createPaginationLinks($cur) {
		$output = "";
		$start = number_format($this->numLinks/2,0)-1;
		if(!$this->circular) {
			for($i = $cur-$start; $i < $max && $i < $cur+(int)$this->numLinks; $i++)
			{
				$output .= "<a href='$this->url/$i'>$i</a>";
			}
		} else {
			$c = 0;
			$i = ($cur-$start > 0 ? $cur-$start : 0);
			while($c < $this->numLinks && $c <= $this->max) {
				$output .= "<a href='$this->url/$i'".($i == $cur ? 'class="selected"' : '').">$i</a>";
				$c++;
				if($i < $this->max) {
					$i++;
				} else {
					$i = 0;
				}
			}
		}
		return $output;
	}
	
	
}



?>