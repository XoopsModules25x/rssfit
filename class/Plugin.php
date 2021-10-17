<?php

declare(strict_types=1);

namespace XoopsModules\Rssfit;

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
if (!\defined('RSSFIT_ROOT_PATH')) {
    exit();
}

/**
 * Class Plugin
 * @package XoopsModules\Rssfit
 */
class Plugin extends \XoopsObject
{
    public function __construct()
    {
        parent::__construct();
        //  key, data_type, value, req, max, opt
        $this->initVar('rssf_conf_id', \XOBJ_DTYPE_INT, 0);
        $this->initVar('rssf_filename', \XOBJ_DTYPE_TXTBOX, '');
        $this->initVar('rssf_activated', \XOBJ_DTYPE_INT, 0);
        $this->initVar('rssf_grab', \XOBJ_DTYPE_INT, 0, true);
        $this->initVar('rssf_order', \XOBJ_DTYPE_INT, 0);
        $this->initVar('subfeed', \XOBJ_DTYPE_INT, 0);
        $this->initVar('sub_entries', \XOBJ_DTYPE_INT, 0);
        $this->initVar('sub_link', \XOBJ_DTYPE_TXTBOX, '');
        $this->initVar('sub_title', \XOBJ_DTYPE_TXTBOX, '');
        $this->initVar('sub_desc', \XOBJ_DTYPE_TXTBOX, '');
        $this->initVar('img_url', \XOBJ_DTYPE_TXTBOX, '');
        $this->initVar('img_link', \XOBJ_DTYPE_TXTBOX, '');
        $this->initVar('img_title', \XOBJ_DTYPE_TXTBOX, '');
    }
}
