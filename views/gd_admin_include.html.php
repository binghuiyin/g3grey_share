<?php defined("SYSPATH") or die("No direct script access.");
/**
 * G3 Grey Theme - a custom theme for Gallery 3
 * This theme is designed and built by David Yin, https://www.yinfor.com
 * Copyright (C) 2023 David Yin
 *
 * Based on the Grey Dragon Theme, which was designed and built by Serguei Dosyukov,
 * whose blog you will find at http://blog.dragonsoft.us/
 * Copyright (C) 2012 Serguei Dosyukov

 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General
 * Public License as published by the Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License
 * for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program; if not, write to
 * the Free Software Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 */
?>
<style>   
  @import "<?php echo url::file("modules/g3grey_share/css/gd_common.css"); ?>";
</style>

<script>
  $(document).ready( function() { 
    $('form').submit( function() { $('input[type=submit]', this).attr('disabled', 'disabled'); });
    
    var objAutoUpdate = $('#g-autoupdate-config');
    var objAutoHidden = $('input[name="g_auto_delay"]');
    var objAutoEdit   = $('#g-auto-delay-edit');
    function showSidebar(){ objAutoEdit.val(objAutoHidden.val());	objAutoUpdate.slideDown('fast', function() { objAutoUpdate.addClass('visible'); }); };
    function hideSidebar(){ objAutoHidden.val(objAutoEdit.val());	objAutoUpdate.slideUp('fast',   function() { objAutoUpdate.removeClass('visible'); }); };

		objAutoEdit.keyup( function() { objAutoHidden.val($(this).val());	});
		objAutoEdit.keydown(function(event) {       
			// Allow: backspace, delete, tab and escape 
      if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 ||  
        // Allow: Ctrl+A 
        (event.keyCode == 65 && event.ctrlKey === true) ||  
        // Allow: home, end, left, right 
        (event.keyCode >= 35 && event.keyCode <= 39)) { 
          // let it happen, don't do anything 
          return; 
      } else { 
        // Ensure that it is a number and stop the keypress 
        if ((event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) { 
          event.preventDefault();  
        }    
      } 
    }); 
 
    $('.g-link-autoupdate').click(function(e){ e.preventDefault(); if ( objAutoUpdate.hasClass('visible') ){ hideSidebar(); } else { showSidebar(); }});
  });
</script>

<?php

function isCurlInstalled() {
	if (in_array('curl', get_loaded_extensions())) {
		return true;
	}	else {
		return false;
	}
} 

// -1 - cannot get version info
// 0 - current
// + - newer is avaialble, version is returned
function checkVersionInfo($downloadid, $version) {
  if (!isset($downloadid)):
    return -1;
  endif;

  try {                                                                                        
    $call = "http://blog.dragonsoft.us/downloadversion/" . $downloadid; 
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $call);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
    $output=curl_exec($ch);
    $json = json_decode($output);
  
    if ($json->id == $downloadid):
      $newversion = $json->version;
      if ($json->version > $version):
        return $json->version;
      else:
        return 0;
      endif;      
    else:
      return -1;
    endif;
  } catch (Exception $e) {
    return -1;
  }
}

if ($is_module):
  $admin_info = new ArrayObject(parse_ini_file(MODPATH   . $name . "/module.info"), ArrayObject::ARRAY_AS_PROPS);
  $version = number_format($admin_info->version / 10, 1, '.', '');
  $lastupdate = module::get_var($name, "last_update", time());
	$checkInDays = module::get_var($name, "auto_delay", 30);
else:
  $admin_info = new ArrayObject(parse_ini_file(THEMEPATH . $name . "/theme.info"), ArrayObject::ARRAY_AS_PROPS);
  $version = $admin_info->version;
  $lastupdate = module::get_var("th_" . $name, "last_update", time());
	$checkInDays = module::get_var("th_" . $name, "auto_delay", 30);
endif;

if (isCurlInstalled() && ($checkInDays > 0) && ((time() - $lastupdate) > ($checkInDays * 24 * 60 * 60))): // Check version every N days
  $admin_info2 = new ArrayObject(parse_ini_file(MODPATH   . "g3grey_share/module.info"), ArrayObject::ARRAY_AS_PROPS);
  $version2 = number_format($admin_info2->version / 10, 1, '.', '');
  
  $versionCheck  = checkVersionInfo($downloadid, $version);
  $versionCheck2 = checkVersionInfo(15, $version2);
  
  if (($versionCheck == 0) && ($versionCheck2 == 0)):
    if ($is_module):
      module::set_var($name, "last_update", time());
    else:
      module::set_var("th_" . $name, "last_update", time());
    endif;
  endif;
else:
  $versionCheck = 0;
  $versionCheck2 = 0;
endif;
?>

<div id="gd-admin-header">
  <div id="gd-admin-title"><?php echo t($admin_info->name) ?> - <?php echo $version ?></div>
  <div id="gd-admin-hlinks">
    <ul style="float: right;"><li><a href="http://blog.dragonsoft.us/gallery-3/" target="_blank"><?php echo t("Home") ?></a>&nbsp;|&nbsp;</li>
      <?php if (isset($admin_info->discuss_url)): ?>
      <li><a href="<?php echo $admin_info->discuss_url;  ?>" target="_blank"><?php echo t("Support") ?></a>&nbsp;|&nbsp;</li>
      <?php endif; ?>
      <?php if (isset($admin_info->info_url)): ?>
	      <li><a href="<?php echo $admin_info->info_url; ?>" target="_blank"><?php echo t("Download") ?></a>&nbsp;|&nbsp;</li>
      <?php endif; ?>
      <?php if (isset($admin_info->vote)): ?>
	      <li><a href="<?php echo $admin_info->vote;     ?>" target="_blank"><?php echo t("Vote") ?></a>&nbsp;|&nbsp;</li>
      <?php endif; ?>
      <li><a href="http://twitter.com/greydragon_th" target="_blank" title="<?php echo t("Follow Us on Twitter") ?>"><?php echo t("Follow Us") ?></a>&nbsp;|&nbsp;</li>
      <li><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=9MWBSVJMWMJEU" target="_blank" ><?php echo t("Coffee Fund") ?></a>&nbsp;|&nbsp;</li>
      <li><a href="#" class="g-link-autoupdate" <?php echo (isCurlInstalled())? null : "disabled=\"disabled\""; ?> ><?php echo t("Auto Update"); ?></a></li>
    </ul>
  </div>
</div>
<div id="g-autoupdate-config">
  <ul><li><?php echo t("Check every"); ?>&nbsp;&nbsp;</li>
    <li><input id="g-auto-delay-edit" type="text" size="2" value="30"></li>
    <li><?=t("days (set to 0 to disable)"); ?></li>
<?php if (($versionCheck == 0) && ($versionCheck2 == 0)): ?>
    <li>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo t("Last check:"); ?>&nbsp;<?php echo date("Y-m-d H:i:s", $lastupdate); ?></li>
<?php endif; ?>  
  </ul>
</div>

<?php if ($versionCheck == -1): ?>
  <div id="gd-admin-version"><?php echo t("Version check is incomplete. No version information has been found."); ?> <?php echo $versionCheck; ?> : <?php echo $downloadid; ?></div>
<?php elseif ($versionCheck == 0): ?>
<?php else: ?>
  <div id="gd-admin-version"><?php echo t("Newer version") ?> <?php echo $versionCheck; ?> <?php echo t("is available. Click Download link for more info.") ?></div>
<?php endif; ?>
<?php if (($versionCheck2 == -1) || ($versionCheck2 == 0)): ?>
<?php else: ?>
  <div id="gd-admin-version-2"><?php echo t("Newer version") ?> <?php echo $versionCheck2; ?> <?php echo t("of GreyDragon Shared Module is available. Click") . ' <a href="http://codex.gallery2.org/Gallery3:Modules:greydragon" target="_blank">' . t("here") . '</a> ' . t("for more info.") ?></div>
<?php endif; ?>
<div id="g-admin-container">
<?php if (isset($help)): ?>
	<div class="column1">
	  <?php echo $form ?>
	</div>
	<div class="column2">
    <?php echo $help ?>
	</div>
<?php else: ?>
  <?php echo $form ?>
<?php endif; ?>
</div>
