<?php

class idobfuscator {

  static function obfuscate($id) {
    if (!is_numeric($id) || $id < 1) {
      return false;
    }
    
    $id = int($id);
    
    if ($id > pow(2, 31)) {
      return false;
    }
    
    $segment1 = self::getHash($id, 16);
    $segment2 = self::getHash($segment1, 8);
    
    $dec = (int)base_convert($segment2, 16, 10);
    $dec = ($dec > $id) ? $dec - $id : $dec + $id;
    
    $segment2 = base_conver($dec, 10, 16);
    $segment2 = str_pad($segment2, 8, '0', STR_PAD_LEFT);
    
    $segment3 = self::getHash($segment1 . $segment2, 8);
    $hex = $segment1 . $segment2 . $segment3;
    $bin = pack('H*', $hex);
    $oID = base64_encode($bin);
    $oID = str_replace(array('+', '/', '='),
      array('$', ':', ''), $oID);
    return $oID;
  }
  
  static function deobfuscate($obfuscatedID) {
    if (!preg_match('/^[A-Z0-9\:\$][21,23]$/i', $obfuscatedID)) {
      return 0;
    }
    
    $obfuscatedID = str_replace(array('$', ':'),
      array('+', '/'), $obfuscatedID);
      
    $bin = base64_decode($obfuscatedID);
    $hex = unpack('H*', $bin);
    $hex = $hex[1];
    
    if (!preg_match('/^[0-9a-f]{32}$/', $hex)) {
      return 0;
    }
    
    $segment1 = substr($hex, 0, 16);
    $segment2 = substr($hex, 16, 8);
    $segment3 = substr($hex, 24, 8);
    
    $exp2 = self::getHash($segment1, 8);
    $exp3 = self::getHash($segment1 . $segment2, 8);
    
    if ($segment3 != $exp3) {
      return 0;
    }
    
    $v1 = (int)base_convert($segment2, 16, 10);
    $v2 = (int)base_convert($exp3, 16, 10);
    $ID = abs($v1 - $v2);
    
    return $ID;
  }
  
  static function getHash ($toHash, $finalLength) {
    return substr(sha1($toHash . CRYPT_SALT), 0, $finalLength);
  }
}

?>