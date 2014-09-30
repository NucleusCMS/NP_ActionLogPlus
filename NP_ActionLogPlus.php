<?php
/*
 * ActionLogPlus plugin for Nucleus CMS
 * ver. 0.2.4
 * Written By Mocchi, Jan.19, 2009
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 3
 * of the License, or (at your option) any later version.
 */

class NP_ActionLogPlus extends NucleusPlugin {
 function getName() { return 'ActionLogPlus'; }
 function getAuthor() { return 'Mocchi, Shizuki'; }
 function getURL() { return 'http://japan.nucleuscms.org/wiki/plugins:actionlogplus'; }
 function getVersion() { return '0.2.4'; }
 function getMinNucleusVersion() { return 250; }
 function getDescription() { return $this->translated('Adding some actions for action log. The logs are written out and removed when clearing logs or the number of logs over 400, then the action recorded. Adding the function for downloading the logs written out.'); }
 function supportsFeature($what) {
  switch($what) {
  case 'HelpPage':
  case 'SqlTablePrefix':
   return 1;
  break;
  default:
   return 0;
  }
 }
 function init() {
  $this->iMaxSize = 400;
 }
 function install() {
  $this->createOption('ALP_MEMBER', $this->translated('Record the logs about MEMBER? (changing settings, deleting)') , 'yesno', 'yes');
  $this->createOption('ALP_BLOG', $this->translated('Record the logs about BLOG? (creating, changing settings, deleting)') , 'yesno', 'yes');
  $this->createOption('ALP_TEAM', $this->translated('Record the logs about TEAM? (changing admin, removing one)') , 'yesno', 'yes');
  $this->createOption('ALP_CATEGORY', $this->translated('Record the logs about CATEGORY? (creating, changing settings, deleting)') , 'yesno', 'yes');
  $this->createOption('ALP_ITEM', $this->translated('Record the logs about ITEM? (adding, editing, deleting)') , 'yesno', 'yes');
  $this->createOption('ALP_BANIP', $this->translated('Record the logs about BAN OF IPs? (adding, removing)') , 'yesno', 'yes');
  $this->createOption('ALP_PLUGIN', $this->translated('Record the logs about PLUGINS? (installing, changing global settings, uninstalling)') , 'yesno', 'yes');
  $this->createOption('ALP_BACKUP', $this->translated('Record the logs about BACKUP? (creating)') , 'yesno', 'yes');
  $this->createOption('ALP_GLOBAL', $this->translated('Record the logs about changing GLOBAL SETTINGS?') , 'yesno', 'yes');
  $this->createOption('ALP_MEDIA', $this->translated('Record the logs about MEDIA FILES?') , 'yesno', 'yes');
 }
 function unInstall() {
  $this->deleteOption('ALP_MEMBER');
  $this->deleteOption('ALP_BLOG');
  $this->deleteOption('ALP_TEAM');
  $this->deleteOption('ALP_CATEGORY');
  $this->deleteOption('ALP_ITEM');
  $this->deleteOption('ALP_BANIP');
  $this->deleteOption('ALP_PLUGIN');
  $this->deleteOption('ALP_BACKUP');
  $this->deleteOption('ALP_GLOBAL');
  $this->deleteOption('ALP_MEDIA');
 }
 function hasAdminArea() {
  if(file_exists($this->getDirectory() . '/') === TRUE ) {
   return 1;
  } else {
   return 0;
  }
 }
 
 function getEventList() {
  return array(
   'PostAuthentication', 'AdminPrePageFoot',
   'PrePluginOptionsUpdate', 'PostPluginOptionsUpdate',
   'PostUpdateItem', 'PostAddItem', 'PostDeleteItem', 'PostMoveItem',
   'PostAddCategory', 'PostChangeCategorySettings', 'PostDeleteCategory', 'PostMoveCategory',
   'PostAddBlog', 'PostChangeBlogSettings', 'PostDeleteBlog',
   'PostAddPlugin','PostChangePluginGlobalOptions', 'PostDeletePlugin', 
   'PostChangeMemberSettings', 'PostDeleteMember',
   'PostTeamChangeAdmin', 'PostDeleteTeamMember',
   'PostAddBan', 'PostDeleteBan',
   'Logout',
   'PreClearActionlog','PostClearActionlog',
   'BackupCreate',
   'SettingsUpdate',
   'PostMediaUpload', 'PostMediaRename', 'PostMediaErase',
   'PostSubdirCreate', 'PostSubdirRemove', 'PostSubdirRename'
   );
 }
 
 // to generate event which is not in core programs
 function event_postauthentication(&$data) {
  global $manager, $action;
  if(!$data['loggedIn']) return;
  $actions = array('backupcreate', 'clearactionlog', 'settingsupdate', 'teamchangeadmin', 'blogsettingsupdate', 'categoryupdate', 'changemembersettings');
  if(!in_array($action, $actions)) return;
  if($action === 'backupcreate') {
   $param = array('');
   $manager->notify('BackupCreate' , $param);
  }
  if($action === 'clearactionlog') {
   $param = array('');
   $manager->notify('PreClearActionlog' , $param);
  }
  if($action === 'settingsupdate') {
   $param = array('');
   $manager->notify('SettingsUpdate' , $param);
  }
  if($action === 'teamchangeadmin') {
   global $blogid, $memberid;
   if(preg_match('#^[0-9]+$#', $blogid) == 0) return;
   if(preg_match('#^[0-9]+$#', $memberid) == 0) return;
   $pmem = MEMBER::createFromID($memberid);
   $pmem->isBlogAdmin($blogid) ? $admin = '1' :$admin = '0' ;
   $param = array('blogid'=>$blogid, 'memberid'=>$memberid, 'admin'=> $admin);
   $manager->notify('PreTeamChangeAdmin' , $param);
  }
  if($action === 'blogsettingsupdate') {
   global $blogid;
   if(preg_match('#^[0-9]+$#', $blogid) == 0) return;
   $param = array('blogid'=>$blogid);
   $manager->notify('PreChangeBlogSettings' , $param);
  }
  if($action === 'categoryupdate') {
   global $catid;
   if(preg_match('#^[0-9]+$#', $catid) == 0) return;
   $param = array('catid'=>$catid);
   $manager->notify('PreChangeCategorySettings' , $param);
  }
  if($action === 'changemembersettings') {
   global $memberid;
   if(preg_match('#^[0-9]+$#', $memberid) == 0) return;
   $param = array('memberid'=>$memberid);
   $manager->notify('PreChangeMemberSettings' , $param);
  }
 }
 
 // to generate event which is not in core programs
 function event_adminprepagefoot(&$data) {
  global $manager;
  $actions = array('clearactionlog', 'teamchangeadmin');
  $action = $data['action'];
  if(!in_array($action,$actions)) return;
  if( $action === 'clearactionlog') {
   $param = array('');
   $manager->notify('PostClearActionlog' , $param);
  }
  if( $action === 'teamchangeadmin') {
   global $blogid, $memberid;
   if(preg_match('#^[0-9]+$#', $blogid) == 0) return;
   if(preg_match('#^[0-9]+$#', $memberid) == 0) return;
   $pmem = MEMBER::createFromID($memberid);
   $pmem->isBlogAdmin($blogid) ? $admin = '1' :$admin = '0' ;
   $param = array('blogid'=>$blogid, 'memberid'=>$memberid, 'admin'=> $admin);
   $manager->notify('PostTeamChangeAdmin' , $param);
  }
 }
 
 // to separate each events related plugin options
 function event_prepluginoptionsupdate(&$data) {
  global $manager;
  switch($data['context']) {
   case 'global':
    $pluginid = $data['plugid'];
    $optionname = $data['optionname'];
    $value = &$data['value'];
    $param = array('plugid'=>$pluginid, 'optionname'=>$optionname, 'value'=>&$value);
    $manager->notify('PreChangePluginGlobalOptions' , $param);
   return;
   case 'member':
    $memberid = $data['contextid'];
    $optionname = $data['optionname'];
    $value = &$data['value'];
    $param = array('memberid'=>$memberid, 'optionname'=>$optionname, 'value'=>&$value);
    $manager->notify('PreChangePluginMemberOptions' , $param);
   return;
   case 'blog':
    $blogid = $data['contextid'];
    $optionname = $data['optionname'];
    $value = &$data['value'];
    $param = array('blogid'=>$blogid, 'optionname'=>$optionname, 'value'=>&$value);
    $manager->notify('PreChangePluginBlogOptions' , $param);
   return;
   case 'category':
    $catid = $data['contextid'];
    $optionname = $data['optionname'];
    $value = &$data['value'];
    $param = array('catid'=>$catid, 'optionname'=>$optionname, 'value'=>&$value);
    $manager->notify('PreChangePluginCategoryOptions' , $param);
   return;
   case 'item':
    $itemid = $data['contextid'];
    $optionname = $data['optionname'];
    $value = &$data['value'];
    $param = array('itemid'=>$itemid, 'optionname'=>$optionname, 'value'=>&$value);
    $manager->notify('PreChangePluginItemOptions' , $param);
   return;
   default:
   return;
  }
 }
 
 // to separate each events related changing settings and plugin options
 function event_postpluginoptionsupdate(&$data) {
  global $manager;
  switch($data['context']) {
   case 'global':
    $pluginid = $data['plugid'];
    $param = array('pluginid'=>$pluginid);
    $manager->notify('PostChangePluginGlobalOptions' , $param);
   break;
   case 'member':
    $memberid = $data['memberid'];
    $member = &$data['member'];
    $param = array('memberid'=>$memberid, 'member'=> &$member);
    $manager->notify('PostChangeMemberSettings' , $param);
    $param = array('memberid'=>$memberid, 'member'=> &$member);
    $manager->notify('PostChangePluginMemberOptions' , $param);
   break;
   case 'blog':
    $blogid = $data['blogid'];
    $blog = &$data['blog'];
    $param = array('blogid'=>$blogid, 'blog'=>&$blog);
    $manager->notify('PostChangeBlogSettings' , $param);
    $param = array('blogid'=>$blogid, 'blog'=>&$blog);
    $manager->notify('PostChangePluginBlogOptions' , $param);
   break;
   case 'category':
    $catid = $data['catid'];
    $param = array('catid'=>$catid);
    $manager->notify('PostChangeCategorySettings' , $param);
    $param = array('catid'=>$catid);
    $manager->notify('PostChangePluginCategoryOptions' , $param);
   break;
   case 'item':
    $itemid = $data['itemid'];
    $item = &$data['item'];
    $param = array('itemid'=>$itemid, 'item'=>&$item);
    $manager->notify('PostChangePluginItemOptions' , $param);
   break;
   default:
   return;
  }
 }
 
 // logs about item
 function event_postadditem(&$data) {
  if(!$this->getOption('ALP_ITEM')) return;
  $itemid = $data['itemid'];
  addToLog('INFO', $this->translated('Add new item') . " (itemID:$itemid)");
  $this->checkLog();
 }
 function event_postupdateitem(&$data) {
  if(!$this->getOption('ALP_ITEM')) return;
  $itemid = $data['itemid'];
  addToLog('INFO', $this->translated('Edit item') . " (itemID:$itemid)");
  $this->checkLog();
 }
 function event_postdeleteitem(&$data) {
  if(!$this->getOption('ALP_ITEM')) return;
  $itemid = $data['itemid'];
  addToLog('INFO', $this->translated('Delete item') . " (itemID:$itemid)");
  $this->checkLog();
 }
 function event_postmoveitem(&$data) {
  if(!$this->getOption('ALP_ITEM')) return;
  $itemid = $data['itemid'];
  $destblogid = $data['destblogid'];
  $destcatid = $data['destcatid'];
  addToLog('INFO', $this->translated('Move an item to other category of weblog') . " (itemID:$itemid, new weblogID:$destblogid, new categoryID:$destcatid)");
  $this->checkLog();
 }
 
 // logs about category
 function event_postaddcategory(&$data) {
  if(!$this->getOption('ALP_CATEGORY')) return;
  $catid = $data['catid'];
  addToLog('INFO', $this->translated('Add category') . " (categoryID:$catid)");
  $this->checkLog();
 }
 function event_postchangecategorysettings(&$data) {
  if(!$this->getOption('ALP_CATEGORY')) return;
  $catid = $data['catid'];
  addToLog('INFO', $this->translated('Change settings for category') . " (categoryID:$catid)");
  $this->checkLog();
 }
 function event_postdeletecategory(&$data) {
  if(!$this->getOption('ALP_CATEGORY')) return;
  $catid = $data['catid'];
  addToLog('INFO', $this->translated('Delete category') . " (categoryID:$catid)");
  $this->checkLog();
 }
 function event_postmovecategory(&$data) {
  if(!$this->getOption('ALP_CATEGORY')) return;
  $catid = $data['catid'];
  $this->sourceblog = $data['sourceblog'];
  $this->destblog = $data['destblog'];
  addToLog('INFO', $this->translated('Move an category from/to weblog') . " (categoryID:$catid, former weblogID:$this->sourceblog->getID(), current weblogID:$this->destblog->getID())");
  $this->checkLog();
 }
 
 // logs about weblog
 function event_postaddblog(&$data) {
  if(!$this->getOption('ALP_BLOG')) return;
  $blog = $data['blog'];
  $blogid = $blog->getID();
  addToLog('INFO', $this->translated('Add new weblog') . " (weblogID:$blogid)");
  $this->checkLog();
 }
 function event_postchangeblogsettings(&$data) {
  if(!$this->getOption('ALP_BLOG')) return;
  $blogid = $data['blogid'];
  addToLog('INFO', $this->translated('Change settings for weblog') . " (weblogID:$blogid)");
  $this->checkLog();
 }
 function event_postdeleteblog(&$data) {
  if(!$this->getOption('ALP_BLOG')) return;
  $blogid = $data['blogid'];
  addToLog('INFO', $this->translated('Delete weblog') . "(weblogID:$blogid)");
  $this->checkLog();
 }
 
 // logs about plugin
 function event_postaddplugin(&$data) {
  if(!$this->getOption('ALP_PLUGIN')) return;
  $plugin = $data['plugin'];
  $pluginid = $plugin->getID();
  $pluginname = $plugin->getName();
  addToLog('INFO', $this->translated('Install plugin') . " (pluginID:$pluginid, plugin name:$pluginname)");
  $this->checkLog();
 }
 function event_postchangepluginglobaloptions(&$data) {
  if(!$this->getOption('ALP_PLUGIN')) return;
  $pluginid = $data['pluginid'];
  addToLog('INFO', $this->translated('Change plugin options for global settings') . " (pluginID:$pluginid)");
  $this->checkLog();
 }
 
 function event_postdeleteplugin(&$data) {
  if(!$this->getOption('ALP_PLUGIN')) return;
  $pluginid = $data['plugid'];
  addToLog('INFO', $this->translated('Uninstall plugin')." (pluginID:$pluginid)");
  $this->checkLog();
 }
 
 // logs about member
 function event_postchangemembersettings(&$data) {
  $memberid = $data['memberid'];
  if(!$this->getOption('ALP_MEMBER')) return;
  addToLog('INFO', $this->translated('Change settings for member') . " (memberID:$memberid)");
  $this->checkLog();
 }
 function event_postdeletemember(&$data) {
  if(!$this->getOption('ALP_MEMBER')) return;
  $oldmember = $data['member'];
  $oldmemberid = $oldmember->getID();
  $oldmembername = $oldmember->getDisplayName();
  addToLog('INFO', $this->translated('Delete member') . " (memberID:$oldmemberid, member name:$oldmembername)");
  $this->checkLog();
 }
 
 // logs about team
 function event_postteamchangeadmin(&$data) {
  if(!$this->getOption('ALP_TEAM')) return;
  $blogid = $data['blogid'];
  $memberid = $data['memberid'];
  if($data['admin']) $admin ='Team Admin';
  else $admin = 'Non Team Admin';
  addToLog('INFO', $this->translated('Change the rights for team administration') . " (weblogID:$blogid, memberID:$memberid, $admin)");
  $this->checkLog();
 }
 function event_postdeleteteammember(&$data) {
  if(!$this->getOption('ALP_TEAM')) return;
  $blogid = $data['blogid'];
  $member = $data['member'];
  $memberid = $member->getID();
  $membername = $member->getDisplayName();
  addToLog('INFO', $this->translated('Remove member from team') . " (weblogID:$blogid, memberID:$memberid, member name:$membername)");
  $this->checkLog();
 }
 
 // logs about ban IP
 function event_postaddban(&$data) {
  if(!$this->getOption('ALP_BANIP')) return;
  $blogid = $data['blogid'];
  $iprange = $data['iprange'];
  addToLog('INFO', $this->translated('Add ban of IP') . " (weblogID:$blogid, banIP:$iprange)");
  $this->checkLog();
 }
 function event_postdeleteban(&$data) {
  if(!$this->getOption('ALP_BANIP')) return;
  $blogid = $data['blogid'];
  $iprange = $data['range'];
  addToLog('INFO', $this->translated('Remove ban of IP') . " (weblogID:$blogid, banIP:$iprange)");
  $this->checkLog();
 } 

 // logs about Login
 function event_logout(&$data) {
  $name = $data['username'];
  addToLog('INFO', "[$name] " . $this->translated('Logout'));
  $this->checkLog();
 }
 
 // logs about actionlog
 var $error;
 function event_preclearactionlog() {
  $this->preserveLog();
 }
 function event_postclearactionlog() {
  if(!$this->error){
   addToLog('INFO', $this->translated('Preserve and delete action logs'));
  } else {
   addToLog('ERROR', $this->translated($this->error));
  }
 }
 
 // logs about backup
 function event_backupcreate() {
  if(!$this->getOption('ALP_BACKUP')) return;
  addToLog('INFO', $this->translated('Create backup data'));
  $this->checkLog();
 }
 
 // logs about global settings
 function event_settingsupdate(){
  if(!$this->getOption('ALP_GLOBAL')) return;
  addToLog('INFO', $this->translated('Change global settings'));
  $this->checkLog();
 }
 
 // logs about media files
 function event_postmediaupload(&$data){
  if(!$this->getOption('ALP_MEDIA')) return;
  $collection = $data['collection'];
  $filename = $data['filename'];
  addToLog('INFO', $this->translated('File Upload') . " (Collection Directory:$collection, Filename:$filename)");
 }
 function event_postmediarename(&$data){
  if(!$this->getOption('ALP_MEDIA')) return;
  $collection = $data['collection'];
  $oldfilename = $data['oldfilename'];
  $newfilename = $data['newfilename'];
  addToLog('INFO', $this->translated('File Rename') . " (Collection Directory:$collection, Old Filename:$oldfilename, New Filename:$newfilename)");
   }
 function event_postmediaerase(&$data){
  if(!$this->getOption('ALP_MEDIA')) return;
  $collection = $data['collection'];
  $filename = $data['filename'];
  addToLog('INFO', $this->translated('File Erase') . " (Collection Directory:$collection, Filename:$filename)");
 }
 
 // logs about media sub directories
 function event_postsubdircreate(&$data){
  if(!$this->getOption('ALP_MEDIA')) return;
  $collection = $data['collection'];
  $subdir = $data['subdir'];
  addToLog('INFO', $this->translated('Sub Directory Create') . " (Collection Directory:$collection, Sub Directory Name:$subdir)");
 }
 function event_postsubdirremove(&$data){
  if(!$this->getOption('ALP_MEDIA')) return;
  $collection = $data['collection'];
  $subdir = $data['subdir'];
  addToLog('INFO', $this->translated('Sub Directory Remove') . " (Collection Directory:$collection, Sub Directory Name:$subdir)");
   }
 function event_postsubdirrename(&$data){
  if(!$this->getOption('ALP_MEDIA')) return;
  $collection = $data['collection'];
  $olddirname = $data['olddirname'];
  $newdirname = $data['newdirname'];
  addToLog('INFO', $this->translated('Sub Directory Rename') . " (Collection Directory:$collection, Old Sub Directory Name:$olddirname, New Sub Directory Name:$newdirname)");
 }
 
 // methods
 function checkLog() {
  static $checked = 0;
  if ($checked) return;
  $checked = 1;
  $iTotal = quickQuery('SELECT COUNT(*) AS result FROM ' . sql_table('actionlog'));
  if ($iTotal < $this->iMaxSize  ) return;
  $this->preserveLog();
 }
 function getLogDirectory(){
  return $this->getDirectory() . 'logs/';
 }
 function preserveLog() {
  
  if(!@file_exists($this->getLogDirectory())) {
   $this->error = 'Missing the directory for logs';
   addToLog('ERROR', '[NP_ActionLogPlus] Missing the directory for logs');
   return;
  }
  if(!is_writable($this->getLogDirectory())) {
   if(!@chmod($this->getLogDirectory(), 0777)) {
    $this->error = 'Forbidden access to the directory for logs';
    addToLog('ERROR', '[NP_ActionLogPlus] Forbidden access to the directory for logs');
    return;
   }
  }
  $filename = date('Ymd-His') . '-actionlog.log';
  $actionlog = @fopen( $this->getLogDirectory() . $filename, "wb");
  if(!$actionlog) {
   $this->error = 'Fail to open file for preserving action logs';
   addToLog('ERROR', '[NP_ActionLogPlus] Fail to open file for preserving action logs');
   return;
  }
  if(!@flock($actionlog, LOCK_EX)) {
   $this->error = 'Fail to lock file for preserving action logs';
   addToLog('ERROR', '[NP_ActionLogPlus] Fail to lock file for preserving action logs');
   return;
  }
  
  $query = 'SELECT timestamp,message FROM ' . sql_table('actionlog') . ' ORDER BY timestamp DESC';
  $logData = mysql_query($query);
  
  if(!@fwrite( $actionlog, date("Y-m-d H:i:s",time()) . "\t" . '[NP_ActionLogPlus] ' . $this->translated('Auto-write out for action logs') . "\r\n" )){
   $this->error = 'Fail to keep action logs in system cash';
   addToLog('ERROR', '[NP_ActionLogPlus] Fail to keep action logs in system cash');
   return;
  }
  
  while ($record = mysql_fetch_assoc($logData)) {
   if(!@fwrite( $actionlog, $record['timestamp'] . "\t" . $record['message'] . "\r\n" )){
    $this->error = 'Fail to keep action logs in system cash';
    addToLog('ERROR', '[NP_ActionLogPlus] Fail to keep action logs in system cash');
    return;
   }
  }
  
  if(!@fclose($actionlog)) {
   $this->error = 'Fail to close file for preserving action logs';
   addToLog('ERROR', '[NP_ActionLogPlus] Fail to close file for preserving action logs');
   return;
  }
  if(!@chmod($this->getLogDirectory() . $filename, 0700)) {
   $this->error = 'Fail to change the mode of file for action logs';
   addToLog('ERROR', '[NP_ActionLogPlus] Fail to change the mode of file for action logs');
  }
  sql_query('DELETE FROM ' . sql_table('actionlog'));
  addToLog('INFO', $this->translated('Auto-write out for action logs'));
  return;
 }
 
 function translated($english){
  if (!is_array($this->langArray)) {
   $this->langArray=array();
   $language = $this->getDirectory() . 'lang/' . str_replace( array('\\','/') , array('',''), getLanguageName()).'.php';
   if (file_exists($language)) include($language);
  }
  if (!($ret=$this->langArray[$english])) $ret=$english;
  return $ret;
 }
}
