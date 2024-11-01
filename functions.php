<?php

if(!defined('ABSPATH')){
  echo '<meta http-equiv="refresh" content="0; url=/404">';
  die('404 Page Not Found');
}

if(!class_exists('SwiftNinjaProWhitelistLoginIPFunctions')){
  class SwiftNinjaProWhitelistLoginIPFunctions{
    
    private $pluginSettingsName;
    
    function register($pluginSettingsName){
      $this->pluginSettingsName = $pluginSettingsName;
    }
    
    function getBrowser(){
      $browserVersion = get_option('SwiftNinjaPro'.$this->pluginSettingsName.'_BrowserDetectAlgorithm');
      if($browserVersion !== null){
        return $this->getBrowserVersion($browserVersion);
      }
      $useNewBrowserDetect = get_option('SwiftNinjaPro'.$this->pluginSettingsName.'_useNewBrowserDetect');
      if($useNewBrowserDetect !== null){
        $useNewBrowserDetect = $this->trueText($useNewBrowserDetect);
        if(!$useNewBrowserDetect){
          return $this->getBrowserVersion('v1');
        }else{
          return $this->getBrowserVersion('v2');
        }
      }
      return $this->getBrowserVersion('latest');
    }
    
    function getBrowserVersion($v){
      if($v === 'v1'){
        return $this->getBrowser_v1();
      }else{
        return $this->getBrowser_v2();
      }
    }
    
    function browserAlgorithmLatestVersion(){
      return 2;
    }
    
    function getBrowser_v2(){
      $fullUserBrowser = (!empty($_SERVER['HTTP_USER_AGENT'])? 
      $_SERVER['HTTP_USER_AGENT']:getenv('HTTP_USER_AGENT'));
      $fullUserBrowser = strtolower(htmlentities(strip_tags($fullUserBrowser)));
      $userBrowser = explode(')', $fullUserBrowser);
      $userBrowser = $userBrowser[count($userBrowser)-1];
      
      if((!$userBrowser || $userBrowser === '' || $userBrowser === ' ' || strpos($userBrowser, 'like gecko') === 1) && strpos($fullUserBrowser, 'windows') !== false){
      	return 'Internet-Explorer';
      }else if((strpos($userBrowser, 'edge/') !== false || strpos($userBrowser, 'edg/') !== false) && strpos($fullUserBrowser, 'windows') !== false){
      	return 'Microsoft-Edge';
      }else if(strpos($userBrowser, 'chrome/') === 1 || strpos($userBrowser, 'crios/') === 1){
      	return 'Google-Chrome';
      }else if(strpos($userBrowser, 'firefox/') !== false || strpos($userBrowser, 'fxios/') !== false){
      	return 'Mozilla-Firefox';
      }else if(strpos($userBrowser, 'safari/') !== false && strpos($fullUserBrowser, 'mac') !== false){
      	return 'Safari';
      }else if(strpos($userBrowser, 'opr/') !== false && strpos($fullUserBrowser, 'opera mini') !== false){
      	return 'Opera-Mini';
      }else if(strpos($userBrowser, 'opr/') !== false){
        return 'Opera';
      }
      
      return false;
    }
    
    function getBrowser_v1(){
      $browser = strtolower(htmlentities(strip_tags($_SERVER['HTTP_USER_AGENT'])));
      
      if(strpos($browser, 'edge') !== FALSE){
        return 'Microsoft-Edge';
      }else if(strpos($browser, 'msie') !== FALSE){
        return 'Internet-Explorer';
      }else if(strpos($browser, 'trident') !== FALSE){
        return 'Internet-Explorer';
      }else if(strpos($browser, 'firefox') !== FALSE){
        return 'Mozilla-Firefox';
      }else if(strpos($browser, 'chrome') !== FALSE){
        return 'Google-Chrome';
      }else if(strpos($browser, 'opera mini') !== FALSE){
        return "Opera-Mini";
      }else if(strpos($browser, 'opera') !== FALSE){
        return "Opera";
      }else if(strpos($browser, 'safari') !== FALSE){
        return "Safari";
      }
      
      return false;
    }
    
    
    function checkForProxy(){
      $test_HTTP_proxy_headers = array(
        'HTTP_VIA',
        'VIA',
        'Proxy-Connection',
        'HTTP_X_FORWARDED_FOR',  
        'HTTP_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_FORWARDED',
        'HTTP_CLIENT_IP',
        'HTTP_FORWARDED_FOR_IP',
        'X-PROXY-ID',
        'MT-PROXY-ID',
        'X-TINYPROXY',
        'X_FORWARDED_FOR',
        'FORWARDED_FOR',
        'X_FORWARDED',
        'FORWARDED',
        'CLIENT-IP',
        'CLIENT_IP',
        'PROXY-AGENT',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'FORWARDED_FOR_IP',
        'HTTP_PROXY_CONNECTION'
      );
      foreach($test_HTTP_proxy_headers as $header){
        if(isset($_SERVER[$header]) && !empty($_SERVER[$header])) {
          return true;
        }
      }
      return false;
    }
    
    
    function get_string_between($string, $start, $end, $pos = 1){
      $cPos = 0;
      $ini = 0;
      $result = '';
      for($i = 0; $i < $pos; $i++){
        $ini = strpos($string, $start, $cPos);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        $result = substr($string, $ini, $len);
        $cPos = $ini + $len;
      }
      return $result;
    }
    
    function trueText($text){
      if($text === 'true' || $text === 'TRUE' || $text === 'True' || $text === true || $text === 1 || $text === 'on'){
        return true;
      }else{return false;}
    }
    
  }

  //$swiftNinjaProWhitelistLoginIPFunctions = new SwiftNinjaProWhitelistLoginIPFunctions();

}

$swiftNinjaProWhitelistLoginIPFunctions = new SwiftNinjaProWhitelistLoginIPFunctions();
