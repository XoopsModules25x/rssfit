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
* Requirements (Tested with):
*  Module: SmartFAQ <http://www.smartfactory.ca>
*  Version: 1.04 / 1.1 dev
*  RSSFit verision: 1.2 / 1.5
*  XOOPS version: 2.0.13.2 / 2.2.3
*/

use XoopsModules\Rssfit\{
    AbstractPlugin
};
use XoopsModules\Smartfaq\Helper as PluginHelper;

if (!\defined('RSSFIT_ROOT_PATH')) {
    exit();
}

/**
 * Class Smartfaq
 * @package XoopsModules\Rssfit\Plugins
 */
final class Smartfaq extends AbstractPlugin
{
    public $dirname = 'smartfaq';

    /**
     * @return \XoopsModule
     */
    public function loadModule(): ?\XoopsModule
    {
        $mod = null;
        if (\class_exists(PluginHelper::class)) {
            $this->helper  = PluginHelper::getInstance();
            $this->module  = $this->helper->getModule();
            $this->modname = $this->module->getVar('name');
            $mod           = $this->module;
            //        $this->dirname = $this->helper->getDirname();
        }

        return $mod;
    }

    /**
     * @return array
     */
    public function grabEntries(\XoopsMySQLDatabase $xoopsDB): ?array
    {
        $myts = \MyTextSanitizer::getInstance();
        $ret  = null;
        //        @require_once XOOPS_ROOT_PATH . '/modules/smartfaq/include/functions.php';

        /** @var \XoopsModules\Smartfaq\FaqHandler $faqHandler */
        $faqHandler = PluginHelper::getInstance()->getHandler('Faq');
        $faqs       = $faqHandler->getAllPublished($this->grab, 0);
        if (false !== $faqs && \count($faqs) > 0) {
            $ret = [];
            /** @var \XoopsModules\Smartfaq\AnswerHandler $answerHandler */
            $answerHandler = PluginHelper::getInstance()->getHandler('Answer');
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
