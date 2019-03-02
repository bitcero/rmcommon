<?php
/**
 * Common Utilities
 * A framework for new XOOPS modules
 *
 * Copyright © 2015 Eduardo Cortés
 * -----------------------------------------------------------------
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * -----------------------------------------------------------------
 * @package      rmcommon
 * @author       Eduardo Cortés
 * @copyright    Eduardo Cortés
 * @license      GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @link         http://eduardocortes.mx
 * @link         http://rmcommon.com
 */

/**
 * This file allows to load updates information from the remote server
 */

include dirname(dirname(dirname(__DIR__))) . '/mainfile.php';
$xoopsLogger->activated = false;

set_time_limit(0);

// Get modules
$sql = "SELECT * FROM ".$xoopsDB->prefix("modules")." WHERE isactive=1";
$result = $xoopsDB->query($sql);

$urls = array();
$modNames = array();

/**
 * Load existing modules and fetch data to request remote server information
 */
while ($row = $xoopsDB->fetchArray($result)) {
    $mod = new XoopsModule();
    $mod->assignVars($row);
    
    $info =& $mod->getInfo();
    
    if (!isset($info['rmversion'])) {
        continue;
    }
    if (!isset($info['updateurl'])) {
        continue;
    }
    
    $modNames[$mod->dirname()] = $info['name'];
    $v = $info['rmversion'];

    if (isset($v['major'])) {
        $version = $v['major'].'.'.$v['minor'].'.'.$v['revision'].'.'.$v['stage'];
    } else {
        $version = $v['number'].'.'.($v['revision']/10).'.'.$v['status'];
    }
    
    $urls[$mod->dirname()] = $info['updateurl'] . (strpos($info['updateurl'], '?')===false ? '?' : '&') . 'action=check&id='.$mod->dirname().'&version='.$version;
}

/**
 * Load remote information and fetch it
 * for each module
 */
$total = 0;
$upds = array();
foreach ($urls as $dir => $url) {
    $ret = file_get_contents($url . '&xv=' . urlencode(XOOPS_VERSION));
    $ret = json_decode($ret, true);
    if ($ret['message']==0) {
        continue;
    }
    if ($ret['type']=='error') {
        continue;
    }
    
    $ret['data']['type'] = 'module';
    $ret['data']['dir'] = $dir;
    $ret['data']['name'] = $modNames[$dir];
    $upds[] = $ret;
    
    $total++;
}


/**
 * Now load all installed plugins to search for updates
 */
$rmFunc = RMFunctions::get();
$urls = array();
$plugNames = array();
// Check updates for plugins
$result = $xoopsDB->query("SELECT dir FROM ".$xoopsDB->prefix("mod_rmcommon_plugins"));
while ($row = $xoopsDB->fetchArray($result)) {
    $plugin = $rmFunc->load_plugin($row['dir']);
    if (!$plugin) {
        continue;
    }
    
    $info = $plugin->info();
    
    if (!isset($info['updateurl'])) {
        continue;
    }
    
    $plugNames[$row['dir']] = $info['name'];
    $v = $info['version'];
    
    if (!is_array($v)) {
        $version = '0.0.0.0';
    } else {
        $version = $v['major'].'.'.$v['minor'].'.'.$v['revision'].'.'.$v['stage'];
    }

    $params = "action=check&type=plugin&version=$version&id=$row[dir]";
    
    $urls[$row['dir']] = strpos($info['updateurl'], '?')===false ? $info['updateurl']."?$params" : $info['updateurl']."&$params";
}

/**
 * Load information from remote server
 * and fetch it for ach plugin
 */
foreach ($urls as $dir => $url) {
    $ret = file_get_contents($url);
    $ret = json_decode($ret, true);
    if ($ret['message']==0) {
        continue;
    }
    if ($ret['type']=='error') {
        continue;
    }
    
    $ret['data']['type'] = 'plugin';
    $ret['data']['dir'] = $dir;
    $ret['data']['name'] = $plugNames[$dir];
    $upds[] = $ret;
    
    $total++;
}

/**
 * Check themes updates
 * Event must return an array with 'dir' => 'url' values
 */
$urls = $common->events()->trigger('rmcommon.check.updates.themes', []);
foreach ($urls as $dir => $data) {
    $ret = file_get_contents($data['url']);
    $ret = json_decode($ret, true);
    if ($ret['message']==0) {
        continue;
    }
    if ($ret['type']=='error') {
        continue;
    }

    $ret['data']['type'] = 'theme';
    $ret['data']['dir'] = $dir;
    $ret['data']['name'] = $data['name'];
    $upds[] = $ret;

    $total++;
}

// Write file with updates information
file_put_contents(XOOPS_CACHE_PATH.'/updates.chk', base64_encode(serialize(array('date'=>time(),'total'=>$total,'updates'=>$upds))));

\Common\Core\Helpers\Licensing::getInstance()->checkRemote();

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');
echo json_encode(array('total'=>$total));
die();
