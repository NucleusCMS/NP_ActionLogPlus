<?php
/*
 * ActionLogPlus plugin for Nucleus CMS
 * ver. 0.2.3
 * Written By Mocchi, Jan. 23, 2008
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 3
 * of the License, or (at your option) any later version.
 */

include($strRel . '../../../config.php');
if (!$member->isLoggedIn()) doError('You should log in.');
include($DIR_LIBS . 'PLUGINADMIN.php');

$oPluginAdmin = new PluginAdmin('ActionLogPlus');
$actionlogplus = $manager->getPlugin('NP_ActionLogPlus');

$mode = htmlspecialchars(requestVar('mode'), ENT_QUOTES, _CHARSET);
$modes = array('download', 'view');
if(!in_array($mode, $modes)) $mode = 'display';

switch($mode) {
 case 'download':
  downloadLog();
 break;
 case 'display':
 default:
  display($mode);
 break;
}

// display
function display() {
global $CONF, $oPluginAdmin, $actionlogplus;
 $list = getLogList();
 
 $oPluginAdmin->start();
 
 echo "<h2>Action Log Manager: </h2>\n";
 echo "<h3>ActionLog Plugin for Nucleus CMS</h3>\n";
 echo "<h4>" . $actionlogplus->translated('Directory for Action Logs') . "</h4>\n";
 echo "<p>" . $CONF['PluginURL'] . $actionlogplus->getShortName() . "/logs/</p>\n";
 if($list) {
  echo "<h4>" . $actionlogplus->translated('Action Log List') . "</h4>\n";
  echo '<table frame="void" rules="none" summary="Log List">'."\n";
  echo "<thead>\n";
  echo "<tr>\n";
  echo "<th>" . $actionlogplus->translated('Log Date') . "</th>\n";
  echo "<th>" . $actionlogplus->translated('Log Time') . "</th>\n";
  echo "<th>" . $actionlogplus->translated('Function') . "</th>\n";
  echo "</tr>\n";
  echo "</thead>\n";
  foreach($list as $filename ) {
   $element = explode( '-' , $filename );
   $date = preg_replace("/^(\d{4})(\d{2})(\d{2})$/", "$1/$2/$3"  , $element[0] );
   $time = preg_replace("/^(\d{2})(\d{2})(\d{2})$/", "$1:$2:$3"  , $element[1] );
   echo "<tr>\n";
   echo "<td>" . $date . "</td>\n";
   echo "<td>" . $time ."</td>\n";
   echo "<td>\n";
   echo '<form action="' . $CONF['PluginURL'] . $actionlogplus->getShortName() . '/index.php" method="post" >' . "\n";
   echo "<p style=\"margin:0px; padding:0px;\">\n";
   echo '<input type="hidden" name="mode" value="download" />' . "\n";
   echo '<input type="hidden" name="date" value="' . $element[0] . '" />' . "\n";
   echo '<input type="hidden" name="time" value="' . $element[1] . '" />' . "\n";
   echo '<input type="submit" name="submit" value="' . $actionlogplus->translated('Download') . '" />' . "\n";
   echo "</p>\n";
   echo '</form>' . "\n";
   echo "</td>\n";
   echo "</tr>\n";
  }
  echo '</table>';
 }
 $oPluginAdmin->end();
}

function downloadLog() {
 global $actionlogplus;
 
 $filename = getFilename();
 if(!preg_match("/^\d{8}\-\d{6}\-actionlog.log$/", $filename )) {
  log_doError($filename);
 } else {
  header("Content-type: text/plain; name=$filename");
  header("Content-Disposition: attachment; filename=$filename");
  $log = @readfile($actionlogplus->getLogDirectory() . $filename);
 exit;
 }
}

function getFilename() {
 global $DIR_PLUGINS, $actionlogplus;
 
 if (strtoupper($_SERVER['REQUEST_METHOD'])!='POST') {
  display();
  return;
 }
 
 $date = intRequestVar('date');
 $time = intRequestVar('time');
 if(!preg_match("/^\d{8}$/" , $date)) {
  return 'Forbidden Access';
 }
 if(!preg_match("/^\d{6}$/" , $time)) {
  return 'Forbidden Access';
 }
 
 $filename = $date . '-' . $time . '-actionlog.log';
 if(!@chmod($actionlogplus->getLogDirectory() . $filename, 0777)) {
  return 'Fail to open log file: ' . $actionlogplus->getLogDirectory() . $filename;
 }
 if(!file_exists($actionlogplus->getLogDirectory() . $filename)) {
  return 'Log file does not exist: ' . $actionlogplus->getLogDirectory() . $filename;
 }
 if(!@chmod($actionlogplus->getLogDirectory() . $filename, 0700)) {
  return 'Fail to close log file: ' . $actionlogplus->getLogDirectory() . $filename;
 }
 return $filename;
}

function getLogList() {
 global $actionlogplus;
 $filelist = array();

 if (!is_dir($actionlogplus->getLogDirectory())) return $filelist;
 $dirhandle = opendir($actionlogplus->getLogDirectory());

 while ($filename = readdir($dirhandle)) {
  if(preg_match("/^\d{8}\-\d{6}\-actionlog.log$/", $filename )) array_push($filelist, $filename);
 }
 closedir($dirhandle);
 
 rsort($filelist);
 return $filelist;
}

function log_doError($error){
 header("Content-type: text/plain; name=error");
 header("Content-Disposition: attachment; filename=error");
 echo $error;
 exit;
}
?>