<?php
###############################################################################
##                RSSFit - Extendable XML news feed generator                ##
##                Copyright (c) 2004 - 2006 NS Tai (aka tuff)                ##
##                       <http://www.brandycoke.com/>                        ##
###############################################################################
##                    XOOPS - PHP Content Management System                  ##
##                       Copyright (c) 2000 XOOPS.org                        ##
##                          <http://www.xoops.org/>                          ##
###############################################################################
##  This program is free software; you can redistribute it and/or modify     ##
##  it under the terms of the GNU General Public License as published by     ##
##  the Free Software Foundation; either version 2 of the License, or        ##
##  (at your option) any later version.                                      ##
##                                                                           ##
##  You may not change or alter any portion of this comment or credits       ##
##  of supporting developers from this source code or any supporting         ##
##  source code which is considered copyrighted (c) material of the          ##
##  original comment or credit authors.                                      ##
##                                                                           ##
##  This program is distributed in the hope that it will be useful,          ##
##  but WITHOUT ANY WARRANTY; without even the implied warranty of           ##
##  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            ##
##  GNU General Public License for more details.                             ##
##                                                                           ##
##  You should have received a copy of the GNU General Public License        ##
##  along with this program; if not, write to the Free Software              ##
##  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA ##
###############################################################################
##  Author of this file: NS Tai (aka tuff)                                   ##
##  URL: http://www.brandycoke.com/                                          ##
##  Project: RSSFit                                                          ##
###############################################################################

use Xmf\Request;

if (!preg_match('#/rss/admin/#', $_SERVER['PHP_SELF'])) {
    header('Location: index.php');
}

switch ($op) {
    default:
        $ret = '';
        if ($plugins =& $plugins_handler->getObjects(null, 'sublist')) {
            $ret .= "<br />\n<table cellspacing='1' class='outer' width='100%'>\n"
                . "<tr><th colspan='4'>" . _AM_SUB_LIST . "</th></tr>\n"
                . "<tr>\n<td class='head' align='center'>" . _AM_SUB_FILENAME_URL . "</td>\n"
                . "<td class='head' align='center'>" . _AM_PLUGIN_MODNAME . "</td>\n"
                . "<td class='head' align='center'>" . _AM_SUB_ACTIVATE . "</td>\n"
                . "<td class='head' align='center'>&nbsp;</td>\n"
                . "</tr>\n";
            foreach ($plugins as $p) {
                $id = $p->getVar('rssf_conf_id');
                if (!$handler =& $plugins_handler->checkPlugin($p)) {
                    $plugins_handler->forceDeactivate($p);
                    $mod = implode('<br />', $p->getErrors());
                    $activate = new XoopsFormCheckbox('', 'activate[' . $id . ']', 0);
                    $activate->setExtra('disabled="disabled"');
                    $config = '&nbsp;';
                } else {
                    $mod = $handler->modname;
                    $activate = new XoopsFormCheckbox('', 'activate[' . $id . ']', $p->getVar('subfeed'));
                    $config = rssfGenAnchor(RSSFIT_ADMIN_URL . '?do=' . $do . '&amp;op=edit&amp;feed=' . $id, _AM_SUB_CONFIGURE);
                }
                $activate->addOption(1, ' ');
                $ret .= "<tr>\n"
                    . "<td class='even'>"
                    . $p->getVar('rssf_filename') . '<br />'
                    . $rss->subFeedUrl($p->getVar('rssf_filename'))
                    . "</td>\n"
                    . "<td class='even' align='center'>"
                    . $mod . "</td>\n"
                    . "<td class='odd' align='center'>"
                    . $activate->render() . "</td>\n"
                    . "<td class='even' align='center'>"
                    . $config . "</td>\n";
                $ret .= "</tr>\n";
            }
            $ret .= "</table>\n";
            $hidden = new XoopsFormHidden('op', 'save');
            $ret = "<form action='" . RSSFIT_ADMIN_URL . "' method='post'>\n" . $ret
                . "<br /><table cellspacing='1' class='outer' width='100%'><tr><td class='foot' align='center'>\n"
                . $tray_save_cancel->render() . "\n" . $hidden->render() . "\n"
                . $hidden_do->render() . "\n</td></tr></table></form>";
            echo $ret;
        } else {
            echo '<p><b>' . _AM_PLUGIN_NONE . '</b></p>';
        }
        break;
    case 'save':
        $activate = Request::getArray('activate', null, 'POST');

        if ($plugins = $plugins_handler->getObjects(null, 'sublist')) {
            $plugins_handler->modifyObjects(null, array('subfeed' => 0));
            if (isset($activate) && is_array($activate) && count($activate) > 0) {
                $keys = array_keys($activate);
                $criteria = new Criteria('rssf_conf_id', '(' . implode(',', $keys) . ')', 'IN');
                $plugins_handler->modifyObjects($criteria, array('subfeed' => 1));
            }
            redirect_header(RSSFIT_ADMIN_URL . '?do=' . $do, 0, _AM_DBUPDATED);
        } else {
            redirect_header(RSSFIT_ADMIN_URL, 0, _AM_PLUGIN_NONE);
        }
        break;
    case 'edit':
        $id = isset($_GET['feed']) ? (int)$_GET['feed'] : 0;
        if (!empty($id)) {
            $sub =& $plugins_handler->get($id);
            if (!$handler =& $plugins_handler->checkPlugin($sub)) {
                $plugins_handler->forceDeactivate($sub);
            }
        }
        if (empty($id) || !$sub) {
            redirect_header(RSSFIT_ADMIN_URL, 0, _AM_SUB_PLUGIN_NONE);
        }
        $form = new XoopsThemeForm(sprintf(_AM_SUB_EDIT, $handler->modname), 'editsub', RSSFIT_ADMIN_URL);
        $form->addElement(new XoopsFormRadioYN(_AM_SUB_ACTIVATE, 'subfeed', $sub->getVar('subfeed')));
        $form->addElement(new XoopsFormText(_AM_PLUGIN_SHOWXENTRIES, 'sub_entries', 3, 2, $sub->getVar('sub_entries')), true);

        $form->addElement(new XoopsFormLabel('', '<b>' . _AM_EDIT_CHANNEL_REQUIRED . '</b> ' . genSpecMoreInfo('req', $rss)));
        $form->addElement(new XoopsFormText('title', 'sub_title', 50, 255, $sub->getVar('sub_title', 'e')), true);
        $form->addElement(new XoopsFormText('link', 'sub_link', 50, 255, $sub->getVar('sub_link', 'e')), true);
        $form->addElement(new XoopsFormTextArea('description', 'sub_desc', $sub->getVar('sub_desc', 'e')), true);

        $form->addElement(new XoopsFormLabel('', '<b>' . _AM_EDIT_CHANNEL_IMAGE . '</b> ' . genSpecMoreInfo('img', $rss)));
        $form->addElement(new XoopsFormText('url', 'img_url', 50, 255, $sub->getVar('img_url', 'e')));
        $form->addElement(new XoopsFormText('link', 'img_link', 50, 255, $sub->getVar('img_link', 'e')));
        $form->addElement(new XoopsFormText('title', 'img_title', 50, 255, $sub->getVar('img_title', 'e')));

        $form->addElement(new XoopsFormHidden('feed', $id));
        $form->addElement(new XoopsFormHidden('op', 'savefeed'));
        $form->addElement($hidden_do);
        $form->addElement($tray_save_cancel);
        $form->display();
        break;
    case 'savefeed':
        $id = Request::getInt('feed', 0, 'POST');
        if (!empty($id)) {
            $sub = $plugins_handler->get($id);
            if (!$handler = $plugins_handler->checkPlugin($sub)) {
                $plugins_handler->forceDeactivate($sub);
            }
        }
        if (empty($id) || !$sub || !$handler) {
            redirect_header(RSSFIT_ADMIN_URL, 0, _AM_SUB_PLUGIN_NONE);
        }

        $subfeed = Request::getBool('subfeed', false, 'POST');
        $sub_entries = Request::getInt('sub_entries', 5, 'POST');
        $sub_title = Request::getString('sub_title', '', 'POST');
        $sub_link = Request::getUrl('sub_link', '', 'POST');
        $sub_desc = Request::getString('sub_desc', '', 'POST');
        $img_url = Request::getUrl('img_url', '', 'POST');
        $img_link = Request::getUrl('img_link', '', 'POST');
        $img_title = Request::getString('img_title', '', 'POST');

        $sub->setVar('subfeed', (int) $subfeed);
        $sub->setVar('sub_entries', $sub_entries);
        $sub->setVar('sub_title', $sub_title);
        $sub->setVar('sub_link', $sub_link);
        $sub->setVar('sub_desc', $sub_desc);
        $sub->setVar('img_url', $img_url);
        $sub->setVar('img_link', $img_link);
        $sub->setVar('img_title', $img_title);
        if (false != $plugins_handler->insert($sub)) {
            redirect_header(RSSFIT_ADMIN_URL . '?do=' . $do, 0, _AM_DBUPDATED);
        } else {
            echo $sub->getHtmlErrors();
        }
        break;
}
