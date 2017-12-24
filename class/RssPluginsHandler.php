<?php namespace Xoopsmodules\rssfit;

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

use Xoopsmodules\rssfit;

if (!defined('RSSFIT_ROOT_PATH')) {
    exit();
}

/**
 * Class RssPluginsHandler
 * @package Xoopsmodules\rssfit
 */
class RssPluginsHandler extends \XoopsObjectHandler
{
    //    public $db;
    public $db_table;
    public $obj_class = RssPlugins::class;
    public $obj_key   = 'rssf_conf_id';
    public $sortby    = 'rssf_order';
    public $order     = 'ASC';

    /**
     * RssPluginsHandler constructor.
     * @param \XoopsDatabase $db
     */
    public function __construct(\XoopsDatabase $db)
    {
        $this->db       = $db;
        $helper         = rssfit\Helper::getInstance();
        $this->db_table = $this->db->prefix($helper->getDirname() . '_plugins');
    }

    /**
     * @param \XoopsDatabase $db
     * @return static
     */
    public function getInstance(\XoopsDatabase $db)
    {
        static $instance;
        if (null === $instance) {
            $instance = new static($db);
        }
        return $instance;
    }

    /**
     * @return \XoopsObject
     */
    public function create()
    {
        $obj = new $this->obj_class();
        $obj->setNew();
        return $obj;
    }

    /**
     * @param int    $id
     * @param string $fields
     * @return bool|\XoopsObject
     */
    public function get($id, $fields = '*')
    {
        $ret      = false;
        $criteria = new \Criteria($this->obj_key, (int)$id);
        if ($objs =& $this->getObjects($criteria) && 1 === count($objs)) {
            $ret =& $objs[0];
        }
        return $ret;
    }

    /**
     * @param \XoopsObject $obj
     * @return bool|mixed
     */
    public function insert(\XoopsObject $obj) //, $force=false)
    {
        $force = false;
        if (strtolower(get_class($obj)) != strtolower($this->obj_class)) {
            return false;
        }
        if (!$obj->isDirty()) {
            return true;
        }
        if (!$obj->cleanVars()) {
            return false;
        }
        foreach ($obj->cleanVars as $k => $v) {
            if (XOBJ_DTYPE_INT == $obj->vars[$k]['data_type']) {
                $cleanvars[$k] = (int)$v;
            } else {
                $cleanvars[$k] = $this->db->quoteString($v);
            }
        }
        if (count($obj->getErrors()) > 0) {
            return false;
        }
        if ($obj->isNew() || empty($cleanvars[$this->obj_key])) {
            $cleanvars[$this->obj_key] = $this->db->genId($this->db_table . '_' . $this->obj_key . '_seq');
            $sql                       = 'INSERT INTO ' . $this->db_table . ' (' . implode(',', array_keys($cleanvars)) . ') VALUES (' . implode(',', array_values($cleanvars)) . ')';
        } else {
            unset($cleanvars[$this->obj_key]);
            $sql = 'UPDATE ' . $this->db_table . ' SET';
            foreach ($cleanvars as $k => $v) {
                $sql .= ' ' . $k . '=' . $v . ',';
            }
            $sql = substr($sql, 0, -1);
            $sql .= ' WHERE ' . $this->obj_key . ' = ' . $obj->getVar($this->obj_key);
        }
        if (false !== $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
            $obj->setErrors('Could not store data in the database.<br>' . $this->db->error() . ' (' . $this->db->errno() . ')<br>' . $sql);
            return false;
        }
        if (false === $obj->getVar($this->obj_key)) {
            $obj->assignVar($this->obj_key, $this->db->getInsertId());
        }
        return $obj->getVar($this->obj_key);
    }

    /**
     * @param \XoopsObject $obj
     * @return bool|void
     */
    public function delete(\XoopsObject $obj) //, $force = false)
    {
        $force = false;
        if (strtolower(get_class($obj)) != strtolower($this->obj_class)) {
            return false;
        }
        $sql = 'DELETE FROM ' . $this->db_table . ' WHERE ' . $this->obj_key . '=' . $obj->getVar($this->obj_key);
        if (false !== $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
            return false;
        }
        return true;
    }

    /**
     * @param null   $criteria
     * @param string $fields
     * @param string $key
     * @return array|bool
     */
    public function &getObjects($criteria = null, $fields = '*', $key = '')
    {
        $ret   = false;
        $limit = $start = 0;
        switch ($fields) {
            case 'p_activated':
                $fields = 'rssf_conf_id, rssf_filename, rssf_grab, rssf_order';
                break;
            case 'p_inactive':
                $fields = 'rssf_conf_id, rssf_filename';
                break;
            case 'sublist':
                $fields = 'rssf_conf_id, rssf_filename, subfeed, sub_title';
                break;
        }
        $sql = 'SELECT ' . $fields . ' FROM ' . $this->db_table;
        if (null !== $criteria && is_subclass_of($criteria, 'CriteriaElement')) {
            $sql .= ' ' . $criteria->renderWhere();
            if ('' != $criteria->getSort()) {
                $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        if (!preg_match('/ORDER\ BY/', $sql)) {
            $sql .= ' ORDER BY ' . $this->sortby . ' ' . $this->order;
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return false;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            $obj = new $this->obj_class();
            $obj->assignVars($myrow);
            switch ($key) {
                default:
                    $ret[] =& $obj;
                    break;
                case 'id':
                    $ret[$myrow[$this->obj_key]] =& $obj;
                    break;
            }
            unset($obj);
        }
        return $ret;
    }

    /**
     * @param null  $criteria
     * @param array $fields
     * @param bool  $force
     * @return bool|string
     */
    public function modifyObjects($criteria = null, $fields = [], $force = false)
    {
        if (is_array($fields) && count($fields) > 0) {
            $obj = new $this->obj_class();
            $sql = '';
            foreach ($fields as $k => $v) {
                $sql .= $k . ' = ';
                $sql .= 3 == $obj->vars[$k]['data_type'] ? (int)$v : $this->db->quoteString($v);
                $sql .= ', ';
            }
            $sql = substr($sql, 0, -2);
            $sql = 'UPDATE ' . $this->db_table . ' SET ' . $sql;
            if (null !== $criteria && is_subclass_of($criteria, 'CriteriaElement')) {
                $sql .= ' ' . $criteria->renderWhere();
            }
            if (false !== $force) {
                $result = $this->db->queryF($sql);
            } else {
                $result = $this->db->query($sql);
            }
            if (!$result) {
                return 'Could not store data in the database.<br>' . $this->db->error() . ' (' . $this->db->errno() . ')<br>' . $sql;
            }
        }
        return false;
    }

    /**
     * @param null $criteria
     * @return bool
     */
    public function getCount($criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->db_table;
        if (null !== $criteria && is_subclass_of($criteria, 'CriteriaElement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        $result = $this->db->query($sql);
        if (!$result) {
            return false;
        }
        list($count) = $this->db->fetchRow($result);
        return $count;
    }

    /**
     * @param        $obj
     * @param string $type
     * @return bool
     */
    public function forceDeactivate(&$obj, $type = 'rssf_activated')
    {
        $criteria = new \Criteria($this->obj_key, $obj->getVar($this->obj_key));
        $fields   = ['rssf_activated' => 0, 'subfeed' => 0];
        $this->modifyObjects($criteria, $fields, true);
        return true;
    }

    /**
     * @return array|bool
     */
    public function &getPluginFileList()
    {
        $ret = false;
        if ($objs =& $this->getObjects(null, 'rssf_filename')) {
            foreach ($objs as $o) {
                $ret[] = $o->getVar('rssf_filename');
            }
        }
        return $ret;
    }

    /**
     * @param $obj
     * @return bool
     */
    public function &checkPlugin($obj)
    {
        $ret = false;
        global $module_handler;
        $file = RSSFIT_ROOT_PATH . 'plugins/' . $obj->getVar('rssf_filename');
        if (file_exists($file)) {
            $require = require_once $file;
            $name    = explode('.', $obj->getVar('rssf_filename'));
            $class   = 'Rssfit' . ucfirst($name[1]);
            if (class_exists($class)) {
                $handler = new $class;
                if (!method_exists($handler, 'loadmodule') || !method_exists($handler, 'grabentries')) {
                    $obj->setErrors(_AM_RSSFIT_PLUGIN_FUNCNOTFOUND);
                } else {
                    $dirname = $handler->dirname;
                    if (!empty($dirname) && is_dir(XOOPS_ROOT_PATH . '/modules/' . $dirname)) {
                        if (!$handler->loadModule()) {
                            $obj->setErrors(_AM_RSSFIT_PLUGIN_MODNOTFOUND);
                        } else {
                            $ret =& $handler;
                        }
                    } else {
                        $obj->setErrors(_AM_RSSFIT_PLUGIN_MODNOTFOUND);
                    }
                }
            } else {
                $obj->setErrors(_AM_RSSFIT_PLUGIN_CLASSNOTFOUND . ' ' . $class);
            }
        } else {
            $obj->setErrors(_AM_RSSFIT_PLUGIN_FILENOTFOUND);
        }
        return $ret;
    }
}
