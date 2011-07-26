<?php

function redirect($url, $silent = false) {
	$sm = redox::getState();
	if (REQURL == '') {
		$cur = CONTROLLER.'/'.FUNC;
	} else {
		$cur = REQURL;
	}
	
	if (!$silent) {
		$sm->setVar('passThroughURL', $cur);
	}
	
	$_SESSION['sm'] = serialize($sm);
	header('Location: '.FOLDER.'/'.$url);
	exit;
}

function passThrough($default) {
	$sm = redox::getState();
	$pass = $sm->getVar('passThroughURL');
	if($pass) {
		$loc = $pass;
	} else {
		$loc = $default;
	}

	$sm->setVar('passThroughURL', ''); //::TODO:: MAKE SURE THIS IS DESIRED FUNCTIONALITY

	header('Location: '.FOLDER.'/'.$loc);
}

function flash() {
	$sm = redox::getState();
	echo $sm->getVar('flash');
	$sm->unsetVar('flash');	
}

function hasFlash() {
	$sm = redox::getState();
	return $sm->getVar('flash') != '';
}

function setFlash($msg) {
	$sm = redox::getState();
	$sm->setVar('flash',$msg);
}

function getFlash() {
	$sm = redox::getState();
	$temp = $sm->getVar('flash');
	$sm->unsetVar('flash');	
	return $temp;
}

function benchmarkDisplay($values, $color = '#000', $label = 'BENCHMARKS') {
	$buildstring = '<a name="'.strtolower($label).'"></a><fieldset id="bm_'.strtolower($label).'" style="border: 1px solid '.$color.'; background: #EEE; margin-bottom: 2em; padding: .5em 1em 1em;">
		<legend style="color: '.$color.'; padding: 0 .5em; border: 1px solid '.$color.'; background: #EEE; margin-top: 0; line-height: 200%;">'.$label.'</legend>
		<table summary="Benchmark table" cellspacing="1" style="background: #FFF; color: '.$color.'; width: 100%;">';
			switch ($label) {
				case 'ERRORS':
					$buildstring .= '<thead><tr style="text-align: left;"><th style="background: #CCC; text-align: center;">#</th><th style="background: #CCC;">Level</th><th style="background: #CCC;">File</th><th style="background: #CCC; text-align: center;">Line</th><th style="background: #CCC;">Message</th></tr></thead>';
				break;
				case 'QUERIES':
					$buildstring .= '<thead><tr style="text-align: left;"><th style="background: #CCC; text-align: center;">#</th><th style="background: #CCC;">Query</th><th style="background: #CCC;">Result</th></tr></thead>';
				break;
				case 'BENCHMARKS':
					$buildstring .= '<thead><tr style="text-align: left;"><th style="background: #CCC;">Benchmark</th><th style="background: #CCC;">Time</th></tr></thead>';
				break;
				default:
					$buildstring .= '<thead><tr style="text-align: left;"><th style="background: #CCC;">Field</th><th style="background: #CCC;">Value</th></tr></thead>';
				break;
			}
			$buildstring .= '<tbody>';
			switch ($label) {
				case 'ERRORS':
					$count = 1;
					foreach ($values as $value) {
						$buildstring .= '<tr><td style="background: #DDD; text-align: center;">'.$count++.'</td><td style="background: #DDD;">'.$value[0].'</td><td style="background: #DDD;">'.$value[1].'</td><td style="background: #DDD; text-align: center;">'.$value[2].'</td><td style="background: #DDD;">'.$value[3].'</td></tr>';
					}
				break;

				case 'QUERIES':
					$count = 1;
					foreach ($values as $key => $value) {
						$buildstring .= '<tr><td style="background: #DDD; text-align: center;"><a name="queries'.$count.'">'.$count++.'</a></td><td style="background: #DDD;">'.treat::xss($value[0]).'</td><td style="background: #DDD;">'.treat::xss($value[1]).'</td></tr>';
					}
				break;

				case 'BENCHMARKS':
				case '$_POST':
				case '$_FILES':
				case '$_GET':
					foreach ($values as $key => $value) {
							$buildstring .= '<tr><td style="width: 25%; background: #DDD;">'.treat::xss($key).'</td><td style="width: 75%; background: #DDD;">'.treat::xss($value).'</td></tr>';
					}
				break;
				case 'STATEMACHINE':
					foreach ($values as $value) {
						$value[1] = (is_string($value[1]) ? $value[1] : print_r($value[1], true));
						$buildstring .= '<tr><td style="width: 25%; background: #DDD;">'.treat::xss($value[0]).'</td><td style="width: 75%; background: #DDD;"><pre style="font-size:130%;">'.treat::xss($value[1]).'</pre></td></tr>';
					}
				break;

				default:
					foreach ($values as $key => $value) {
						if (is_array($value)) {
							$buildstring .= '<tr><td style="width: 25%; background: #DDD;">'.treat::xss($value[0]).'</td><td style="width: 75%; background: #DDD;">'.treat::xss($value[1]).'</td></tr>';
						} else {
							$buildstring .= '<tr><td style="width: 25%; background: #DDD;">'.treat::xss($key).'</td><td style="width: 75%; background: #DDD;">'.treat::xss($value).'</td></tr>';
						}
					}
				break;
			}

			$buildstring .= '</tbody></table>
	</fieldset>';

	return "\r\n\t".$buildstring."\r\n";
}

function addProfileInfo() {
	$file = ob_get_contents();
	ob_end_clean();
	$bm = redox::getBm();
	$sm = redox::getState();

	if (PROFILER && !STATUS) {
		$rep = '<div style="padding: 2em; clear: both;">';

		/* Time Benchmarks. */
		if (isset($bm) && count($bm->marks) > 0) {
			$values = array();
			foreach(array_keys($bm->marks) as $key) { $values[$key] = $bm->time($key); }
			$rep .= benchmarkDisplay($values, '#060', 'BENCHMARKS');
		}

		/* Queries. */
		if (isset($bm) && count($bm->queries) > 0) {
			$rep .= benchmarkDisplay($bm->queries, '#009', 'QUERIES');
		}

		/* $_POST Values. */
		if (isset($_POST) && count($_POST) > 0) {
			$rep .= benchmarkDisplay($_POST, '#900', '$_POST');
		}

		/* $_GET Values. */
		if (isset($_GET) && count($_GET) > 0) {
			$rep .= benchmarkDisplay($_GET, '#909', '$_GET');
		}

		/* State Machine. */
		if (isset($sm) && (count($sm->states) > 0 || count($sm->session) > 0)) {
			$values = array();
			foreach($sm->states as $key => $value) { $values[] = array('State: '.$key, ($value[1] ? 'TRUE' : 'FALSE')); }
			foreach($sm->session as $key => $value) { $values[] = array('Var: '.$key, $value[0]); }
			$rep .= benchmarkDisplay($values, '#055', 'STATEMACHINE');
		}

		$rep .= '</div>';
		$file = str_replace('</body>',$rep."\r\n</body>",$file);
	}

	if (!STATUS) {
		/* Errors. */
		if (isset($bm) && count($bm->errors) > 0) {
			$rep = '<div style="padding: 2em; clear: both;">';
			$rep .= benchmarkDisplay($bm->errors, '#900', 'ERRORS');
			$rep .= '</div>';
			$file = str_replace('<body>',"<body>\r\n".$rep, $file);
		}
	}

	echo $file;
}

function redoxErrorHandler($level, $message, $file, $line) {
	$bm = redox::getBm();
	
	$errortype = array(
		E_ERROR => 'Error',
		E_WARNING => 'Warning',
		E_PARSE => 'Parsing Error',
		E_NOTICE => 'Notice',
		E_CORE_ERROR => 'Core Error',
		E_CORE_WARNING => 'Core Warning',
		E_COMPILE_ERROR => 'Compile Error',
		E_COMPILE_WARNING => 'Compile Warning',
		E_USER_ERROR => 'User Error',
		E_USER_WARNING => 'User Warning',
		E_USER_NOTICE => 'User Notice',
		E_STRICT => 'Runtime Notice',
		E_RECOVERABLE_ERROR => 'Catchable Fatal Error'
	);

	switch ($level) {
		case E_USER_ERROR:
			echo "<b>My ERROR</b> [$level] $message<br />\n";
			echo "  Fatal error on line $line in file $file";
			echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
			echo "Aborting...<br />\n";
			exit(1);
		break;
		case E_STRICT:
		break;
		default:
			$bm->addError($errortype[$level], $file, $line, $message);
		break;
	}
}

function ifsetor(&$var, $default) {
	return (isset($var) && $var !== false ? $var : $default);
}

function handleMail() {
	$controller = redox::getController();
	if (isset($controller->mailer) && !$controller->mailer->sendemail()) {
		echo "Sending email failed.";
	}
}

function deltree($f) {
	if (is_dir($f) && !is_link($f)) {
		foreach(scandir($f) as $item) {
			if ($item != '.' && $item != '..') {
				deltree($f.'/'.$item);
			}
		}
		return rmdir($f);
	} else {
		return unlink($f);
	}
}

function is_assoc($_array) {
    if ( !is_array($_array) || empty($array) ) {
        return -1;
    }
    foreach (array_keys($_array) as $k => $v) {
        if ($k !== $v) {
            return true;
        }
    }
    return false;
} 

function is_assoc_callback($a, $b) {
    return $a === $b ? $a + 1 : 0;
}

function attributeString($attributeArray) {
	$return = '';
	if(is_array($attributeArray)) {
		foreach($attributeArray as $key=>$value) {
			$return .= "$key=\"$value\" ";
		}
	} else if(is_string($attributeArray)) {
		$return = $attributeArray;
	}
	return $return;
	
}


?>