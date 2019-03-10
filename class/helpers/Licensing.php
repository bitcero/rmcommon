<?php
/**
 * Common Utilities Framework for XOOPS
 * More info at Eduardo Cortés Website (www.eduardocortes.mx)
 *
 * Copyright © 2017 Eduardo Cortés (http://www.eduardocortes.mx)
 * -------------------------------------------------------------
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 * -------------------------------------------------------------
 * @copyright    Eduardo Cortés (http://www.eduardocortes.mx)
 * @license      GNU GPL 2
 * @package      rmcommon
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          http://www.eduardocortes.mx
 */

namespace Common\Core\Helpers;

use Common\Core\License;

class Licensing
{
    private $siteId;

    public function __construct()
    {
        global $common;

        if ('' == $common->settings->siteId && 32 != mb_strlen($common->settings->siteId)) {
            $this->siteId = urlencode(md5(crypt(XOOPS_LICENSE_KEY . XOOPS_URL . time(), $common->settings->secretkey)));
            $common->settings()->setValue('rmcommon', 'siteId', $siteId);
        } else {
            $this->siteId = $common->settings->siteId;
        }
    }

    /**
     * Verifies element activation
     * @param $element
     * @param $type
     * @return bool
     */
    public function checkLocal($element, $type)
    {
        if ('' == $element || '' == $type) {
            return false;
        }
        $identifier = md5($type . '-' . $element);
        $license = new License($identifier);
        if ($license->isNew()) {
            return false;
        }
        if ('' == $license->data) {
            return false;
        }
        if ($license->expiration <= time()) {
            return false;
        }

        return true;
    }

    public function checkRemote()
    {
        global $common, $xoopsDB;
        $sql = 'SELECT * FROM ' . $common->db()->prefix('mod_rmcommon_licensing');
        $result = $common->db()->queryF($sql);
        while (false !== ($row = $common->db()->fetchArray($result))) {
            $license = new License();
            $license->assignVars($row);
            $this->getInfo($license);
        }
    }

    public function getInfo(License $qngn)
    {
        global $common;
        $pbzzba = $common;
        $svyr = XOOPS_ROOT_PATH;
        //if(false === isset($qngn->type)){return false;}
        switch ($qngn->type) {
            case 'module':$zbq = $pbzzba->modules()::load($qngn->element);
                if ($zbq->isNew()) {
                    return false;
                }
                if (false === ($url = $zbq->getInfo('updateurl'))) {
                    return false;
                }
                break;
            case 'plugin':$cyhtva = $pbzzba->plugins()->load($qngn->element);
                if ($cyhtva->isNew()) {
                    return false;
                }
                if (false === ($url = $cyhtva->get_info('updateurl'))) {
                    return false;
                }
                break;
            case 'theme':
                if (false === ($url = $pbzzba->events()->trigger('rmcommon.theme.update.url', false, $qngn->element))) {
                    return false;
                }
                break;
        }
        $response = $pbzzba->httpRequest()::load_url($url, 'action=verify&type=' . $qngn->type . '&id=' . $qngn->element . '&data=' . $qngn->data);
        if ('8c0735ff' != $response) {
            $qngn->data = '';
            $qngn->save();
        }
    }

    /**
     * Singleton
     * @return Licensing
     */
    public static function getInstance()
    {
        static $instance;

        if (!isset($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}
