<?php
// $Id$
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

include_once dirname(__FILE__) . '/admin_header.php';

//if( !defined("RSSFIT_OK") ){
//	header('Location: index.php');
//}

switch($op){
default:
	$ret = '';
//	rssfitAdminHeader();
	// activated plugins
	$criteria = new Criteria('rssf_activated', 1);
	if( $plugins =& $plugins_handler->getObjects($criteria, 'p_activated') ){
		$ret .= "<table cellspacing='1' class='outer' width='100%'>\n"
			."<tr><th colspan='5'>"._AM_PLUGIN_ACTIVATED."</th></tr>\n"
			."<tr>\n<td class='head' align='center' width='30%'>"._AM_PLUGIN_FILENAME."</td>\n"
			."<td class='head' align='center'>"._AM_PLUGIN_MODNAME."</td>\n"
			."<td class='head' align='center'>"._AM_PLUGIN_SHOWXENTRIES."</td>\n"
			."<td class='head' align='center'>"._AM_PLUGIN_ORDER."</td>\n"
			."<td class='head' align='center' width='20%'>"._AM_ACTION."</td>\n"
			."</tr>\n";
		foreach( $plugins as $p ){
			if( $handler =& $plugins_handler->checkPlugin($p) ){
				$id = $p->getVar('rssf_conf_id');
				$entries = new XoopsFormText('', 'rssf_grab['.$id.']', 3, 2, $p->getVar('rssf_grab'));
				$order = new XoopsFormText('', 'rssf_order['.$id.']', 3, 2, $p->getVar('rssf_order'));
				$action = new XoopsFormSelect('', 'action['.$id.']', '');
				$action->addOption('', _SELECT);
				$action->addOption('d', _AM_PLUGIN_DEACTIVATE);
				$action->addOption('u', _AM_PLUGIN_UNINSTALL);
				$ret .= "<tr>\n"
					."<td class='odd' align='center'>"
						.$p->getVar('rssf_filename')."</td>\n"
					."<td class='even' align='center'>"
						.$handler->modname."</td>\n"
					."<td class='odd' align='center'>"
						.$entries->render()."</td>\n"
					."<td class='odd' align='center'>"
						.$order->render()."</td>\n"
					."<td class='odd' align='center'>"
						.$action->render()."</td>\n"
					;
				$ret .= "</tr>\n";
			}else{
				$plugins_handler->forceDeactivate($p);
			}
		}
		$ret .= "</table>\n";
	}

	// inactive plugins
	if( $plugins =& $plugins_handler->getObjects(new Criteria('rssf_activated', 0), 'p_inactive') ){
		$ret .= "<br />\n<table cellspacing='1' class='outer' width='100%'>\n"
			."<tr><th colspan='3'>"._AM_PLUGIN_INACTIVE."</th></tr>\n"
			."<tr>\n<td class='head' align='center' width='30%'>"._AM_PLUGIN_FILENAME."</td>\n"
			."<td class='head' align='center'>"._AM_PLUGIN_MODNAME."</td>\n"
			."<td class='head' align='center' width='20%'>"._AM_ACTION."</td>\n"
			."</tr>\n";
		foreach( $plugins as $p ){
			$id = $p->getVar('rssf_conf_id');
			$action = new XoopsFormSelect('', 'action['.$id.']', '');
			$action->addOption('', _SELECT);
			$ret .= "<tr>\n"
				."<td class='odd' align='center'>"
					.$p->getVar('rssf_filename')."</td>\n"
				."<td class='even' align='center'>";
			if( $handler =& $plugins_handler->checkPlugin($p) ){
				$ret .= $handler->modname;
				$action->addOption('a', _AM_PLUGIN_ACTIVATE);
			}else{
				if( count($p->getErrors()) > 0 ){
					$ret .= '<b>'._ERRORS."</b>\n";
					foreach( $p->getErrors() as $e ){
						$ret .= '<br />'.$e;
					}
				}else{
					$ret .= '<b>'._AM_PLUGIN_UNKNOWNERROR."</b>";
				}
			}
			$ret .= "</td>\n";
			$action->addOption('u', _AM_PLUGIN_UNINSTALL);
			$ret .= "<td class='odd' align='center'>"
				.$action->render()."</td>\n";
		}
		$ret .= "</table>\n";
	}

	// Non-installed plugins
	if( !$filelist =& $plugins_handler->getPluginFileList() ){
		$filelist = array();
	}
	$list =& XoopsLists::getFileListAsArray(RSSFIT_ROOT_PATH.'plugins');
	$installable = array();
	foreach( $list as $f ){
		if( preg_match('/rssfit\.+[a-zA-Z0-9_]+\.php/', $f) && !in_array($f, $filelist) ){
			$installable[] = $f;
		}
	}
	if( count($installable) > 0 ){
		$ret .= "<br />\n<table cellspacing='1' class='outer' width='100%'>\n"
			."<tr><th colspan='3'>"._AM_PLUGIN_NONINSTALLED."</th></tr>\n"
			."<tr>\n<td class='head' align='center' width='30%'>"._AM_PLUGIN_FILENAME."</td>\n"
			."<td class='head' align='center'>"._AM_PLUGIN_MODNAME."</td>\n"
			."<td class='head' align='center' width='20%'>"._AM_PLUGIN_INSTALL."</td>\n"
			."</tr>\n";
		foreach( $installable as $i ){
			$action = new XoopsFormCheckbox('', 'install['.$i.']');
			$action->addOption('i', ' ');
			$ret .= "<tr>\n"
				."<td class='odd' align='center'>"
					.$i."</td>\n"
				."<td class='even' align='center'>";
			$p =& $plugins_handler->create();
			$p->setVar('rssf_filename', $i);
			if( $handler =& $plugins_handler->checkPlugin($p) ){
				$ret .= $handler->modname;
			}else{
				if( count($p->getErrors()) > 0 ){
					$ret .= '<b>'._ERRORS."</b>\n";
					foreach( $p->getErrors() as $e ){
						$ret .= '<br />'.$e;
					}
				}else{
					$ret .= '<b>'._AM_PLUGIN_UNKNOWNERROR."</b>";
				}
				$action->setExtra('disabled="disabled"');
			}
			$ret .= "</td>\n";
			$ret .= "<td class='odd' align='center'>"
				.$action->render()."</td>\n";
		}
		$ret .= "</table>\n";
	}

	if( !empty($ret) ){
		$hidden = new XoopsFormHidden('op', 'save');
		$ret = "<form action='".RSSFIT_ADMIN_URL."' method='post'>\n".$ret
				."<br /><table cellspacing='1' class='outer' width='100%'><tr><td class='foot' align='center'>\n"
				.$tray_save_cancel->render()."\n".$hidden->render()."\n"
				.$hidden_do->render()."\n</td></tr></table></form>"
				;
		echo $ret;
	}
break;
case 'save':
	extract($_POST);
	$err = '';
	if( isset($action) ){
	$keys = array_keys($action);
		foreach( $keys as $k ){
			$plugin =& $plugins_handler->get($k);
			if( isset($rssf_grab[$k]) ){
				$plugin->setVar('rssf_grab', $rssf_grab[$k]);
				$plugin->setVar('rssf_order', $rssf_order[$k]);
			}
			switch($action[$k]){
			default:
				$result = $plugins_handler->insert($plugin);
			break;

			case 'u':	// uninstall
				$result = $plugins_handler->delete($plugin);
			break;

			case 'd':	// deactivate
				$plugin->setVar('rssf_activated', 0);
				$result = $plugins_handler->insert($plugin);
			break;

			case 'a':	// activate
				$plugin->setVar('rssf_activated', 1);
				$result = $plugins_handler->insert($plugin);
			break;
			}
			if( !$result ){
				$err .= $plugin->getHtmlErrors();
			}
		}
	}
	if( !empty($install) ){
		$files = array_keys($install);
		foreach( $files as $f ){
			$p =& $plugins_handler->create();
			$p->setVar('rssf_filename', $f);
			if( $handler =& $plugins_handler->checkPlugin($p) ){
				$p->setVar('rssf_activated', 1);
				$p->setVar('rssf_grab', $xoopsModuleConfig['plugin_entries']);
				$p->setVar('sub_entries', $xoopsModuleConfig['plugin_entries']);
				$p->setVar('sub_link', XOOPS_URL.'/modules/'.$handler->dirname);
				$p->setVar('sub_title', $xoopsConfig['sitename'].' - '.$handler->modname);
				$p->setVar('sub_desc', $xoopsConfig['slogan']);
				if( !$result = $plugins_handler->insert($p) ){
					$err .= $p->getHtmlErrors();
				}
			}
		}
	}
	if( !empty($err) ){
//		rssfitAdminHeader();
		echo $err;
	}else{
		redirect_header(RSSFIT_ADMIN_URL.'?do='.$do, 0, _AM_DBUPDATED);
	}
break;
}

include_once 'admin_footer.php';
