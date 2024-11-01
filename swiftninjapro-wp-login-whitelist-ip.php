<?php
/**
* @package SwiftNinjaProWhitelistLoginIP
*/
/*
Plugin Name: WP-Login and WP-Admin Whitelist
Plugin URI: https://www.swiftninjapro.com/plugins/wordpress/?plugin=swiftninjapro-wp-login-whitelist-ip
Description: A Plugin That only allows whitelisted IP's, or optionally whitelisted browsers, to access wp-login, or optionally wp-admin. This plugin also does Not effect front-end login plugins.
Version: 1.11.1
Author: SwiftNinjaPro
Author URI: https://www.swiftninjapro.com
License: GPLv2 or later
Text Domain: swiftninjapro-wp-login-whitelist-ip
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

if(!defined('ABSPATH')){
  echo '<script>window.location.replace("/404");</script>';
  echo '<meta http-equiv="refresh" content="0; url=/404">';
  die('404 Page Not Found');
}

if(!class_exists('SwiftNinjaProWhitelistLoginIP')){

  class SwiftNinjaProWhitelistLoginIP{
    
    public $pluginSettingsName = 'WP-Login Whitelist';
    public $pluginSettingsPermalink = 'swiftninjapro-wp-login-whitelist-ip';
    public $settings_PluginName = 'WhitelistLoginIP';
    
    public $pluginName;
    private $settings_icon;
    
    function __construct(){
      $this->pluginName = plugin_basename(__FILE__);
    }
    
    
    function shortcode_recovery_page(){
      ob_start();
      $this->recovery_index();
      return ob_get_clean();
    }
    
    function create_recovery_page(){
      
      $guid = get_option('SwiftNinjaPro'.$this->settings_PluginName.'_RecoveryGUID');
      if($guid){return;}
      
      $randGUID = uniqid($this->settings_PluginName.'__RecoveryGUID_', true);
      
      $post_data = array(
        'post_title' => 'Admin Login Whitelist Recovery',
        'post_name' => 'admin-login-whitelist-recovery',
        'post_content' => '[wp-login-recovery-page]',
        'post_status' => 'publish',
        'post_type' => 'page',
        'comment_status' => 'closed',
        'post_password' => 'gHxXeVwvuz6Cez3',
        'guid' => 'http://wordpress.org/plugins/swiftninjapro-wp-login-whitelist-ip/?recovery_guid='.$randGUID
      );
      
      $success = wp_insert_post($post_data);
      if($success){
        update_option('SwiftNinjaPro'.$this->settings_PluginName.'_RecoveryGUID', $randGUID);
      }
    }
    
    function remove_recovery_page(){
      $guid = get_option('SwiftNinjaPro'.$this->settings_PluginName.'_RecoveryGUID');
      if(!$guid){return;}
      
      $post = $this->getIDfromGUID('http://wordpress.org/plugins/swiftninjapro-wp-login-whitelist-ip/?recovery_guid='.$guid);
      if($post){
        $post = get_post($post);
        if($post && $post->ID){
          $success = wp_delete_post($post->ID, true);
          if($success){
            delete_option('SwiftNinjaPro'.$this->settings_PluginName.'_RecoveryGUID');
          }
        }
      }
    }
    
    function getIDfromGUID($guid){
      global $wpdb;
      return $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid=%s", $guid));
    }
    
    
    function register(){
      $this->settings_icon = plugins_url('assets/settings_icon.png', __FILE__);
      add_action('wp_enqueue_scripts', array($this, 'enqueue'));
      add_action('admin_menu', array($this, 'add_admin_pages'));
      add_filter("plugin_action_links_$this->pluginName", array($this, 'settings_link'));
      
      //add_action('init', array($this, 'custom_post_type'));
      //$this->custom_post_type();
      
      add_shortcode('wp-login-recovery-page', array($this, 'shortcode_recovery_page'));
    }
    
    function startPlugin(){
      $sNameEnabled = 'SwiftNinjaPro'.$this->settings_PluginName.'_Enabled';
      $pluginEnabled = get_option($sNameEnabled);
      if(isset($pluginEnabled) && ($pluginEnabled || $pluginEnabled === false || $pluginEnabled === '')){
        $pluginEnabled = $this->trueText($pluginEnabled);
      }else{$pluginEnabled = true;}
      if($pluginEnabled){
        require_once(plugin_dir_path(__FILE__).'main.php');
        $swiftNinjaProWhitelistLoginIPMain->start($this->settings_PluginName);
      }
    }
    
    function trueText($text){
      if($text === 'true' || $text === 'TRUE' || $text === 'True' || $text === true || $text === 1 || $text === 'on'){
    return true;
  }else{return false;}
    }
    
    function settings_link($links){
      $settings_link = '<a href="admin.php?page='.$this->pluginSettingsPermalink.'">Settings</a>';
      array_push($links, $settings_link);
      return $links;
    }
    
    
    function custom_post_type(){
      register_post_type('admin-ip-whitelist-recovery', array('public' => true, 'show_in_menu' => false, 'capability_type' => 'administrator'));
      /*wp_insert_post(array(
        'post_content' => 'test',
        'post_type' => 'page',
        'post_status' => 'publish',
        'comment_status' => 'closed',
        'ping_status' => 'closed',
        'post_password' => '12345'
      ));*/
    }
    
    
    function add_admin_pages(){
      if(empty($GLOBALS['admin_page_hooks']['swiftninjapro-settings'])){
        add_menu_page('SwiftNinjaPro Settings', 'SwiftNinjaPro Settings', 'manage_options', 'swiftninjapro-settings', array($this, 'settings_index'), $this->settings_icon, 100);
      }
      $adminOnlyOptionName = 'SwiftNinjaPro'.$this->settings_PluginName.'_AdminOnly';
      $adminOnly = $this->trueText(get_option($adminOnlyOptionName));
      if($adminOnly && current_user_can('administrator')){
        add_submenu_page('swiftninjapro-settings', $this->pluginSettingsName, $this->pluginSettingsName, 'administrator', $this->pluginSettingsPermalink, array($this, 'admin_index'));
        //add_submenu_page('swiftninjapro-settings', $this->pluginSettingsName.' Recovery', $this->pluginSettingsName.' Recovery', 'administrator', $this->pluginSettingsPermalink.'-recovery', array($this, 'recovery_index'));
      }else if(!$adminOnly){
        add_submenu_page('swiftninjapro-settings', $this->pluginSettingsName, $this->pluginSettingsName, 'manage_options', $this->pluginSettingsPermalink, array($this, 'admin_index'));
      }
    }
    
    function admin_index(){
      require_once(plugin_dir_path(__FILE__).'templates/admin.php');
    }
    
    function settings_index(){
      require_once(plugin_dir_path(__FILE__).'templates/settings-info.php');
    }
    
    function recovery_index(){
      require_once(plugin_dir_path(__FILE__).'templates/recovery.php');
    }
    
    function activate(){
      //$this->custom_post_type();
      $this->create_recovery_page();
      flush_rewrite_rules();
    }
    
    function deactivate(){
      $this->remove_recovery_page();
      flush_rewrite_rules();
    }
    
    function enqueue(){
      //wp_enqueue_style('SwiftNinjaProWhitelistLoginIPStyle', plugins_url('/assets/style.css', __FILE__));
      //wp_enqueue_script('SwiftNinjaProWhitelistLoginIPScript', plugins_url('/assets/script.js', __FILE__));
    }
    
  }

  $swiftNinjaProWhitelistLoginIP = new SwiftNinjaProWhitelistLoginIP();
  $swiftNinjaProWhitelistLoginIP->register();
  $swiftNinjaProWhitelistLoginIP->startPlugin();

  register_activation_hook(__FILE__, array($swiftNinjaProWhitelistLoginIP, 'activate'));
  register_deactivation_hook(__FILE__, array($swiftNinjaProWhitelistLoginIP, 'deactivate'));
  
}
