<?php
// $Id: blocks.php 1037 2012-09-07 21:19:12Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

include dirname(dirname(dirname(dirname(__FILE__)))).'/mainfile.php';
$xoopsLogger->activated = false;

set_time_limit(0);

// Get modules
$sql = "SELECT * FROM ".$xoopsDB->prefix("modules")." WHERE isactive=1";
$result = $xoopsDB->query($sql);

$urls = array();
$modNames = array();

while($row = $xoopsDB->fetchArray($result)){
    $mod = new XoopsModule();
    $mod->assignVars($row);
    
    $info = $mod->getInfo();
    
    if(!isset($info['rmversion'])) continue;
    if(!isset($info['updateurl'])) continue;
    
    $modNames[$mod->dirname()] = $info['name'];
    $v = $info['rmversion'];

    if(isset($v['major']))
        $version = $v['major'].'.'.$v['minor'].'.'.$v['revision'].'.'.$v['stage'];
    else
        $version = $v['number'].'.'.($v['revision']/10).'.'.$v['status'];
    
    $urls[$mod->dirname()] = $info['updateurl'] . (strpos($info['updateurl'], '?')===false ? '?' : '&') . 'action=check&id='.$mod->dirname().'&version='.$version;
    
}

$total = 0;
$upds = array();
foreach($urls as $dir => $url){
    $ret = file_get_contents($url);
    $ret = json_decode($ret, true);
    if($ret['message']==0) continue;
    if($ret['error']==1) continue;
    
    $ret['data']['type'] = 'module';
    $ret['data']['dir'] = $dir;
    $ret['data']['name'] = $modNames[$dir];
    $upds[] = $ret;
    
    $total++;
}

$rmFunc = RMFunctions::get();
$urls = array();
$plugNames = array();
// Check updates for plugins
$result = $xoopsDB->query("SELECT dir FROM ".$xoopsDB->prefix("mod_rmcommon_plugins"));
while($row = $xoopsDB->fetchArray($result)){
    $plugin = $rmFunc->load_plugin($row['dir']);
    if(!$plugin) continue;
    
    $info = $plugin->info();
    
    if(!isset($info['updateurl'])) continue;
    
    $plugNames[$row['dir']] = $info['name'];
    $v = $info['version'];
    
    if(!is_array($v))
        $version = '0.0.0.0';
    else
        $version = $v['major'].'.'.$v['minor'].'.'.$v['revision'].'.'.$v['stage'];

    $params = "type=plugin&verion=$version&id=$row[dir]";
    
    $urls[$row['dir']] = strpos($info['updateurl'], '?')===false ? $info['updateurl']."?$params" : $info['updateurl']."&$params";
    
}

foreach($urls as $dir => $url){
    $ret = file_get_contents($url);
    $ret = json_decode($ret, true);
    if($ret['message']==0) continue;
    if($ret['error']==1) continue;
    
    $ret['data']['type'] = 'plugin';
    $ret['data']['dir'] = $dir;
    $ret['data']['name'] = $plugNames[$dir];
    $upds[] = $ret;
    
    $total++;
}

file_put_contents(XOOPS_CACHE_PATH.'/updates.chk', base64_encode(serialize(array('date'=>time(),'total'=>$total,'updates'=>$upds))));

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');
echo json_encode(array('total'=>$total));
die();
