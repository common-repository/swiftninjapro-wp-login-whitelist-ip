<?php

if(!defined('ABSPATH') || !current_user_can('manage_options')){
  echo '<meta http-equiv="refresh" content="0; url=/404">';
  die('404 Page Not Found');
}

global $SwiftNinjaProSettings_PluginName;
$SwiftNinjaProSettings_PluginName = 'WhitelistLoginIP';
$SwiftNinjaProSettings_PluginDisplayName = 'WP-Login and WP-Admin Whitelist';
$SwiftNinjaProSettings_PluginPermalinkName = 'wp-login-whitelist-ip';


global $swiftNinjaProFunc;
if(file_exists(plugin_dir_path(__FILE__).'../functions.php')){
  require(plugin_dir_path(__FILE__).'../functions.php');
  if($swiftNinjaProWhitelistLoginIPFunctions){
    $swiftNinjaProFunc = $swiftNinjaProWhitelistLoginIPFunctions;
	$swiftNinjaProFunc->register($SwiftNinjaProSettings_PluginName);
  }
}

$SwiftNinjaProSettingAdminOnly = $swiftNinjaProFunc->trueText(SwiftNinjaPro_settings_GetOption_only('AdminOnly'));

if(!current_user_can('administrator') && $SwiftNinjaProSettingAdminOnly){
  die('Only Administrators Are Allowed To Access These Settings!');
}

?>

<style>
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
  font-size: 30px;
  padding: 10px;
}

.swiftninjapro-settings-button:hover {
  border: solid 2px #0f0f0f;
  color: #e8e8e8;
  background: #2269aa;
}


.collapsible {
  background-color: #777;
  color: white;
  cursor: pointer;
  padding: 10px;
  border: none;
  text-align: left;
  outline: none;
  font-size: 1.3em;
  font-weight: bold;
}

.active, .collapsible:hover {
  background-color: #555;
}

.content {
  padding: 0 18px;
  display: none;
  overflow: hidden;
  background-color: #f1f1f1;
}
</style>

<h1>WP-Login and WP-Admin Whitelist</h1>

<?php

echo "<h1>SwiftNinjaPro $SwiftNinjaProSettings_PluginDisplayName</h1>";

if(!isset($_GET['UpdateOptions']) && isset($_GET['settings'])){
  if(esc_html($_GET['settings']) === 'session-error'){
    echo '<h2>Error: Failed to save settings! Session Expired!</h2>';
  }else if(esc_html($_GET['settings']) === 'saved'){
    echo '<h2>Successfully Saved Settings!</h2>';
  }
}

if(isset($_GET['UpdateOptions']) && (!isset($_POST['SwiftNinjaProSettingsToken']) || (esc_html($_POST['SwiftNinjaProSettingsToken']) !== esc_html($_COOKIE['SwiftNinjaProSettingsToken']) && esc_html($_POST['SwiftNinjaProSettingsToken']) !== esc_html($_REQUEST['SwiftNinjaProSettingsToken'])))){
  echo '<script>window.location.replace("'.esc_url(get_admin_url()).'admin.php?page=swiftninjapro-'.$SwiftNinjaProSettings_PluginPermalinkName.'&settings=session-error");</script>';
  exit('<h2>Error: Failed to save settings! Session Expired!</h2>');
}
$SwiftNinjaProSettingsToken = esc_html(wp_generate_password(64));
$SwiftNinjaProSettingsDomain = preg_replace('/^https?:\/\//', '', esc_url(get_admin_url()));
$SwiftNinjaProSettingsDomain = explode('/', $SwiftNinjaProSettingsDomain, 2);
setcookie('SwiftNinjaProSettingsToken', $SwiftNinjaProSettingsToken, 0, '/'.$SwiftNinjaProSettingsDomain[1], $SwiftNinjaProSettingsDomain[0]);


//get and update options
$SwiftNinjaProSettingsAdminOnly = SwiftNinjaPro_settings_GetOption('AdminOnly', 'administrator');
$SwiftNinjaProSettingsEnabled = SwiftNinjaPro_settings_GetOption('Enabled', 'manage_options');

$SwiftNinjaProSettingsAllowShortBots = SwiftNinjaPro_settings_GetOption('AllowShortBots', 'manage_options');

$SwiftNinjaProSettingRedirect404 = SwiftNinjaPro_settings_GetOption('Redirect404', 'manage_options');
$SwiftNinjaProSettingRedirectLogin = SwiftNinjaPro_settings_GetOption('RedirectLogin', 'manage_options');
$SwiftNinjaProSettingBlockProxy = SwiftNinjaPro_settings_GetOption('BlockProxy', 'manage_options');

$SwiftNinjaProSettingUseNewBrowserDetect = SwiftNinjaPro_settings_GetOption('useNewBrowserDetect', 'manage_options');
$SwiftNinjaProSettingBrowserDetectAlgorithm = SwiftNinjaPro_settings_GetOption('BrowserDetectAlgorithm', 'manage_options');

$SwiftNinjaProSettingIPEnabled = SwiftNinjaPro_settings_GetOption('IPEnabled', 'manage_options');
$SwiftNinjaProSettingIPUseAdmin = SwiftNinjaPro_settings_GetOption('IPUseAdmin', 'manage_options');
$SwiftNinjaProSettingIPList = SwiftNinjaPro_settings_GetOption('IPList', 'manage_options');

$SwiftNinjaProSettingBREnabled = SwiftNinjaPro_settings_GetOption('BREnabled', 'manage_options');
$SwiftNinjaProSettingBRUseAdmin = SwiftNinjaPro_settings_GetOption('BRUseAdmin', 'manage_options');
$SwiftNinjaProSettingBRList = SwiftNinjaPro_settings_GetOption('BRList', 'manage_options');


if(isset($_GET['UpdateOptions'])){
  echo '<script>window.location.replace("'.esc_url(get_admin_url()).'admin.php?page=swiftninjapro-'.$SwiftNinjaProSettings_PluginPermalinkName.'&settings=saved");</script>';
  exit('<h2>Successfully Saved Settings!</h2>');
}

function SwiftNinjaPro_settings_GetOption($name, $requiredPermToUpdate){
  global $SwiftNinjaProSettings_PluginName;
  $pluginName = $SwiftNinjaProSettings_PluginName;
  $sName = SwiftNinjaPro_settings_SetOption($name, $pluginName);
  $option = get_option($sName);
  if(isset($option) && ($option || $option === false || $option === '')){
    $option = esc_html($option);
  }else{$option = null;}
  if(isset($_GET['UpdateOptions'])){
    $post = esc_html($_POST[$sName]);
    if(current_user_can($requiredPermToUpdate)){update_option($sName, $post);}
    return $post;
  }else{return $option;}
}

function SwiftNinjaPro_settings_GetOption_only($name){
  global $SwiftNinjaProSettings_PluginName;
  $pluginName = $SwiftNinjaProSettings_PluginName;
  $sName = SwiftNinjaPro_settings_SetOption($name, $pluginName);
  $option = get_option($sName);
  if(isset($option) && $option){
    $option = esc_html($option);
  }else{$option = null;}
  return $option;
}

function SwiftNinjaPro_settings_SetOption($name, $pluginName){
  return esc_html('SwiftNinjaPro'.$pluginName.'_'.$name);
}

function SwiftNinjaProSettingsTrueText($text){
  if($text === 'true' || $text === 'TRUE' || $text === 'True' || $text === true || $text === 1 || $text === 'on'){
    return true;
  }else{return false;}
}


//option display functions
function SwiftNinjaProSettingAddCheckBox($setting, $option, $text, $default=false){
  global $SwiftNinjaProSettings_PluginName;
  $pluginName = $SwiftNinjaProSettings_PluginName;
  $sName = SwiftNinjaPro_settings_SetOption($option, $pluginName);
  $set;
  if($setting !== null){
    $set = SwiftNinjaProSettingsTrueText($setting);
  }else{$set = $default;}
  if($set){
    echo '<input type="checkbox" name="'.$sName.'" checked="true"><strong>'.$text.'</strong></input>';
  }else{
    echo '<input type="checkbox" name="'.$sName.'"><strong>'.$text.'</strong></input>';
  }
}

function SwiftNinjaProSettingAddList($setting, $option, $text, $default=false){
  global $SwiftNinjaProSettings_PluginName;
  $pluginName = $SwiftNinjaProSettings_PluginName;
  $sName = SwiftNinjaPro_settings_SetOption($option, $pluginName);
  $set;
  if(isset($setting) && $setting && $setting !== ''){
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

function SwiftNinjaProSettingAddSelect($setting, $option, $text, $default, $optionList, $optionNameFunction = false){
  global $SwiftNinjaProSettings_PluginName;
  $pluginName = $SwiftNinjaProSettings_PluginName;
  $sName = SwiftNinjaPro_settings_SetOption($option, $pluginName);
  $setValue;
  if($setting){
    $setValue = $setting;
  }else{$setValue = $default;}
  $result = '<strong>'.$text.' </strong>';
  $result .= '<select id="'.$option.'" name="'.$sName.'" style="border-radius: 10px;">';
  foreach($optionList as $value => $name){
    if(!$value || !is_string($value) || $value === ''){
      $value = $name;
    }else if(!$name || $name === ''){
      $name = $value;
    }
    if($optionNameFunction && is_callable($optionNameFunction)){
      $name = $optionNameFunction($name);
    }
    if($value === $setValue){
      $result .= '<option value="'.$value.'" selected>'.$name.'</option>';
    }else{
      $result .= '<option value="'.$value.'">'.$name.'</option>';
    }
  }
  $result .= '</select>';
  echo $result;
}


echo '<form action="'.esc_url(get_admin_url()).'admin.php?page=swiftninjapro-'.$SwiftNinjaProSettings_PluginPermalinkName.'&UpdateOptions" autocomplete="off" method="POST" enctype="multipart/form-data">';
echo '<input type="hidden" name="SwiftNinjaProSettingsToken" value="'.$SwiftNinjaProSettingsToken.'">';

SwiftNinjaProSettingAddCheckBox($SwiftNinjaProSettingsEnabled, 'Enabled', 'Plugin Enabled', true);
echo '<br>';
SwiftNinjaProSettingAddCheckBox($SwiftNinjaProSettingsAdminOnly, 'AdminOnly', 'Restrict Settings To Administrator');
echo '<br><br>';

SwiftNinjaProSettingAddCheckBox($SwiftNinjaProSettingsAllowShortBots, 'AllowShortBots', 'Allow quick bots on wp-login (enable if using a login plugin that requires a short redirect to wp-login)');

echo '<br><br>';
SwiftNinjaProSettingAddCheckBox($SwiftNinjaProSettingRedirect404, 'Redirect404', 'Hide message and attempt 404 redirect');
echo '<br>';
SwiftNinjaProSettingAddCheckBox($SwiftNinjaProSettingRedirectLogin, 'RedirectLogin', 'If not wp-admin url, Hide message and attempt login redirect');
echo '<br>';
SwiftNinjaProSettingAddCheckBox($SwiftNinjaProSettingBlockProxy, 'BlockProxy', 'Block Common Proxy IP\'s (may produce false positives)');

echo '<br><br>';

$SwiftNinjaProSettingBrowserDetectAlgorithmOptions = array();
for($i = 1; $i <= $swiftNinjaProFunc->browserAlgorithmLatestVersion(); $i++){
  array_push($SwiftNinjaProSettingBrowserDetectAlgorithmOptions, 'v'.$i);
}

function SwiftNinjaProSettingBrowserDetectSelectName($value){
  global $swiftNinjaProFunc;
  $browser = $swiftNinjaProFunc->getBrowserVersion($value);
  if(!$browser){
    $browser = 'Undefined';
  }
  return strtoupper($value).' (Your Browser: '.$browser.')';
}

if($SwiftNinjaProSettingBrowserDetectAlgorithm){
  SwiftNinjaProSettingAddSelect($SwiftNinjaProSettingBrowserDetectAlgorithm, 'BrowserDetectAlgorithm', 'Browser Detection Algorithm', 'v3', $SwiftNinjaProSettingBrowserDetectAlgorithmOptions, SwiftNinjaProSettingBrowserDetectSelectName);
}else if(SwiftNinjaProSettingsTrueText($SwiftNinjaProSettingUseNewBrowserDetect)){
  SwiftNinjaProSettingAddSelect('v2', 'BrowserDetectAlgorithm', 'Browser Detection Algorithm', 'v2', $SwiftNinjaProSettingBrowserDetectAlgorithmOptions, SwiftNinjaProSettingBrowserDetectSelectName);
}else{
  SwiftNinjaProSettingAddSelect('v1', 'BrowserDetectAlgorithm', 'Browser Detection Algorithm', 'v1', $SwiftNinjaProSettingBrowserDetectAlgorithmOptions, SwiftNinjaProSettingBrowserDetectSelectName);
}
echo '<br><p id="BrowserDetectAlgorithmMatchesCurrent" style="font-weight: bold; font-size: 1.2em;"></p>';

echo '<input type="submit" value="Save" class="swiftninjapro-settings-button">';


?>
<br><br><button type="button" class="collapsible" style="background: #de0000;">Notice! (Click To Read)</button>
<div class="content" style="width: 80%; min-width: 300px; padding: 10px; word-wrap: break-word;">

<h1 style="color: red;">Notice:</h1>
<h3>Make Sure You Include Your IP In This List.</h3>
<h3>If You Do Not Put Your IP In This List, You Will Not Have Access To wp-login</h3>
<h3>If You Do Get Locked Out, You can do a few different things to recover your site</h3>
<br>
<h3>Option 1:</h3>
<h4>1. on your site, go to: <a href="/admin-login-whitelist-recovery">/admin-login-whitelist-recovery</a></h4>
<h4>2. enter the page password: <strong>gHxXeVwvuz6Cez3</strong></h4>
<h4>3. follow the directions on that page</h4>
<h4>This will disable the plugins "Enabled" setting, and you can modify the settings as needed</h4>
<br>
<h3>Option 2:</h3>
<h4>Contact your host, and ask them to disable the plugin manually</h4>
<br>
<h3>Option 3:</h3>
<h4>Use FTP, Filezilla, or cpanel, then (click "File Manager", if using cpanel) navigate to public_html/wp-content/plugins, then find the folder "swiftninjapro-wp-login-whitelist-ip" and rename it to "swiftninjapro-wp-login-whitelist-ip-off" to disable the plugin</h4>

</div>
<?php


echo '<pre class="swiftninjaro-settings-pre">';
echo '<h3>Your IP: '.htmlentities($_SERVER['REMOTE_ADDR']).'</h3>';
SwiftNinjaProSettingAddCheckBox($SwiftNinjaProSettingIPEnabled, 'IPEnabled', 'IP Whitelist');
echo '<br>';
SwiftNinjaProSettingAddCheckBox($SwiftNinjaProSettingIPUseAdmin, 'IPUseAdmin', 'Also Block WP-Admin');
echo '<br><br>';
SwiftNinjaProSettingAddList($SwiftNinjaProSettingIPList, 'IPList', 'ex: 000.000.000.000');

echo '</pre>';
echo '<pre class="swiftninjaro-settings-pre">';
echo '<h3>Your Browser: '.esc_html($swiftNinjaProFunc->getBrowser()).'</h3>';
echo '<h3 id="BrowserDetectAlgorithmChangedBrowser"></h3>';
SwiftNinjaProSettingAddCheckBox($SwiftNinjaProSettingBREnabled, 'BREnabled', 'Browser Whitelist');
echo '<br>';
SwiftNinjaProSettingAddCheckBox($SwiftNinjaProSettingBRUseAdmin, 'BRUseAdmin', 'Also Block WP-Admin');
echo '<br><br>';
SwiftNinjaProSettingAddList($SwiftNinjaProSettingBRList, 'BRList', 'ex: Google-Chrome');

echo '</pre>';

echo '</form>';

?>

<script>

;(function(){
  let currentBrowser = '<?php echo esc_html($swiftNinjaProFunc->getBrowser()); ?>';
  let browserDetectVersion = document.getElementById('BrowserDetectAlgorithm');
  let browserDetectMatchNotice = document.getElementById('BrowserDetectAlgorithmMatchesCurrent');
  let changedBrowser = document.getElementById('BrowserDetectAlgorithmChangedBrowser');
  browserDetectVersion.addEventListener('change', function(){
    let optionBrowser = this.options[this.selectedIndex].text;
    if(!optionBrowser || typeof optionBrowser !== 'string'){
      optionBrowser = '';
    }else{
      optionBrowser = optionBrowser.replace(/^.*?\(.*?browser: (.*?)\).*$/i, '$1');
      if(!optionBrowser || optionBrowser === ''){
        optionBrowser = 'Undefined';
      }
    }
    if(optionBrowser !== currentBrowser){
      browserDetectMatchNotice.innerText = 'Warning: The result of Algorithm '+this.value.toUpperCase()+' does Not match your current Algorithm!';
      browserDetectMatchNotice.style['color'] = 'red';
      changedBrowser.innerText = 'Changed Browser: '+optionBrowser;
    }else{
      browserDetectMatchNotice.innerText = 'The result of Algorithm '+this.value.toUpperCase()+' matchs your current Algorithm!';
      browserDetectMatchNotice.style['color'] = 'green';
      changedBrowser.innerText = '';
    }
  }, false);
  browserDetectVersion.dispatchEvent(new Event('change'));
})();

;(function(){
  // function from w3schools.com
  let coll = document.getElementsByClassName("collapsible");
  for (let i = 0; i < coll.length; i++) {
    coll[i].addEventListener("click", function() {
      this.classList.toggle("active");
      var content = this.nextElementSibling;
      if(content.style.display === "block"){
        content.style.display = "none";
      }else{
        content.style.display = "block";
      }
    });
  }
})();

</script>

<div style="width: 80%; min-width: 300px; padding: 10px; word-wrap: break-word;">

<h1 style="color: red;">Notice:</h1>
<h3>Make Sure You Include Your IP In This List.</h3>
<h3>If You Do Not Put Your IP In This List, You Will Not Have Access To wp-login</h3>
<h3>If You Do Get Locked Out, You can do a few different things to recover your site</h3>
<br>
<h3>Option 1:</h3>
<h4>1. on your site, go to: <a href="/admin-login-whitelist-recovery">/admin-login-whitelist-recovery</a></h4>
<h4>2. enter the page password: <strong>gHxXeVwvuz6Cez3</strong></h4>
<h4>3. follow the directions on that page</h4>
<h4>This will disable the plugins "Enabled" setting, and you can modify the settings as needed</h4>
<br>
<h3>Option 2:</h3>
<h4>Contact your host, and ask them to disable the plugin manually</h4>
<br>
<h3>Option 3:</h3>
<h4>Use FTP, Filezilla, or cpanel, then (click "File Manager", if using cpanel) navigate to public_html/wp-content/plugins, then find the folder "swiftninjapro-wp-login-whitelist-ip" and rename it to "swiftninjapro-wp-login-whitelist-ip-off" to disable the plugin</h4>

</div>
