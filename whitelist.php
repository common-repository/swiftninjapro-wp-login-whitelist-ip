<?php
/**
* @package SwiftNinjaProWhitelistLoginIP
*/

if(!defined('ABSPATH')){
  echo '<meta http-equiv="refresh" content="0; url=/404">';
  die('404 Page Not Found');
}

if(!class_exists('SwiftNinjaProWhitelistLoginIPWhitelist')){
  
  class SwiftNinjaProWhitelistLoginIPWhitelist{
    
    private $whitelist = array();
    private $browserlist = array();
    private $whitelistEnabled;
    private $browserlistEnabled;
    private $listCheck;
    private $listCheckNext;
    
    public $pluginSettingsName;
    
    private $func;
    
    function checkWhitelist_set($pluginSettingsName){
      $this->pluginSettingsName = $pluginSettingsName;
      
      if(file_exists(plugin_dir_path(__FILE__).'functions.php')){
        require(plugin_dir_path(__FILE__).'functions.php');
        if($swiftNinjaProWhitelistLoginIPFunctions){
          $this->func = $swiftNinjaProWhitelistLoginIPFunctions;
		  $this->func->register($pluginSettingsName);
        }
      }
      
      $sNameIPEnabled = 'SwiftNinjaPro'.$pluginSettingsName.'_IPEnabled';
      $sNameBREnabled = 'SwiftNinjaPro'.$pluginSettingsName.'_BREnabled';
      $sNameIPList = 'SwiftNinjaPro'.$pluginSettingsName.'_IPList';
      $sNameBRList = 'SwiftNinjaPro'.$pluginSettingsName.'_BRList';
      
      $this->whitelistEnabled = $this->func->trueText(get_option($sNameIPEnabled));
      $this->browserlistEnabled = $this->func->trueText(get_option($sNameBREnabled));
      $whitelist = get_option($sNameIPList);
      $browserlist = get_option($sNameBRList);
      
      $this->whitelist = explode("\n", str_replace("\r", "", $whitelist));
      $this->browserlist = explode("\n", str_replace("\r", "", $browserlist));
    }
    
    function checkWhitelist_get($userIP){
      $whitelist = $this->whitelist;
      if(!$this->whitelistEnabled || $whitelist[0] === ''){
        return true;
      }
      for($i = 0; $i < count($whitelist); $i++){
        if($userIP == $whitelist[$i]){
          return true;
        }
      }
      return false;
    }
    
    function checkBrowserlist_get($userBrowser){
      $browserlist = $this->browserlist;
      if(!$this->browserlistEnabled || $browserlist[0] === ''){
        return true;
      }
      for($i = 0; $i < count($browserlist); $i++){
        if($userBrowser == $browserlist[$i]){
          return true;
        }
      }
      return false;
    }
    
  }
  
  $swiftNinjaProWhitelistLoginIPWhitelist = new SwiftNinjaProWhitelistLoginIPWhitelist();
  
}
