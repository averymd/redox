<?php

include('sys/libraries/class.phpmailer.php');

class mailer extends PHPMailer {
	
	var $sent = false;
	
	function mailer() {
		$sent = true;
	}
	
	function sendemail() {
		$val = false;
		if(!$this->sent) {
			$val = $this->Send();
			$sent = true;
		}
		return $val;
	}
	
	function single($from, $address, $subject, $viewfile, $data = array()) {
		if(is_array($from)) {
			$this->From = $from[0];
			$this->FromName = $from[1];
		} else {
			$this->From = $from;
		}
		
		if(is_array($address)) {
			$this->AddAddress($address[0], $address[1]);
		} else {
			$this->AddAddress($address);
		}
		
		$this->Subject = $subject;
		$this->MsgHTML($this->getBody($viewfile, $data));
		
		$this->IsSendmail();
	}
	
	function multiple($from, $address, $subject, $viewfile, $data = array()) {
		if(is_array($from)) {
			$this->From = $from[0];
			$this->FromName = $from[1];
		} else {
			$this->From = $from;
		}
		
		foreach($address as $a) {
			if(is_array($a)) {
				$this->AddAddress($a[0], $a[1]);
			} else {
				$this->AddAddress($a);
			}
		}
				
		$this->Subject = $subject;
		$this->MsgHTML($this->getBody($viewfile, $data));
		
		$this->IsSendmail();
	}
	
	function getBody($viewFile, $data) {
		if(count($data) > 0) {
			foreach($data as $key => $value) {
				$$key = $value;
			}
		}
		if(file_exists('app/views/'.$viewFile.'.php')) {
			ob_start();
			include('app/views/'.$viewFile.'.php');
			$yield = ob_get_contents();
			ob_end_clean();
			ob_start();
		}
		return $yield;
	}
	
	
}

?>