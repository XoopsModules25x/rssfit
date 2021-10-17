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
 * Class Misc
 * @package XoopsModules\Rssfit
 */
class Misc extends \XoopsObject
{
    public function __construct()
    {
        parent::__construct();
        //  key, data_type, value, req, max, opt
        $this->initVar('misc_id', \XOBJ_DTYPE_INT, null, false);
        $this->initVar('misc_category', \XOBJ_DTYPE_TXTBOX, '', true, 15);
        $this->initVar('misc_title', \XOBJ_DTYPE_TXTBOX, '', false, 255);
        $this->initVar('misc_content', \XOBJ_DTYPE_TXTAREA, '', false);
        $this->initVar('misc_setting', \XOBJ_DTYPE_ARRAY, '');
    }

    /**
     * @param bool $do
     */
    public function setDoHtml($do = true): void
    {
        $this->vars['dohtml']['value'] = $do;
    }

    /**
     * @param bool $do
     */
    public function setDoBr($do = true): void
    {
        $this->vars['dobr']['value'] = $do;
    }
}
