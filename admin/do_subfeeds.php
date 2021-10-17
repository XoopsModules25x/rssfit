<?php

declare(strict_types=1);
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

use Xmf\Request;
use XoopsModules\Rssfit\{
    FeedHandler,
    PluginHandler,
    Utility
};
/** @var FeedHandler $feedHandler */
/** @var PluginHandler $pluginHandler */

if (!preg_match('#/rssfit/admin/#', $_SERVER['SCRIPT_NAME'])) {
    header('Location: index.php');
}

switch ($op) {
    default:
        $ret     = '';
        $plugins = $pluginHandler->getObjects2(null, 'sublist');
        if ($plugins) {
            $ret .= "<br>\n<table cellspacing='1' class='outer' width='100%'>\n"
                    . "<tr><th colspan='4'>"
                    . _AM_RSSFIT_SUB_LIST
                    . "</th></tr>\n"
                    . "<tr>\n<td class='head' align='center'>"
                    . _AM_RSSFIT_SUB_FILENAME_URL
                    . "</td>\n"
                    . "<td class='head' align='center'>"
                    . _AM_RSSFIT_PLUGIN_MODNAME
                    . "</td>\n"
                    . "<td class='head' align='center'>"
                    . _AM_RSSFIT_SUB_ACTIVATE
                    . "</td>\n"
                    . "<td class='head' align='center'>&nbsp;</td>\n"
                    . "</tr>\n";
            foreach ($plugins as $p) {
                $id = $p->getVar('rssf_conf_id');
                if ($handler = $pluginHandler->checkPlugin($p)) {
                    $mod      = $handler->modname;
                    $activate = new \XoopsFormCheckBox('', 'activate[' . $id . ']', $p->getVar('subfeed'));
                    $config = Utility::rssfGenAnchor(RSSFIT_ADMIN_URL . '?do=' . $do . '&amp;op=edit&amp;feed=' . $id, _AM_RSSFIT_SUB_CONFIGURE);
                    $urlLink  = '<a href="' . $feedHandler->subFeedUrl($p->getVar('rssf_filename')) . '">' . $feedHandler->subFeedUrl($p->getVar('rssf_filename')) . '</a>';
                } else {
                    $pluginHandler->forceDeactivate($p);
                    $mod      = implode('<br>', $p->getErrors());
                    $activate = new \XoopsFormCheckBox('', 'activate[' . $id . ']', 0);
                    $activate->setExtra('disabled="disabled"');
                    $config  = '&nbsp;';
                    $urlLink = $feedHandler->subFeedUrl($p->getVar('rssf_filename'));
                }
                $activate->addOption(1, ' ');
                $ret .= "<tr>\n"
                        . "<td class='even'>"
                        . $p->getVar('rssf_filename')
                        . '<br>'
                        . $urlLink
                        . "</td>\n"
                        . "<td class='even' align='center'>"
                        . $mod
                        . "</td>\n"
                        . "<td class='odd' align='center'>"
                        . $activate->render()
                        . "</td>\n"
                        . "<td class='even' align='center'>"
                        . $config
                        . "</td>\n";
                $ret .= "</tr>\n";
            }
            $ret    .= "</table>\n";
            $hidden = new \XoopsFormHidden('op', 'save');
            $ret    = "<form action='"
                      . RSSFIT_ADMIN_URL
                      . "' method='post'>\n"
                      . $ret
                      . "<br><table cellspacing='1' class='outer' width='100%'><tr><td class='foot' align='center'>\n"
                      . $saveCancelTray->render()
                      . "\n"
                      . $hidden->render()
                      . "\n"
                      . $hiddenDo->render()
                      . "\n</td></tr></table></form>";
            echo $ret;
        } else {
            echo '<p><b>' . _AM_RSSFIT_PLUGIN_NONE . '</b></p>';
        }
        break;
    case 'save':
        $activate = Request::getArray('activate', null, 'POST');

        $plugins = $pluginHandler->getObjects2(null, 'sublist');
        if ($plugins) {
            $pluginHandler->modifyObjects(null, ['subfeed' => 0], false);
            if (isset($activate) && is_array($activate) && count($activate) > 0) {
                $keys     = array_keys($activate);
                $criteria = new \Criteria('rssf_conf_id', '(' . implode(',', $keys) . ')', 'IN');
                $pluginHandler->modifyObjects($criteria, ['subfeed' => 1], false);
            }
            redirect_header(RSSFIT_ADMIN_URL . '?do=' . $do, 0, _AM_RSSFIT_DBUPDATED);
        } else {
            redirect_header(RSSFIT_ADMIN_URL, 0, _AM_RSSFIT_PLUGIN_NONE);
        }
        break;
    case 'edit':
        $id = \Xmf\Request::getInt('feed', 0, 'GET');
        if (!empty($id)) {
            $sub = $pluginHandler->get($id);
            if (!$handler = $pluginHandler->checkPlugin($sub)) {
                $pluginHandler->forceDeactivate($sub);
            }
        }
        if (empty($id) || !$sub) {
            redirect_header(RSSFIT_ADMIN_URL, 0, _AM_RSSFIT_SUB_PLUGIN_NONE);
        }
        $form = new \XoopsThemeForm(sprintf(_AM_RSSFIT_SUB_EDIT, $handler->modname), 'editsub', RSSFIT_ADMIN_URL);
        $form->addElement(new \XoopsFormRadioYN(_AM_RSSFIT_SUB_ACTIVATE, 'subfeed', $sub->getVar('subfeed')));
        $form->addElement(new \XoopsFormText(_AM_RSSFIT_PLUGIN_SHOWXENTRIES, 'sub_entries', 3, 2, $sub->getVar('sub_entries')), true);

        $form->addElement(new \XoopsFormLabel('', '<b>' . _AM_RSSFIT_EDIT_CHANNEL_REQUIRED . '</b> ' . Utility::genSpecMoreInfo('req', $feedHandler)));
        $form->addElement(new \XoopsFormText('title', 'sub_title', 50, 255, $sub->getVar('sub_title', 'e')), true);
        $form->addElement(new \XoopsFormText('link', 'sub_link', 50, 255, $sub->getVar('sub_link', 'e')), true);
        $form->addElement(new \XoopsFormTextArea('description', 'sub_desc', $sub->getVar('sub_desc', 'e')), true);

        $form->addElement(new \XoopsFormLabel('', '<b>' . _AM_RSSFIT_EDIT_CHANNEL_IMAGE . '</b> ' . Utility::genSpecMoreInfo('img', $feedHandler)));
        $form->addElement(new \XoopsFormText('url', 'img_url', 50, 255, $sub->getVar('img_url', 'e')));
        $form->addElement(new \XoopsFormText('link', 'img_link', 50, 255, $sub->getVar('img_link', 'e')));
        $form->addElement(new \XoopsFormText('title', 'img_title', 50, 255, $sub->getVar('img_title', 'e')));

        $form->addElement(new \XoopsFormHidden('feed', $id));
        $form->addElement(new \XoopsFormHidden('op', 'savefeed'));
        $form->addElement($hiddenDo);
        $form->addElement($saveCancelTray);
        $form->display();
        break;
    case 'savefeed':
        $id = Request::getInt('feed', 0, 'POST');
        if (!empty($id)) {
            $sub = $pluginHandler->get($id);
            if (!$handler = $pluginHandler->checkPlugin($sub)) {
                $pluginHandler->forceDeactivate($sub);
            }
        }
        if (empty($id) || !$sub || !$handler) {
            redirect_header(RSSFIT_ADMIN_URL, 0, _AM_RSSFIT_SUB_PLUGIN_NONE);
        }

        $subfeed     = Request::getBool('subfeed', false, 'POST');
        $sub_entries = Request::getInt('sub_entries', 5, 'POST');
        $sub_title   = Request::getString('sub_title', '', 'POST');
        $sub_link    = Request::getUrl('sub_link', '', 'POST');
        $sub_desc    = Request::getString('sub_desc', '', 'POST');
        $img_url     = Request::getUrl('img_url', '', 'POST');
        $img_link    = Request::getUrl('img_link', '', 'POST');
        $img_title   = Request::getString('img_title', '', 'POST');

        $sub->setVar('subfeed', (int)$subfeed);
        $sub->setVar('sub_entries', $sub_entries);
        $sub->setVar('sub_title', $sub_title);
        $sub->setVar('sub_link', $sub_link);
        $sub->setVar('sub_desc', $sub_desc);
        $sub->setVar('img_url', $img_url);
        $sub->setVar('img_link', $img_link);
        $sub->setVar('img_title', $img_title);
        if (false !== $pluginHandler->insert($sub)) {
            redirect_header(RSSFIT_ADMIN_URL . '?do=' . $do, 0, _AM_RSSFIT_DBUPDATED);
        } else {
            echo $sub->getHtmlErrors();
        }
        break;
}
