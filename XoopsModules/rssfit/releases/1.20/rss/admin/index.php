<?php
// $Id$
###############################################################################
##                RSSFit - Extendable XML news feed generator                ##
##                Copyright (c) 2004 - 2005 NS Tai (aka tuff)                ##
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
require 'admin_header.php';
$do = isset($_GET['do']) ? trim($_GET['do']) : '';
$do = isset($_POST['do']) ? trim($_POST['do']) : $do;
$op = isset($_GET['op']) ? trim($_GET['op']) : 'list';
$op = isset($_POST['op']) ? trim($_POST['op']) : $op;

if( file_exists(RSSFIT_ROOT_PATH.'admin/do_'.$do.'.php') ){
	include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';
	$hidden_do = new XoopsFormHidden('do', $do);
	$button_save = new XoopsFormButton('', 'submit', _AM_SAVE, 'submit');
	$button_go =& new XoopsFormButton('', 'submit', _GO, 'submit');
	$button_cancel =& new XoopsFormButton('', 'cancel', _CANCEL);
	$button_cancel->setExtra('onclick="javascript:history.go(-1)"');
	$tray_save_cancel = new XoopsFormElementTray('', '');
	$tray_save_cancel->addElement($button_save);
	$tray_save_cancel->addElement($button_cancel);
	require RSSFIT_ROOT_PATH.'admin/do_'.$do.'.php';
}else{
	rssfitAdminHeader();
}
require 'footer.php';
xoops_cp_footer();
?>