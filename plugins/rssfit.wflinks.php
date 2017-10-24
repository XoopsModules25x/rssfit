<?php
/*
* About this RSSFit plug-in
*
* Author :
*   DuGris - http://www.dugris.info
*
* Requirements:
*   Module : RSSFit  - http://www.brandycoke.com
*   verision : 1.20
*
*   Module : wflinks - http://www.wf-projects.com
*   Version : 1.03
*/

if (!defined('RSSFIT_ROOT_PATH')) {
    exit();
}

/**
 * Class RssfitWflinks
 */
class RssfitWflinks
{
    public $dirname = 'wflinks';
    public $modname;
    public $mid;
    public $grab;

    public function loadModule()
    {
        global $module_handler;
        $mod = $module_handler->getByDirname($this->dirname);
        if (!$mod || !$mod->getVar('isactive')) {
            return false;
        }
        $this->modname = $mod->getVar('name');
        $this->mid = $mod->getVar('mid');
        return $mod;
    }

    public function grabEntries(&$obj)
    {
        global $xoopsDB, $xoopsUser;

        $groups = is_object($xoopsUser) ? $xoopsUser -> getGroups() : XOOPS_GROUP_ANONYMOUS;
        $gperm_handler = &xoops_getHandler('groupperm');

        $myts = MyTextSanitizer::getInstance();
        $ret = [];
        $i = 0;
        $sql = 'SELECT lid, cid, title, date, description FROM ' . $xoopsDB->prefix('wflinks_links') . ' WHERE status>0 ORDER BY date DESC';
        $result = $xoopsDB->query($sql, $this->grab, 0);
        while ($row = $xoopsDB->fetchArray($result)) {
            if ($gperm_handler -> checkRight('WFLinkCatPerm', $row['cid'], $groups, $this->mid)) {
                //	required
                $ret[$i]['title'] = $row['title'];
                $ret[$i]['link'] = $ret[$i]['guid'] = XOOPS_URL.'/modules/'.$this->dirname.'/singlelink.php?cid='.$row['cid'].'&lid='.$row['lid'];
                $ret[$i]['timestamp'] = $row['date'];
                $ret[$i]['description'] = $row['description'];
                //	optional
                $ret[$i]['category'] = $this->modname;
                $ret[$i]['domain'] = XOOPS_URL.'/modules/'.$this->dirname.'/';
                $i++;
            }
        }
        return $ret;
    }
}
