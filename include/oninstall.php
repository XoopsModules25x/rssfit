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
 * @copyright     {@link https://xoops.org/ XOOPS Project}
 * @license       {@link http://www.gnu.org/licenses/gpl-2.0.html GNU GPL 2 or later}
 * @package
 * @since
 * @author        XOOPS Development Team
 */

use Xoopsmodules\rssfit;

/**
 *
 * Prepares system prior to attempting to install module
 * @param \XoopsModule $module {@link XoopsModule}
 *
 * @return bool true if ready to install, false if not
 */
function xoops_module_pre_install_rssfit(\XoopsModule $module)
{

    include __DIR__ . '/common.php';
    /** @var rssfit\Utility $utility */
    $utility      = new \Xoopsmodules\rssfit\Utility();
    //check for minimum XOOPS version
    $xoopsSuccess = $utility::checkVerXoops($module);
    
    // check for minimum PHP version
    $phpSuccess   = $utility::checkVerPhp($module);

    if (false !== $xoopsSuccess && false !== $phpSuccess) {
        $moduleTables = $module->getInfo('tables');
        foreach ($moduleTables as $table) {
            $GLOBALS['xoopsDB']->queryF('DROP TABLE IF EXISTS ' . $GLOBALS['xoopsDB']->prefix($table) . ';');
        }
    }

    return $xoopsSuccess && $phpSuccess;
}

/**
 *
 * Performs tasks required during installation of the module
 * @param XoopsModule $module {@link XoopsModule}
 *
 * @return bool true if installation successful, false if not
 */
function xoops_module_install_rssfit(\XoopsModule $module)
{
    global $xoopsConfig;
//    include __DIR__ . '/../preloads/autoloader.php';
    include __DIR__ . '/common.php';
    require_once __DIR__ . '/../../../mainfile.php';
    require_once __DIR__ . '/../include/config.php';

    $moduleDirName = basename(dirname(__DIR__));

/** @var rssfit\Helper $helper */
    /** @var rssfit\Utility $utility */
   /** @var rssfit\Configurator $configurator */
    $helper       = rssfit\Helper::getInstance();
    $utility      = new rssfit\Utility();
    $configurator = new rssfit\common\Configurator();
    // Load language files
    $helper->loadLanguage('admin');
    $helper->loadLanguage('modinfo');
    //    $helper->loadLanguage('install');

    rssfit\Utility::rssfInstallLangFile($module, $xoopsConfig['language']);

    $intro_setting = ['dohtml' => 1, 'dobr' => 1, 'sub' => stripslashes(_INSTALL_INTRO_SUB)];
    $sql[]         = 'INSERT INTO `'
                     . $GLOBALS['xoopsDB']->prefix($helper->getDirname() . '_misc')
                     . '` VALUES (1, '
                     . $GLOBALS['xoopsDB']->quoteString('intro')
                     . ', '
                     . $GLOBALS['xoopsDB']->quoteString(stripslashes(_INTRO_TITLE))
                     . ', '
                     . $GLOBALS['xoopsDB']->quoteString(stripslashes(_INTRO_CONTENT))
                     . ', '
                     . $GLOBALS['xoopsDB']->quoteString(serialize($intro_setting))
                     . ')';
    $sql[]         = rssfit\Utility::rssfInsertChannel($module);
    $sql[]         = 'INSERT INTO ' . $GLOBALS['xoopsDB']->prefix($helper->getDirname() . '_misc') . ' VALUES ' . "('', 'sticky', '', '', " . $GLOBALS['xoopsDB']->quoteString(serialize(['dohtml' => 0, 'dobr' => 0, 'feeds' => [0 => '0'], 'link' => XOOPS_URL])) . ')';
    foreach ($sql as $s) {
        if (false === $GLOBALS['xoopsDB']->query($s)) {
            echo '<span style="color: #ff0000;"><b>' . $GLOBALS['xoopsDB']->error() . '<b></span><br>' . $s . '<br><br>';
            return false;
        }
    }


    // default Permission Settings ----------------------

    $moduleId     = $module->getVar('mid');
    $moduleId2    = $helper->getModule()->mid();
    $gpermHandler = xoops_getHandler('groupperm');
    // access rights ------------------------------------------
    $gpermHandler->addRight($moduleDirName . '_approve', 1, XOOPS_GROUP_ADMIN, $moduleId);
    $gpermHandler->addRight($moduleDirName . '_submit', 1, XOOPS_GROUP_ADMIN, $moduleId);
    $gpermHandler->addRight($moduleDirName . '_view', 1, XOOPS_GROUP_ADMIN, $moduleId);
    $gpermHandler->addRight($moduleDirName . '_view', 1, XOOPS_GROUP_USERS, $moduleId);
    $gpermHandler->addRight($moduleDirName . '_view', 1, XOOPS_GROUP_ANONYMOUS, $moduleId);

    //  ---  CREATE FOLDERS ---------------
    if (count($configurator->uploadFolders) > 0) {
        //    foreach (array_keys($GLOBALS['uploadFolders']) as $i) {
        foreach (array_keys($configurator->uploadFolders) as $i) {
            $utility::createFolder($configurator->uploadFolders[$i]);
        }
    }
    //  ---  COPY blank.png FILES ---------------
    if (count($configurator->copyBlankFiles) > 0) {
        $file = __DIR__ . '/../assets/images/blank.png';
        foreach (array_keys($configurator->copyBlankFiles) as $i) {
            $dest = $configurator->copyBlankFiles[$i] . '/blank.png';
            $utility::copyFile($file, $dest);
        }
    }
    
        //  ---  COPY test folder files ---------------
    if (count($configurator->copyTestFolders) > 0) {
        //        $file = __DIR__ . '/../testdata/images/';
        foreach (array_keys($configurator->copyTestFolders) as $i) {
            $src  = $configurator->copyTestFolders[$i][0];
            $dest = $configurator->copyTestFolders[$i][1];
            $utility::recurseCopy($src, $dest);
        }
    }
    
    //delete .html entries from the tpl table
    $sql = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix('tplfile') . " WHERE `tpl_module` = '" . $module->getVar('dirname', 'n') . "' AND `tpl_file` LIKE '%.html%'";
    $GLOBALS['xoopsDB']->queryF($sql);

    return true;
}
