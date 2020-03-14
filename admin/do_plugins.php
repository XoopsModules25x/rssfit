<?php
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
 * @copyright    XOOPS Project https://xoops.org/
 * @license      GNU GPL 2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package      RSSFit - Extendable XML news feed generator
 * @author       NS Tai (aka tuff) <http://www.brandycoke.com>
 * @author       XOOPS Development Team
 */
use Xmf\Request;
use XoopsModules\Rssfit;

if (!preg_match('#/rssfit/admin/#', $_SERVER['SCRIPT_NAME'])) {
    header('Location: index.php');
}

$helper = Rssfit\Helper::getInstance();

switch ($op) {
    default:
        $ret = '';
        // activated plugins
        $criteria = new \Criteria('rssf_activated', 1);
        $plugins = $pluginsHandler->getObjects2($criteria, 'p_activated');
        if ($plugins) {
            $ret .= "<table cellspacing='1' class='outer' width='100%'>\n"
                    . "<tr><th colspan='5'>"
                    . _AM_PLUGIN_ACTIVATED
                    . "</th></tr>\n"
                    . "<tr>\n<td class='head' align='center' width='30%'>"
                    . _AM_PLUGIN_FILENAME
                    . "</td>\n"
                    . "<td class='head' align='center'>"
                    . _AM_PLUGIN_MODNAME
                    . "</td>\n"
                    . "<td class='head' align='center'>"
                    . _AM_PLUGIN_SHOWXENTRIES
                    . "</td>\n"
                    . "<td class='head' align='center'>"
                    . _AM_PLUGIN_ORDER
                    . "</td>\n"
                    . "<td class='head' align='center' width='20%'>"
                    . _AM_ACTION
                    . "</td>\n"
                    . "</tr>\n";
            foreach ($plugins as $p) {
                $handler = $pluginsHandler->checkPlugin($p);
                if ($handler) {
                    $id = $p->getVar('rssf_conf_id');
                    $entries = new \XoopsFormText('', 'rssf_grab[' . $id . ']', 3, 2, $p->getVar('rssf_grab'));
                    $order = new \XoopsFormText('', 'rssf_order[' . $id . ']', 3, 2, $p->getVar('rssf_order'));
                    $action = new \XoopsFormSelect('', 'action[' . $id . ']', '');
                    $action->addOption('', _SELECT);
                    $action->addOption('d', _AM_PLUGIN_DEACTIVATE);
                    $action->addOption('u', _AM_PLUGIN_UNINSTALL);
                    $ret .= "<tr>\n"
                            . "<td class='odd' align='center'>"
                            . $p->getVar('rssf_filename')
                            . "</td>\n"
                            . "<td class='even' align='center'>"
                            . $handler->modname
                            . "</td>\n"
                            . "<td class='odd' align='center'>"
                            . $entries->render()
                            . "</td>\n"
                            . "<td class='odd' align='center'>"
                            . $order->render()
                            . "</td>\n"
                            . "<td class='odd' align='center'>"
                            . $action->render()
                            . "</td>\n";
                    $ret .= "</tr>\n";
                } else {
                    $pluginsHandler->forceDeactivate($p);
                }
            }
            $ret .= "</table>\n";
        }

        // inactive plugins
        $plugins = $pluginsHandler->getObjects2(new \Criteria('rssf_activated', 0), 'p_inactive');
        if ($plugins) {
            $ret .= "<br>\n<table cellspacing='1' class='outer' width='100%'>\n"
                    . "<tr><th colspan='3'>"
                    . _AM_PLUGIN_INACTIVE
                    . "</th></tr>\n"
                    . "<tr>\n<td class='head' align='center' width='30%'>"
                    . _AM_PLUGIN_FILENAME
                    . "</td>\n"
                    . "<td class='head' align='center'>"
                    . _AM_PLUGIN_MODNAME
                    . "</td>\n"
                    . "<td class='head' align='center' width='20%'>"
                    . _AM_ACTION
                    . "</td>\n"
                    . "</tr>\n";
            foreach ($plugins as $p) {
                $id = $p->getVar('rssf_conf_id');
                $action = new \XoopsFormSelect('', 'action[' . $id . ']', '');
                $action->addOption('', _SELECT);
                $ret .= "<tr>\n" . "<td class='odd' align='center'>" . $p->getVar('rssf_filename') . "</td>\n" . "<td class='even' align='center'>";
                $handler = $pluginsHandler->checkPlugin($p);
                if ($handler) {
                    $ret .= $handler->modname;
                    $action->addOption('a', _AM_PLUGIN_ACTIVATE);
                } elseif (count($p->getErrors()) > 0) {
                        $ret .= '<b>' . _ERRORS . "</b>\n";
                        foreach ($p->getErrors() as $e) {
                            $ret .= '<br>' . $e;
                        }
                    } else {
                        $ret .= '<b>' . _AM_PLUGIN_UNKNOWNERROR . '</b>';
                    }
                $ret .= "</td>\n";
                $action->addOption('u', _AM_PLUGIN_UNINSTALL);
                $ret .= "<td class='odd' align='center'>" . $action->render() . "</td>\n";
            }
            $ret .= "</table>\n";
        }

        // Non-installed plugins
        if (!$filelist = &$pluginsHandler->getPluginFileList()) {
            $filelist = [];
        }
        $list = \XoopsLists::getFileListAsArray(RSSFIT_ROOT_PATH . 'plugins');
        $list2 = \XoopsLists::getFileListAsArray(RSSFIT_ROOT_PATH . 'class\Plugins');
        $installable = [];
        $installable2 = [];
        foreach ($list as $f) {
            if (preg_match('/rssfit\.+[a-zA-Z0-9_]+\.php/$', $f) && !in_array($f, $filelist)) {
                $installable[] = $f;
            }
        }
        foreach ($list2 as $f) {
            if (preg_match('/[a-zA-Z0-9_]+\.php/$', ucfirst($f)) && !in_array($f, $filelist)) {
                $installable2[] = ucfirst($f);
            }
        }
        if (count($installable2) > 0) {
            $ret .= "<br>\n<table cellspacing='1' class='outer' width='100%'>\n"
                    . "<tr><th colspan='3'>"
                    . _AM_PLUGIN_NONINSTALLED
                    . "</th></tr>\n"
                    . "<tr>\n<td class='head' align='center' width='30%'>"
                    . _AM_PLUGIN_FILENAME
                    . "</td>\n"
                    . "<td class='head' align='center'>"
                    . _AM_PLUGIN_MODNAME
                    . "</td>\n"
                    . "<td class='head' align='center' width='20%'>"
                    . _AM_PLUGIN_INSTALL
                    . "</td>\n"
                    . "</tr>\n";
            foreach ($installable2 as $i) {
                $action = new \XoopsFormCheckbox('', 'install[' . $i . ']');
                $action->addOption('i', ' ');
                $ret .= "<tr>\n" . "<td class='odd' align='center'>" . $i . "</td>\n" . "<td class='even' align='center'>";
                $p = $pluginsHandler->create();
                $p->setVar('rssf_filename', $i);
                $handler = $pluginsHandler->checkPlugin($p);
                if ($handler) {
                    $ret .= $handler->modname;
                } else {
                    if (count($p->getErrors()) > 0) {
                        $ret .= '<b>' . _ERRORS . "</b>\n";
                        foreach ($p->getErrors() as $e) {
                            $ret .= '<br>' . $e;
                        }
                    } else {
                        $ret .= '<b>' . _AM_PLUGIN_UNKNOWNERROR . '</b>';
                    }
                    $action->setExtra('disabled="disabled"');
                }
                $ret .= "</td>\n";
                $ret .= "<td class='odd' align='center'>" . $action->render() . "</td>\n";
            }
            $ret .= "</table>\n";
        }

        if (!empty($ret)) {
            $hidden = new \XoopsFormHidden('op', 'save');
            $ret = "<form action='"
                      . RSSFIT_ADMIN_URL
                      . "' method='post'>\n"
                      . $ret
                      . "<br><table cellspacing='1' class='outer' width='100%'><tr><td class='foot' align='center'>\n"
                      . $tray_save_cancel->render()
                      . "\n"
                      . $hidden->render()
                      . "\n"
                      . $hidden_do->render()
                      . "\n</td></tr></table></form>";
            echo $ret;
        }
        break;
    case 'save':
        $rssf_grab = Request::getArray('rssf_grab', [], 'POST');
        $rssf_order = Request::getArray('rssf_order', [], 'POST');
        $action = Request::getArray('action', null, 'POST');
        $install = Request::getArray('install', [], 'POST');
        $err = '';
        if (isset($action)) {
            $keys = array_keys($action);
            foreach ($keys as $k) {
                $plugin = $pluginsHandler->get($k);
                if ($plugin) {
                    if (isset($rssf_grab[$k])) {
                        $plugin->setVar('rssf_grab', $rssf_grab[$k]);
                        $plugin->setVar('rssf_order', $rssf_order[$k]);
                    }
                    switch ($action[$k]) {
                        default:
                            $result = $pluginsHandler->insert($plugin);
                            break;
                        case 'u':    // uninstall
                            $result = $pluginsHandler->delete($plugin);
                            break;
                        case 'd':    // deactivate
                            $plugin->setVar('rssf_activated', 0);
                            $result = $pluginsHandler->insert($plugin);
                            break;
                        case 'a':    // activate
                            $plugin->setVar('rssf_activated', 1);
                            $result = $pluginsHandler->insert($plugin);
                            break;
                    }
                }
                if (!$result) {
                    $err .= $plugin->getHtmlErrors();
                }
            }
        }
        if (!empty($install)) {
            $files = array_keys($install);
            foreach ($files as $f) {
                $p = $pluginsHandler->create();
                $p->setVar('rssf_filename', $f);
                $handler = $pluginsHandler->checkPlugin($p);
                if ($handler) {
                    $p->setVar('rssf_activated', 1);
                    $p->setVar('rssf_grab', $helper->getConfig('plugin_entries'));
                    $p->setVar('sub_entries', $helper->getConfig('plugin_entries'));
                    $p->setVar('sub_link', XOOPS_URL . '/modules/' . $handler->dirname);
                    $p->setVar('sub_title', $xoopsConfig['sitename'] . ' - ' . $handler->modname);
                    $p->setVar('sub_desc', $xoopsConfig['slogan']);
                    if (!$result = $pluginsHandler->insert($p)) {
                        $err .= $p->getHtmlErrors();
                    }
                }
            }
        }
        if (!empty($err)) {
            echo $err;
        } else {
            redirect_header(RSSFIT_ADMIN_URL . '?do=' . $do, 0, _AM_DBUPDATED);
        }
        break;
}

require_once __DIR__ . '/admin_footer.php';
