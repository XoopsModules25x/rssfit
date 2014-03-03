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
include 'admin_header.php';
xoops_cp_header();
?>
<img src="../images/rssfit.png" alt="RSSFit" style="float: left; margin: 0 10px 5px 0;" />
<h4 style="margin: 0;">RSSFit</h4>
<p style="margin-top: 0;">
Version <?php echo number_format($xoopsModule->getVar('version')/100, 2);?><br />
Presented by <a href="http://www.brandycoke.com/" target="_blank">Brandycoke Productions</a> <br />
Copyright &copy; 2003-2006 NS Tai (tuff)
<br clear="all" />
</p>

<h4 style="margin: 0;">License</h4>
<p style="margin-top: 0;">
This software is licensed under the CC-GNU GPL.<br />
<a href="http://creativecommons.org/licenses/GPL/2.0/" target="_blank">Commons Deed</a> |
<a href="http://www.gnu.org/copyleft/gpl.html" target="_blank">Legal Code</a>
</p>

<h4 style="margin: 0;">Who to Contact</h4>
<p style="margin-top: 0;">If you have any questions, comments or bug reports, please register and post your message on the <a href="http://www.brandycoke.com/home/modules/newbb/" target="_blank">discussion area</a>.
</p>

<h4 style="margin: 0;">Help us keep going</h4>
<p style="margin: 0;">
RSSFit is Freeware and Opensource. If you think it is useful and would like to show your appreciation, you can support us in one of the following ways:
</p>
<ul>
	<li><a href="https://www.paypal.com/xclick/business=donations%40brandycoke.com&amp;item_name=Donation+for+Brandycoke+Freewares&amp;item_number=rssfit&amp;no_note=1&amp;tax=0&amp;currency_code=USD">Donate us via PayPal</a>
	</li>
	<li><a href="http://www.brandycoke.com/about/services/">Hire us for your web development projects</a>
	</li>
</ul>

<?php
xoops_cp_footer();
?>