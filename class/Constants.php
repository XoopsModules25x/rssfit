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
 * @author       XOOPS Development Team
 */

/**
 * interface Constants
 */
interface Constants
{
    /**#@+
     * Constant definition
     */

    public const DISALLOW = 0;
    /**
     * no delay XOOPS redirect delay (in seconds)
     */
    public const REDIRECT_DELAY_NONE = 0;
    /**
     * short XOOPS redirect delay (in seconds)
     */
    public const REDIRECT_DELAY_SHORT = 1;
    /**
     * medium XOOPS redirect delay (in seconds)
     */
    public const REDIRECT_DELAY_MEDIUM = 3;
    /**
     * long XOOPS redirect delay (in seconds)
     */
    public const REDIRECT_DELAY_LONG = 7;
    /**#@-*/
}
