<?php

// Functions that check to see if data conforms to certain rules.
class confirm {

	function confirm() {
		// Do Nothing.
	}

	function model_name($value) {
		// Must be alphanumeric, -, _
		// Must be longer than four characters.
		$regex = '/^[\w-_]{4,}$/';
		return preg_match($regex, $value);
	}

	function function_name($value) {
		// Must be alphanumeric, -, _
		$regex = '/^[\w-_]+$/';
		return preg_match($regex, $value);
	}

	function controller_name($value, $create = true) {
		// Must be alphanumeric, -, _
		// Must be longer than four characters.
		// May not be 'guide' or 'generator' unless $create is false.

		if (
			preg_match('/^[\w-_]{3,}$/', $value) &&
			(!$create || ($create && $value != 'guide' && $value != 'generator'))
		) {
			return $value;
		} else {
			return false;
		}
	}

	function test_name($value) {
		// Must be alphanumeric, -, _
		$regex = '/^[\w-_]+$/';
		return preg_match($regex, $value);
	}

	function exists(&$variable) {
		return isset($variable);
	}
	
	function required($var) {
		return $var != '';
	}

	function match($a, $b) {
		return $a === $b;
	}

	function notmatch($a, $b) {
		return $a !== $b;
	}

	function maxlength($value, $limit) {
		return (strlen(utf8_decode($value)) <= $limit);
	}

	function minlength($value, $limit) {
		return (strlen(utf8_decode($value)) >= $limit);
	}

	function number($value) {
		return ctype_digit($value);
	}
  
  function password($value) {
    if (!empty($value)) {
      $regex = "/^(?=.*[0-9]+.*)(?=.*[a-zA-Z]+.*)[0-9a-zA-Z]{6,}$/";
      return preg_match($regex, $value);
    }
    return false;
  }

	function email($value) {
		// http://data.iana.org/TLD/tlds-alpha-by-domain.txt
		// This matches version 2008012701 excluding XN multibyte
		if (!empty($value)) {
			$tld = 'AC|AD|AE|AERO|AF|AG|AI|AL|AM|AN|AO|AQ|AR|ARPA|AS|ASIA|AT|AU|AW|AX|AZ|BA|BB|BD|BE|BF|BG|BH|BI|BIZ|BJ|BM|BN|BO|BR|BS|BT|BV|BW|BY|BZ|CA|CAT|CC|CD|CF|CG|CH|CI|CK|CL|CM|CN|CO|COM|COOP|CR|CU|CV|CX|CY|CZ|DE|DJ|DK|DM|DO|DZ|EC|EDU|EE|EG|ER|ES|ET|EU|FI|FJ|FK|FM|FO|FR|GA|GB|GD|GE|GF|GG|GH|GI|GL|GM|GN|GOV|GP|GQ|GR|GS|GT|GU|GW|GY|HK|HM|HN|HR|HT|HU|ID|IE|IL|IM|IN|INFO|INT|IO|IQ|IR|IS|IT|JE|JM|JO|JOBS|JP|KE|KG|KH|KI|KM|KN|KP|KR|KW|KY|KZ|LA|LB|LC|LI|LK|LR|LS|LT|LU|LV|LY|MA|MC|MD|ME|MG|MH|MIL|MK|ML|MM|MN|MO|MOBI|MP|MQ|MR|MS|MT|MU|MUSEUM|MV|MW|MX|MY|MZ|NA|NAME|NC|NE|NET|NF|NG|NI|NL|NO|NP|NR|NU|NZ|OM|ORG|PA|PE|PF|PG|PH|PK|PL|PM|PN|PR|PRO|PS|PT|PW|PY|QA|RE|RO|RS|RU|RW|SA|SB|SC|SD|SE|SG|SH|SI|SJ|SK|SL|SM|SN|SO|SR|ST|SU|SV|SY|SZ|TC|TD|TEL|TF|TG|TH|TJ|TK|TL|TM|TN|TO|TP|TR|TRAVEL|TT|TV|TW|TZ|UA|UG|UK|UM|US|UY|UZ|VA|VC|VE|VG|VI|VN|VU|WF|WS|YE|YT|YU|ZA|ZM|ZW';
			$regex = "/^[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+(?:".$tld.")$/";
			$regex = '/^[+a-z0-9_-]+(\.[+a-z0-9_-]+)*@([a-z0-9-]+\.)+[a-z]{2,6}$/iD';
			return preg_match($regex, $value);
		}
		return true;
	}
	

	function attribute($value, $type) {
		// Attribute types specified at:
		// http://www.w3.org/TR/html401/index/attributes.html

		switch ($type) {
			case "fragmentid":
				$regex = "/^[A-Za-z][A-Za-z0-9:_.-]*$/";
				return preg_match($regex, $value);
			break;
			default:
				return false;
			break;
		}
	}

}

?>
