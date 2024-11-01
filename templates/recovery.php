<?php

if(!defined('ABSPATH')){
  echo '<meta http-equiv="refresh" content="0; url=/404">';
  die('404 Page Not Found');
}

global $SwiftNinjaProSettings_PluginName;
$SwiftNinjaProSettings_PluginName = 'WhitelistLoginIP';

global $swiftNinjaProFunc;
if(file_exists(plugin_dir_path(__FILE__).'../functions.php')){
  require(plugin_dir_path(__FILE__).'../functions.php');
  if($swiftNinjaProWhitelistLoginIPFunctions){
    $swiftNinjaProFunc = $swiftNinjaProWhitelistLoginIPFunctions;
	$swiftNinjaProFunc->register($SwiftNinjaProSettings_PluginName);
  }
}

$SwiftNinjaProSettingsEnabled = $swiftNinjaProFunc->trueText(SwiftNinjaPro_settings_GetOption('Enabled'));

if(!$SwiftNinjaProSettingsEnabled){
  echo '<meta http-equiv="refresh" content="0; url=/404">';
  die('404 Page Not Found');
}

session_start();

?>

<style>
input, input:focus {
  outline: none;
  border: solid 2px;
  padding: 5px 10px;
}

.swiftninjaro-settings-pre{
    border: solid 3px #2b333d;
    border-radius: 10px;
    font-size: 14px;
    color: #3c3d3c;
    margin: 2%;
    padding: 10px;
    background: #eaeaea;
    display: block;
    font-family: monospace;
    white-space: pre-wrap;
    width: 85%;
}

.swiftninjapro-settings-button {
  all: initial;
  all: unset;
  border: solid 2px #3a3a3a;
  color: #f7f7f7;
  background: #2877c1;
  border-radius: 10px;
  font-size: 20px;
  padding: 5px 10px;
}

.swiftninjapro-settings-button:hover {
  border: solid 2px #0f0f0f;
  color: #e8e8e8;
  background: #2269aa;
}
</style>

<!--<h1>WP-Login and WP-Admin Whitelist Recovery</h1>-->

<?php

if(isset($_POST['mode']) && strip_tags($_POST['mode']) === 'SendEmail'){
  
  $continueForm = true;
  
  $token = htmlentities(strip_tags($_POST['token']));
  $sToken = htmlentities(strip_tags($_SESSION['token']));
  
  
  if($sToken && $token){
    if($sToken !== $token){
      echo '<h3>Session Expired</h3>';
      $continueForm = false;
    }
  }else{
    echo '<h3>Session Expired</h3>';
    $continueForm = false;
  }
  
  if($continueForm){
  
    $email = htmlentities(strip_tags($_POST['email']));

    if(email_exists($email)){
      $user = get_user_by('email', $email);
      
      if(!$user->user_registered){
        echo '<h3>Invalid Email</h3>';
        $continueForm = false;
      }else{
        $today = time();
        $user_date = strtotime(get_userdata(get_current_user_id( ))->user_registered);
        $diference = $today - $user_date;
        $diferencedays = floor($diference/(60*60*24));
        if($diferencedays < 7){ //require user registard for over a week
          echo '<h3>Invalid Email</h3>';
          $continueForm = false;
        }
      }
      
      if($continueForm && $user->user_email === $email && in_array('administrator', $user->roles, true)){

        $randToken1;
        $randToken2;

        if(function_exists('random_bytes')){
          $randToken1 = bin2hex(random_bytes(32));
          $randToken2 = bin2hex(random_bytes(16));
        }else{
          $randToken1 = bin2hex(openssl_random_pseudo_bytes(32));
          $randToken2 = bin2hex(openssl_random_pseudo_bytes(16));
        }
        
        $_SESSION['code_1'] = $randToken1;
        $_SESSION['code_2'] = $randToken2;
        $_SESSION['code_timestamp'] = microtime(true);
        
        $_SESSION['user_email'] = $user->user_email;
        $_SESSION['user_name'] = $user->name;
        
        wp_mail($user->user_email, 'WP-Login and WP-Admin Whitelist Recovery Codes', 'Code 1: '.$randToken1."\n".'Code 2: '.$randToken2."\n");
        
        if(empty($_SESSION['token'])){
          if(function_exists('random_bytes')){
            $_SESSION['token'] = bin2hex(random_bytes(32));
          }else{
            $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));
          }
        }

        $token = $_SESSION['token'];

        echo '<h3>You have been emailed the recovery codes</h3>';
        echo '<h3>They will expire in 10 minutes</h3>';
        echo '<form action="" autocomplete="off" method="POST" enctype="multipart/form-data">';
        echo '<input type="hidden" name="mode" value="RecoverCodes">';
        echo '<input type="hidden" name="token" value="'.$token.'">';
        echo '<input type="hidden" name="email" value="'.$email.'">';
        echo '<input type="text" name="code1" placeholder="Ender Code 1" style="border-radius: 10px; width: 300px;">';
        echo '<br><br><input type="text" name="code2" placeholder="Ender Code 2" style="border-radius: 10px; width: 300px;">';
        echo '<br><br><input type="submit" value="Submit Codes" class="swiftninjapro-settings-button">';
        echo '</form>';
      }else if($continueForm){
        echo '<h3>Invalid Email</h3>';
        $continueForm = false;
      }
    }else{
      echo '<h3>Invalid Email</h3>';
      $continueForm = false;
    }

  }
  
}else if(isset($_POST['mode']) && strip_tags($_POST['mode']) === 'RecoverCodes'){
  
  $continueForm = true;
  
  $token = htmlentities(strip_tags($_POST['token']));
  $sToken = htmlentities(strip_tags($_SESSION['token']));
  
  if($sToken && $token){
    if($sToken !== $token){
      echo '<h3>Session Expired</h3>';
      $continueForm = false;
    }
  }else{
    echo '<h3>Session Expired</h3>';
    $continueForm = false;
  }
  
  if($continueForm){
  
    $email = htmlentities(strip_tags($_POST['email']));

    if(email_exists($email)){
      $user = get_user_by('email', $email);
      if($user->user_email === $email && in_array('administrator', $user->roles, true)){
        $recoveryCode1 = htmlentities(strip_tags($_POST['code1']));
        $recoveryCode2 = htmlentities(strip_tags($_POST['code2']));
        
        $recoveryCodes = array(
        	'code1' => htmlentities(strip_tags($_SESSION['code_1'])),
        	'code2' => htmlentities(strip_tags($_SESSION['code_2'])),
        	'timestamp' => htmlentities(strip_tags($_SESSION['code_timestamp'])),
        );
        
        if(floatval($recoveryCodes['timestamp'])+600000000 < microtime(true)){
          SwiftNinjaPro_settings_DeleteOption('_Recovery_Codes_:'.$_SERVER['REMOTE_ADDR']);
          echo '<h3>Recovery Codes Session Expired</h3>';
          $continueForm = false;
        }
        
        if($continueForm){
          
          if($recoveryCodes['code1'] === $recoveryCode1 && $recoveryCodes['code2'] === $recoveryCode2){
            SwiftNinjaPro_settings_UpdateOption('Enabled', false);
            SwiftNinjaPro_settings_DeleteOption('_Recovery_Codes_:'.$_SERVER['REMOTE_ADDR']);
            echo '<h3>Plugin Successfully Disabled</h3>';
            
            $adminEmail = get_option('admin_email');
            if($adminEmail){
            	wp_mail($adminEmail, 'WP-Login and WP-Admin Whitelist Was Disabled', 'WP-Login and WP-Admin Whitelist was Disabled through recovery codes'."\n\n".'User: '.htmlentities(strip_tags($_SESSION['user_name']))."\n".'Email: '.htmlentities(strip_tags($_SESSION['user_email']))."\n");
            }
            
            echo '<script>window.location.replace("'.esc_url(get_admin_url()).'admin.php?page=swiftninjapro-wp-login-whitelist-ip");</script>';
            $continueForm = false;
          }else{
            SwiftNinjaPro_settings_DeleteOption('_Recovery_Codes_:'.$_SERVER['REMOTE_ADDR']);
            echo '<h3>Invalid Recovery Codes</h3>';
            $continueForm = false;
          }
        }
      }
    }else{
      echo '<h3>Invalid Email</h3>';
      $continueForm = false;
    }

  }
  
}else{
  
  if(empty($_SESSION['token'])){
    if(function_exists('random_bytes')){
      $_SESSION['token'] = bin2hex(random_bytes(32));
    }else{
      $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));
    }
  }
  
  $token = $_SESSION['token'];
  
  echo '<form action="" method="POST" enctype="multipart/form-data">';
  echo '<input type="hidden" name="mode" value="SendEmail">';
  echo '<input type="hidden" name="token" value="'.$token.'">';
  echo '<input type="email" name="email" placeholder="Email" style="border-radius: 10px; width: 300px;">';
  echo '<br><br><input type="submit" value="Send Email" class="swiftninjapro-settings-button">';
  echo '</form>';
}


function SwiftNinjaPro_settings_GetOption($name){
  global $SwiftNinjaProSettings_PluginName;
  $pluginName = $SwiftNinjaProSettings_PluginName;
  $sName = SwiftNinjaPro_settings_SetOption($name, $pluginName);
  $option = get_option($sName);
  if(is_array($option)){
    foreach($option as $key => $value){
      $option[$key] = htmlentities(strip_tags($value));
    }
  }else{$option = htmlentities(strip_tags($option));}
  return $option;
}

function SwiftNinjaPro_settings_UpdateOption($name, $value){
  global $SwiftNinjaProSettings_PluginName;
  $pluginName = $SwiftNinjaProSettings_PluginName;
  $sName = SwiftNinjaPro_settings_SetOption($name, $pluginName);
  update_option($sName, $value);
}

function SwiftNinjaPro_settings_DeleteOption($name){
  global $SwiftNinjaProSettings_PluginName;
  $pluginName = $SwiftNinjaProSettings_PluginName;
  $sName = SwiftNinjaPro_settings_SetOption($name, $pluginName);
  delete_option($sName);
}

function SwiftNinjaPro_settings_SetOption($name, $pluginName){
  return htmlentities(strip_tags('SwiftNinjaPro'.$pluginName.'_'.$name));
}

//option display functions
function SwiftNinjaProAddCheckBox($setting, $option, $text, $default=false){
  global $SwiftNinjaProSettings_PluginName;
  global $swiftNinjaProFunc;
  $pluginName = $SwiftNinjaProSettings_PluginName;
  $sName = SwiftNinjaPro_settings_SetOption($option, $pluginName);
  $set;
  if(isset($setting)){
    $set = $swiftNinjaProFunc->trueText($setting);
  }else{$set == $default;}
  if($set){
    echo '<input type="checkbox" name="'.$sName.'" checked="true"><strong>'.$text.'</strong></input>';
  }else{
    echo '<input type="checkbox" name="'.$sName.'"><strong>'.$text.'</strong></input>';
  }
}

function SwiftNinjaProAddList($setting, $option, $text, $default=false){
  global $SwiftNinjaProSettings_PluginName;
  $pluginName = $SwiftNinjaProSettings_PluginName;
  $sName = SwiftNinjaPro_settings_SetOption($option, $pluginName);
  $set;
  if(isset($setting) && $setting !== ''){
    $set = $setting;
  }else if($default){
    $set = $default;
  }else{$set = '';}
  $result = '<textarea class="swiftninjapro-settings-textarea" name="'.$sName.'" rows="10" cols="20" placeholder="'.$text.'">';
  $result = $result.$set;
  $result = $result.'</textarea>';
  echo $result;
}

function SwiftNinjaProSettingAddInput($setting, $option, $text, $default, $inputSize, $placeholder = false, $type = "text"){
  global $SwiftNinjaProSettings_PluginName;
  $pluginName = $SwiftNinjaProSettings_PluginName;
  $sName = SwiftNinjaPro_settings_SetOption($option, $pluginName);
  $setValue;
  if($setting){
    $setValue = $setting;
  }else{$setValue = $default;}
  
  $setPlaceholder = $text;
  if($placeholder){
    $setPlaceholder = $placeholder;
  }
  
  $result = '<strong>'.$text.' </strong>';
  $result .= '<input type="'.$type.'" name="'.$sName.'" placeholder="'.$setPlaceholder.'" value="'.$setValue.'" style="border-radius: 10px; width: '.$inputSize.';"/>';
  
  echo $result;
}

?>
