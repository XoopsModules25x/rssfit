<?php

declare(strict_types=1);

namespace XoopsModules\Rssfit\Plugins;

/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    XOOPS Project (https://xoops.org)
 * @license      GNU GPL 2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package      RSSFit - Extendable XML news feed generator
 * @author       NS Tai (aka tuff) <http://www.brandycoke.com>
 * @author       XOOPS Development Team
 */

/*
* About this RSSFit plug-in
* Author: tuff <http://www.brandycoke.com>
* Requirements:
* Requirements (Tested with):
*  Module: SmartPartner <http://www.smartfactory.ca>
*  Version: 1.02
*  RSSFit verision: 1.2 / 1.5
*  XOOPS version: 2.0.13.2 / 2.2.3
*/

use XoopsModules\Rssfit\{
    AbstractPlugin
};
use XoopsModules\Smartpartner\{
    Constants,
    Helper as PluginHelper,
    PartnerHandler
};

if (!\defined('RSSFIT_ROOT_PATH')) {
    exit();
}

/**
 * Class Smartpartner
 * @package XoopsModules\Rssfit\Plugins
 */
final class Smartpartner extends AbstractPlugin
{
    public function __construct() {
        if (\class_exists(PluginHelper::class)) {
            $this->helper = PluginHelper::getInstance();
            $this->dirname = $this->helper->dirname();
        }
    }


    public function grabEntries(\XoopsMySQLDatabase $xoopsDB): ?array
    {
        $myts = \MyTextSanitizer::getInstance();
        $ret  = null;
        require_once XOOPS_ROOT_PATH . '/modules/smartpartner/include/common.php';
        /** @var PartnerHandler $partnerHandler */
        $partnerHandler = PluginHelper::getInstance()->getHandler('Partner');
        $partners       = $partnerHandler->getPartners($this->grab, 0, Constants::SPARTNER_STATUS_ACTIVE, 'weight', 'DESC');
        if (\is_array($partners) && \count($partners) > 0) {
            $ret = [];
            for ($i = 0, $iMax = \count($partners); $i < $iMax; ++$i) {
                $ret[$i]['link']        = $ret[$i]['guid'] = SMARTPARTNER_URL . 'partner.php?id=' . $partners[$i]->getVar('id');
                $ret[$i]['title']       = $partners[$i]->getVar('title', 'n');
                $ret[$i]['description'] = $partners[$i]->getVar('summary');
                $ret[$i]['category']    = $this->modname;
                $ret[$i]['domain']      = XOOPS_URL . '/modules/' . $this->dirname . '/';
            }
        }

        return $ret;
    }
}
