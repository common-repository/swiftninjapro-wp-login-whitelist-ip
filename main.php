<?php
/**
* @package SwiftNinjaProWhitelistLoginIP
*/

if(!defined('ABSPATH')){
  echo '<meta http-equiv="refresh" content="0; url=/404">';
  die('404 Page Not Found');
}

if(!class_exists('SwiftNinjaProWhitelistLoginIPMain')){

  class SwiftNinjaProWhitelistLoginIPMain{
    
    public $currentUrl;
    public $userIP;
    public $userBrowser;
    
    public $pluginSettingsName;
    
    private $func;
    
    function start($pluginSettingsName){
      $this->pluginSettingsName = $pluginSettingsName;
      $this->currentUrl = esc_url('http'.(isset($_SERVER['HTTPS']) ? 's' : '').'://'.sanitize_text_field($_SERVER['HTTP_HOST']).sanitize_text_field($_SERVER['REQUEST_URI']));

      if(file_exists(plugin_dir_path(__FILE__).'functions.php')){
        require(plugin_dir_path(__FILE__).'functions.php');
        if($swiftNinjaProWhitelistLoginIPFunctions){
          $this->func = $swiftNinjaProWhitelistLoginIPFunctions;
		  $this->func->register($pluginSettingsName);
        }
      }
      
      $this->userBrowser = esc_html($this->func->getBrowser());
      
      $adminUrl = rtrim(admin_url('admin.php'), '/');
      if(strpos($this->currentUrl, $adminUrl.'?page=swiftninjapro-wp-login-whitelist-ip-recovery') === false && strpos($this->currentUrl, $adminUrl.'/?page=swiftninjapro-wp-login-whitelist-ip-recovery') === false){
        add_action('after_setup_theme', array($this, 'run_after_theme'));
      }
    }
    
    function run_after_theme(){
      $loginUrlType = $this->is_login_url();
      if($loginUrlType){
        $userIP = htmlentities($_SERVER['REMOTE_ADDR']);
        $userBrowser = $this->userBrowser;
        require_once(plugin_dir_path(__FILE__).'whitelist.php');
        $swiftNinjaProWhitelistLoginIPWhitelist->checkWhitelist_set($this->pluginSettingsName);
        $isValidIP = $swiftNinjaProWhitelistLoginIPWhitelist->checkWhitelist_get($userIP);
        $isValidBrowser = $swiftNinjaProWhitelistLoginIPWhitelist->checkBrowserlist_get($userBrowser);
        
        $redirect404 = $this->func->trueText(get_option('SwiftNinjaPro'.$this->pluginSettingsName.'_Redirect404'));
        $redirectLogin = $this->func->trueText(get_option('SwiftNinjaPro'.$this->pluginSettingsName.'_RedirectLogin'));
        
        $blockProxy = $this->func->trueText(get_option('SwiftNinjaPro'.$this->pluginSettingsName.'_BlockProxy'));
        
        $allowShortBots = $this->func->trueText(get_option('SwiftNinjaPro'.$this->pluginSettingsName.'_AllowShortBots'));
        
        $IPBlockAdmin = $this->func->trueText(get_option('SwiftNinjaPro'.$this->pluginSettingsName.'_IPUseAdmin'));
        $BRBlockAdmin = $this->func->trueText(get_option('SwiftNinjaPro'.$this->pluginSettingsName.'_BRUseAdmin'));
        
        if($blockProxy && $this->func->checkForProxy()){
          if(($loginUrlType === 1 || $loginUrlType === 2) && $allowShortBots){
            if($redirectLogin){
              echo '<script>window.location.replace("/login");</script>';
              echo '<meta http-equiv="refresh" content="0; url=/login">';
            }else{
              echo '<script>window.location.replace("/404");</script>';
              echo '<meta http-equiv="refresh" content="0; url=/404">';
            }
          }else if($redirectLogin && $loginUrlType !== 3){
            echo '<meta http-equiv="refresh" content="0; url=/login">';
            die('Error 404: page not found');
          }else if($redirect404){
            echo '<meta http-equiv="refresh" content="0; url=/404">';
            die('Error 404: page not found');
          }else{
            die('A Proxy Server IP is Not Allowed To Access This Page!');
          }
        }
        
        if((!$isValidIP || !$isValidBrowser) && ($loginUrlType !== 3 || (!$isValidIP && $IPBlockAdmin) || (!$isValidBrowser && $BRBlockAdmin))){
          if(($loginUrlType === 1 || $loginUrlType === 2) && $allowShortBots){
            if($redirectLogin){
              echo '<script>window.location.replace("/login");</script>';
              echo '<meta http-equiv="refresh" content="0; url=/login">';
            }else{
              echo '<script>window.location.replace("/404");</script>';
              echo '<meta http-equiv="refresh" content="0; url=/404">';
            }
          }else if($redirectLogin && $loginUrlType !== 3){
            echo '<meta http-equiv="refresh" content="0; url=/login">';
            die('Error 404: page not found');
          }else if($redirect404){
            echo '<meta http-equiv="refresh" content="0; url=/404">';
            die('Error 404: page not found');
          }else{
            $msg = '<h2>You';
            if($isValidIP && $isValidBrowser){$msg .= 'r';}
            if(!$isValidIP){$msg .= ' IP';}
            if(!$isValidIP && !$isValidBrowser){$msg .= ' and';}
            if(!$isValidBrowser){$msg .= ' Browser';}
            if((!$isValidIP && !$isValidBrowser) || ($isValidIP && $isValidBrowser)){$msg .= ' are';}else{$msg .= ' is';}
            $msg .= ' Not Whitelisted!</h2>';
            $msg .= '<h3>If this is in error, ask your administrator or hosting provider to add you';
            if(!$isValidIP || !$isValidBrowser){$msg .= 'r';}
            if(!$isValidIP){$msg .= ' IP';}
            if(!$isValidIP && !$isValidBrowser){$msg .= ' and';}
            if(!$isValidBrowser){$msg .= ' Browser';}
            $msg .= ' to the Whitelist</h3><br>';
            $msg .= '<h3>Things To Check:</h3>';
            if(!$isValidIP){$msg .= '<h4> - Make sure your connected to the right wifi network</h4>';}
            if(!$isValidBrowser){$msg .= '<h4> - Make sure your using the right browser</h4>';}
            if(!$isValidIP && !$isValidBrowser){$msg .= '<h4> - Make sure your not using incognito mode</h4>';}
            if(!$isValidIP || !$isValidBrowser){$msg .= '<h4> - Make sure your not using a proxy server</h4>';}
            if($isValidIP && $isValidBrowser){$msg .= '<h4> - Try refreshing the page</h4>';}
            die($msg);
          }
        }
      }
    }
    
    private function is_login_url(){
      $loginUrl = esc_url(rtrim(strtok(strip_tags(wp_login_url()), '?'), '/')); $registerUrl = esc_url(rtrim(strtok(strip_tags(wp_registration_url()), '?'), '/')); $lostPassUrl = esc_url(rtrim(strtok(strip_tags(wp_lostpassword_url()), '?'), '/'));
      $currentUrlBasic = esc_url(rtrim(strtok($this->currentUrl, '?'), '/'));
      if($this->currentUrl == $loginUrl || $this->currentUrl == $loginUrl.'/' || $this->currentUrl == $registerUrl || $this->currentUrl == $registerUrl.'/' || $this->currentUrl == $lostPassUrl || $this->currentUrl == $lostPassUrl.'/'){
        return 1;
      }else if($currentUrlBasic == $loginUrl || $currentUrlBasic == $registerUrl || $currentUrlBasic == $lostPassUrl){
          return 2;
      }else if(is_admin()){
        return 3;
      }
      return false;
    }
    
  }

  $swiftNinjaProWhitelistLoginIPMain = new SwiftNinjaProWhitelistLoginIPMain();

}
