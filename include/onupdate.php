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
 * @copyright    {@link https://xoops.org/ XOOPS Project}
 * @license      {@link http://www.gnu.org/licenses/gpl-2.0.html GNU GPL 2 or later}
 * @package
 * @since
 * @author       XOOPS Development Team
 */

use Xoopsmodules\rssfit;

if ((!defined('XOOPS_ROOT_PATH')) || !($GLOBALS['xoopsUser'] instanceof XoopsUser)
    || !$GLOBALS['xoopsUser']->IsAdmin()) {
    exit('Restricted access' . PHP_EOL);
}

/**
 * @param string $tablename
 *
 * @return bool
 */
function tableExists($tablename)
{
    $result = $GLOBALS['xoopsDB']->queryF("SHOW TABLES LIKE '$tablename'");

    return ($GLOBALS['xoopsDB']->getRowsNum($result) > 0) ? true : false;
}

/**
 *
 * Prepares system prior to attempting to install module
 * @param \XoopsModule $xoopsModule
 * @param null         $previousVersion
 * @return bool true if ready to install, false if not
 */
function xoops_module_pre_update_rssfit(\XoopsModule $xoopsModule, $previousVersion = null)
{
    $moduleDirName = basename(dirname(__DIR__));
    /** @var rssfit\Helper $helper */
    /** @var rssfit\Utility $utility */
    $helper  = rssfit\Helper::getInstance();
    $utility = new rssfit\Utility();

    $xoopsSuccess = $utility::checkVerXoops($module);
    $phpSuccess   = $utility::checkVerPhp($module);
    return $xoopsSuccess && $phpSuccess;
}

/**
 *
 * Performs tasks required during update of the module
 * @param \XoopsModule $module {@link XoopsModule}
 * @param null         $previousVersion
 *
 * @return bool true if update successful, false if not
 */

function xoops_module_update_rssfit(\XoopsModule $module, $previousVersion = null)
{
    $moduleDirName = basename(dirname(__DIR__));
    $capsDirName   = strtoupper($moduleDirName);

    /** @var rssfit\Helper $helper */
    /** @var rssfit\Utility $utility */
    /** @var rssfit\common\Configurator $configurator */
    $helper       = rssfit\Helper::getInstance();
    $utility      = new rssfit\Utility();
    $configurator = new rssfit\common\Configurator();

    $helper->loadLanguage('install');

    global $xoopsDB, $xoopsConfig;
    //    rssfInstallLangFile($xoopsMod, $xoopsConfig['language']);
    list($rows) = $xoopsDB->fetchRow($xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix($helper->getDirname() . '_misc') . " WHERE misc_category = 'channel'"));
    if (!$rows) {
        $sql[]         = 'ALTER TABLE `' . $xoopsDB->prefix($helper->getDirname() . '_misc') . '` ADD `misc_setting` TEXT NOT NULL;';
        $sql[]         = 'ALTER TABLE `' . $xoopsDB->prefix($helper->getDirname() . '_misc') . '` CHANGE `misc_category` `misc_category` VARCHAR( 30 ) NOT NULL;';
        $intro_setting = ['dohtml' => 1, 'dobr' => 1, 'sub' => _INSTALL_INTRO_SUB];
        $sql[]         = 'UPDATE `' . $xoopsDB->prefix($helper->getDirname() . '_misc') . '` SET misc_setting = ' . $xoopsDB->quoteString(serialize($intro_setting)) . " WHERE misc_category = 'intro'";
        $sql[]         = 'ALTER TABLE `'
                         . $xoopsDB->prefix($helper->getDirname() . '_plugins')
                         . "` ADD `subfeed` TINYINT( 1 ) DEFAULT '0' NOT NULL, ADD `sub_entries` VARCHAR( 2 ) NOT NULL, ADD `sub_link` VARCHAR( 255 ) NOT NULL, ADD `sub_title` VARCHAR( 255 ) NOT NULL, ADD `sub_desc` VARCHAR( 255 ) NOT NULL, ADD `img_url` VARCHAR( 255 ) NOT NULL, ADD `img_link` VARCHAR( 255 ) NOT NULL, ADD `img_title` VARCHAR( 255 ) NOT NULL;";
        $sql[]         = 'UPDATE `' . $xoopsDB->prefix($helper->getDirname() . '_plugins') . '` SET sub_entries = 5';
        $sql[]         = rssfInsertChannel($xoopsMod);
        $sql[]         = 'INSERT INTO ' . $xoopsDB->prefix($helper->getDirname() . '_misc') . ' VALUES ' . "('', 'sticky', '', '', " . $xoopsDB->quoteString(serialize(['dohtml' => 0, 'dobr' => 0, 'feeds' => [0 => '0'], 'link' => XOOPS_URL])) . ')';
        foreach ($sql as $s) {
            if (false === $xoopsDB->query($s)) {
                echo '<span style="color: #ff0000;"><b>' . $xoopsDB->error() . '<b></span><br>' . $s . '<br><br>';
                return false;
            }
        }
    }

    if ($previousVersion < 132) {


        //delete old HTML templates
        if (count($configurator->templateFolders) > 0) {
            foreach ($configurator->templateFolders as $folder) {
                $templateFolder = $GLOBALS['xoops']->path('modules/' . $moduleDirName . $folder);
                if (is_dir($templateFolder)) {
                    $templateList = array_diff(scandir($templateFolder, SCANDIR_SORT_NONE), ['..', '.']);
                    foreach ($templateList as $k => $v) {
                        $fileInfo = new SplFileInfo($templateFolder . $v);
                        if ('html' === $fileInfo->getExtension() && 'index.html' !== $fileInfo->getFilename()) {
                            if (file_exists($templateFolder . $v)) {
                                unlink($templateFolder . $v);
                            }
                        }
                    }
                }
            }
        }

        //  ---  DELETE OLD FILES ---------------
        if (count($configurator->oldFiles) > 0) {
            //    foreach (array_keys($GLOBALS['uploadFolders']) as $i) {
            foreach (array_keys($configurator->oldFiles) as $i) {
                $tempFile = $GLOBALS['xoops']->path('modules/' . $moduleDirName . $configurator->oldFiles[$i]);
                if (is_file($tempFile)) {
                    unlink($tempFile);
                }
            }
        }

        //  ---  DELETE OLD FOLDERS ---------------
        xoops_load('XoopsFile');
        if (count($configurator->oldFolders) > 0) {
            //    foreach (array_keys($GLOBALS['uploadFolders']) as $i) {
            foreach (array_keys($configurator->oldFolders) as $i) {
                $tempFolder = $GLOBALS['xoops']->path('modules/' . $moduleDirName . $configurator->oldFolders[$i]);
                /* @var $folderHandler XoopsObjectHandler */
                $folderHandler = XoopsFile::getHandler('folder', $tempFolder);
                $folderHandler->delete($tempFolder);
            }
        }

        //  ---  CREATE FOLDERS ---------------
        if (count($configurator->uploadFolders) > 0) {
            //    foreach (array_keys($GLOBALS['uploadFolders']) as $i) {
            foreach (array_keys($configurator->uploadFolders) as $i) {
                $utilityClass::createFolder($configurator->uploadFolders[$i]);
            }
        }

        //  ---  COPY blank.png FILES ---------------
        if (count($configurator->blankFiles) > 0) {
            $file = __DIR__ . '/../assets/images/blank.png';
            foreach (array_keys($configurator->blankFiles) as $i) {
                $dest = $configurator->blankFiles[$i] . '/blank.png';
                $utilityClass::copyFile($file, $dest);
            }
        }

        //delete .html entries from the tpl table
        $sql = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix('tplfile') . " WHERE `tpl_module` = '" . $module->getVar('dirname', 'n') . '\' AND `tpl_file` LIKE \'%.html%\'';
        $GLOBALS['xoopsDB']->queryF($sql);

        /** @var XoopsGroupPermHandler $gpermHandler */
        $gpermHandler = xoops_getHandler('groupperm');
        return $gpermHandler->deleteByModule($module->getVar('mid'), 'item_read');

    }
    return true;
}

