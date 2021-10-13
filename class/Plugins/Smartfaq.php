<?php

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
* Requirements (Tested with):
*  Module: SmartFAQ <http://www.smartfactory.ca>
*  Version: 1.04 / 1.1 dev
*  RSSFit verision: 1.2 / 1.5
*  XOOPS version: 2.0.13.2 / 2.2.3
*/

use XoopsModules\Smartfaq\Helper as SmartfaqHelper;

if (!\defined('RSSFIT_ROOT_PATH')) {
    exit();
}

/**
 * Class Smartfaq
 * @package XoopsModules\Rssfit\Plugins
 */
class Smartfaq
{
    public $dirname = 'smartfaq';
    public $modname;
    public $grab;

    /**
     * @return false|string
     */
    public function loadModule()
    {
        $mod = $GLOBALS['module_handler']->getByDirname($this->dirname);
        if (!$mod || !$mod->getVar('isactive')) {
            return false;
        }
        $this->modname = $mod->getVar('name');

        return $mod;
    }

    /**
     * @param \XoopsObject $obj
     * @return bool|array
     */
    public function grabEntries(&$obj)
    {
        $ret = false;
        //        @require_once XOOPS_ROOT_PATH . '/modules/smartfaq/include/functions.php';

        /** @var \XoopsModules\Smartfaq\FaqHandler $faqHandler */
        $faqHandler = SmartfaqHelper::getInstance()->getHandler('Faq');
        $faqs       = $faqHandler->getAllPublished($this->grab, 0);
        if (false !== $faqs && \count($faqs) > 0) {
            $ret = [];
            /** @var \XoopsModules\Smartfaq\AnswerHandler $answerHandler */
            $answerHandler = SmartfaqHelper::getInstance()->getHandler('Answer');
            for ($i = 0, $iMax = \count($faqs); $i < $iMax; ++$i) {
                if (!$answer = $answerHandler->getOfficialAnswer($faqs[$i]->faqid())) {
                    continue;
                }
                $ret[$i]['link']        = $ret[$i]['guid'] = XOOPS_URL . '/modules/smartfaq/faq.php?faqid=' . $faqs[$i]->faqid();
                $q                      = $faqs[$i]->getVar('howdoi', 'n');
                $q                      = empty($q) ? $faqs[$i]->getVar('question', 'n') : $q;
                $ret[$i]['title']       = $q;
                $ret[$i]['timestamp']   = $faqs[$i]->getVar('datesub');
                $ret[$i]['description'] = $answer->getVar('answer');
                $ret[$i]['category']    = $this->modname;
                $ret[$i]['domain']      = XOOPS_URL . '/modules/' . $this->dirname . '/';
            }
        }

        return $ret;
    }
}
