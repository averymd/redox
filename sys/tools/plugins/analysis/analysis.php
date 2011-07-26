<?php

class analysis {
	
	function analysis(&$con) {
		$this->outlet = $con;
	}
	
	function index() {
		$content = file_get_contents(EXPATH.'/sys/base/database.php');
		$content = htmlspecialchars($content);
		$content = preg_replace('/(".*")/', '<span style="color:blue;">${1}</span>', $content);
		$content = preg_replace('/(\'.*\')/U', '<span style="color:blue;">${1}</span>', $content);
		
		$content = preg_replace('/(class\s*.*\s*{)/', '<span style="color:orange;">${1}</span>', $content);
		
		$content = preg_replace('/(function\s*.*\(.*\)\s*{)/', '<span style="color:red;">${1}</span>', $content);
		//$content = preg_replace('/(([\w])+\(.*\))/', '<span style="color:red">${1}</span>', $content);
		
			$content = preg_replace('/(if\s*\(.*\)\s*{)/', '<span style="color:yellow;">${1}</span>', $content);
			$content = preg_replace('/(switch\(.*\))\s*{/', '<span style="color:yellow;">${1}</span>', $content);
			$content = preg_replace('/((case.*\:|break\;))/', '<span style="color:yellow;">${1}</span>', $content);
			
			$content = preg_replace('/(}\s*else\s*{)/', '<span color="color:yellow;">${1}</span>', $content);
		
		
		
		
		$content = preg_replace('/(\/\*[\s\S]*?\*\/)/', '<span style="color:#88f;">${1}</span>', $content);
		$content = preg_replace('/(\/\/.*$)/m', '<span style="color:#88f;">${1}</span>', $content);
		
		
		$this->outlet->set('content', $content);
	}
	
}

?>