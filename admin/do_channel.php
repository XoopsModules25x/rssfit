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
 * @author       NS Tai (aka tuff) <http://www.brandycoke.com/>
 * @author       XOOPS Development Team
 */

use Xmf\Request;
use XoopsModules\Rssfit;

if (!preg_match('#/rssfit/admin/#', $_SERVER['SCRIPT_NAME'])) {
    header('Location: index.php');
}



switch ($op) {
    default:
        $elements = $feedHandler->miscHandler->getObjects2(new \Criteria('misc_category', 'channel'), '*', 'title');
        $img      = $feedHandler->miscHandler->getObjects2(new \Criteria('misc_category', 'channelimg'), '*', 'title');
        if (!empty($elements) && !empty($img)) {
            $form = new \XoopsThemeForm(_AM_EDIT_CHANNEL, 'editchannel', RSSFIT_ADMIN_URL);
            $form->addElement(new \XoopsFormLabel('', '<b>' . _AM_EDIT_CHANNEL_REQUIRED . '</b> ' . Rssfit\Utility::genSpecMoreInfo('req', $feedHandler)));
            $form->addElement(new \XoopsFormText('title', 'ele[' . $elements['title']->getVar('misc_id') . ']', 50, 255, $elements['title']->getVar('misc_content', 'e')), true);
            $form->addElement(new \XoopsFormText('link', 'ele[' . $elements['link']->getVar('misc_id') . ']', 50, 255, $elements['link']->getVar('misc_content', 'e')), true);
            $form->addElement(new \XoopsFormTextArea('description', 'ele[' . $elements['description']->getVar('misc_id') . ']', $elements['description']->getVar('misc_content', 'e')), true);

            $form->addElement(new \XoopsFormLabel('', '<b>' . _AM_EDIT_CHANNEL_OPTIONAL . '</b> ' . Rssfit\Utility::genSpecMoreInfo('opt', $feedHandler)));
            $form->addElement(new \XoopsFormText('copyright', 'ele[' . $elements['copyright']->getVar('misc_id') . ']', 50, 255, $elements['copyright']->getVar('misc_content', 'e')));
            $form->addElement(new \XoopsFormText('managingEditor', 'ele[' . $elements['managingEditor']->getVar('misc_id') . ']', 50, 255, $elements['managingEditor']->getVar('misc_content', 'e')));
            $form->addElement(new \XoopsFormText('webMaster', 'ele[' . $elements['webMaster']->getVar('misc_id') . ']', 50, 255, $elements['webMaster']->getVar('misc_content', 'e')));
            $form->addElement(new \XoopsFormText('category', 'ele[' . $elements['category']->getVar('misc_id') . ']', 50, 255, $elements['category']->getVar('misc_content', 'e')));
            $form->addElement(new \XoopsFormText('generator', 'ele[' . $elements['generator']->getVar('misc_id') . ']', 50, 255, $elements['generator']->getVar('misc_content', 'e')));
            $form->addElement(new \XoopsFormText('docs', 'ele[' . $elements['docs']->getVar('misc_id') . ']', 50, 255, $elements['docs']->getVar('misc_content', 'e')));

            $form->addElement(new \XoopsFormLabel('', '<b>' . _AM_EDIT_CHANNEL_IMAGE . '</b> ' . Rssfit\Utility::genSpecMoreInfo('img', $feedHandler)));
            $form->addElement(new \XoopsFormText('url', 'ele[' . $img['url']->getVar('misc_id') . ']', 50, 255, $img['url']->getVar('misc_content', 'e')));
            $form->addElement(new \XoopsFormText('link', 'ele[' . $img['link']->getVar('misc_id') . ']', 50, 255, $img['link']->getVar('misc_content', 'e')));
            $form->addElement(new \XoopsFormText('title', 'ele[' . $img['title']->getVar('misc_id') . ']', 50, 255, $img['title']->getVar('misc_content', 'e')));

            $form->addElement($tray_save_cancel);
            $form->addElement($hidden_do);
            $form->addElement(new \XoopsFormHidden('op', 'save'));
            $form->display();
        } else {
            echo '<p>' . _AM_DB_RECORD_MISSING . '</p>';
        }
        break;
    case 'save':
        $ele    = Request::getArray('ele', null, 'POST');
        $ids    = array_keys($ele);
        $errors = [];
        foreach ($ids as $i) {
            $criteria = new \Criteria('misc_id', $i);
            $fields   = ['misc_content' => trim($ele[$i])];
            $err      = $feedHandler->miscHandler->modifyObjects($criteria, $fields);
            if ($err) {
                $errors[] = $err;
            }
        }
        if (count($errors) > 0) {
            foreach ($errors as $e) {
                echo $e . "<br><br>\n";
            }
        } else {
            redirect_header(RSSFIT_ADMIN_URL . '?do=' . $do, 0, _AM_DBUPDATED);
        }
        break;
}
