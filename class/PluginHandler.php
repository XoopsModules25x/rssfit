<?php

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
 * Class PluginHandler
 * @package XoopsModules\Rssfit
 */
class PluginHandler extends \XoopsPersistableObjectHandler
{
    public $db;
    public $db_table;
    public $obj_class = Plugin::class;
    public $obj_key   = 'rssf_conf_id';
    public $sortby    = 'rssf_order';
    public $order     = 'ASC';
    /**
     * @var \XoopsModules\Rssfit\Helper
     */
    public $helper;

    /**
     * @param \XoopsDatabase|null              $db
     * @param null|\XoopsModules\Rssfit\Helper $helper
     */
    public function __construct(\XoopsDatabase $db = null, $helper = null)
    {
        if (null === $helper) {
            $helper = \XoopsModules\Rssfit\Helper::getInstance();
        }
        $this->helper = $helper;

        if (null === $db) {
            $db = \XoopsDatabaseFactory::getDatabaseConnection();
        }
        $this->db = $db;
        //        $this->db_table = $db->prefix($helper->getDirname() . '_plugins');

        $table          = $db->prefix($helper->getDirname() . '_plugins');
        $this->db_table = $table;

        parent::__construct($db, $table, Plugin::class, 'rssf_conf_id', 'rssf_filename');
    }

    /**
     * @param \XoopsDatabase|null $db
     * @return \XoopsModules\Rssfit\PluginHandler
     */
    public function getInstance(\XoopsDatabase $db = null): PluginHandler
    {
        static $instance;
        if (null === $instance) {
            $instance = new static($db);
        }

        return $instance;
    }

    /**
     * @param bool $isNew
     * @return \XoopsObject
     */
    public function create($isNew = true)
    {
        $obj = parent::create($isNew);
        //        if ($isNew) {
        //            $obj->setDefaultPermissions();
        //        }
        $obj->helper = $this->helper;

        return $obj;
    }

    //    public function get($id, $fields = '*')

    /**
     * @param null|int $id
     * @param null|array $fields
     * @return bool|mixed|\XoopsObject|null
     */
    public function get($id = null, $fields = null)
    {
        $ret      = false;
        $criteria = new \Criteria($this->obj_key, (int)$id);
        $objs     = $this->getObjects2($criteria);
        if ($objs && 1 === \count($objs)) {
            $ret = &$objs[0];
        }

        return $ret;
    }

    /**
     * @param \XoopsObject $obj
     * @param bool         $force
     * @return array|bool|int|mixed|null
     */
    public function insert(\XoopsObject $obj, $force = false)
    {
        if (mb_strtolower(\get_class($obj)) != mb_strtolower($this->obj_class)) {
            return false;
        }
        if (!$obj->isDirty()) {
            return true;
        }
        if (!$obj->cleanVars()) {
            return false;
        }
        foreach ($obj->cleanVars as $k => $v) {
            if (\XOBJ_DTYPE_INT == $obj->vars[$k]['data_type']) {
                $cleanvars[$k] = (int)$v;
            } else {
                $cleanvars[$k] = $this->db->quoteString($v);
            }
        }
        if (\count($obj->getErrors()) > 0) {
            return false;
        }
        if ($obj->isNew() || empty($cleanvars[$this->obj_key])) {
            $cleanvars[$this->obj_key] = $this->db->genId($this->db_table . '_' . $this->obj_key . '_seq');
            $sql                       = 'INSERT INTO ' . $this->db_table . ' (' . \implode(',', \array_keys($cleanvars)) . ') VALUES (' . \implode(',', $cleanvars) . ')';
        } else {
            unset($cleanvars[$this->obj_key]);
            $sql = 'UPDATE ' . $this->db_table . ' SET';
            foreach ($cleanvars as $k => $v) {
                $sql .= ' ' . $k . '=' . $v . ',';
            }
            $sql = mb_substr($sql, 0, -1);
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
//        if (false === $obj->getVar($this->obj_key)) {
        if (null === $obj->getVar($this->obj_key)) {
            $obj->assignVar($this->obj_key, $this->db->getInsertId());
        }

        return $obj->getVar($this->obj_key);
    }

    /**
     * @param \XoopsObject $obj
     * @param bool         $force
     * @return bool
     */
    public function delete(\XoopsObject $obj, $force = false)
    {
        if (mb_strtolower(\get_class($obj)) != mb_strtolower($this->obj_class)) {
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
     * @param null|\Criteria|\CriteriaCompo $criteria
     * @param string $fields
     * @param string $key
     * @return bool
     */
    public function getObjects2($criteria = null, $fields = '*', $key = '')
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
        if (\is_object($criteria) && \is_subclass_of($criteria, \CriteriaElement::class)) {
            $sql .= ' ' . $criteria->renderWhere();
            if ('' != $criteria->getSort()) {
                $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        if (!\preg_match('/ORDER\ BY/', $sql)) {
            $sql .= ' ORDER BY ' . $this->sortby . ' ' . $this->order;
        }
        $result = $this->db->query($sql, $limit, $start);
        if ($result instanceof \mysqli_result) {
            $ret   = [];
            while (false !== ($myrow = $this->db->fetchArray($result))) {
                $obj = new $this->obj_class();
                $obj->assignVars($myrow);
                switch ($key) {
                    default:
                        $ret[] = &$obj;
                        break;
                    case 'id':
                        $ret[$myrow[$this->obj_key]] = &$obj;
                        break;
                }
                unset($obj);
            }
        }
        return $ret;
    }

    /**
     * @param null|\CriteriaElement  $criteria
     * @param array $fields
     * @param bool  $force
     * @return bool|string
     */
    public function modifyObjects($criteria = null, $fields = [], $force = false)
    {
        if ($fields && \is_array($fields)) {
            $obj = new $this->obj_class();
            $sql = '';
            foreach ($fields as $k => $v) {
                $sql .= $k . ' = ';
                $sql .= 3 == $obj->vars[$k]['data_type'] ? (int)$v : $this->db->quoteString($v);
                $sql .= ', ';
            }
            $sql = mb_substr($sql, 0, -2);
            $sql = 'UPDATE ' . $this->db_table . ' SET ' . $sql;
            if (\is_object($criteria) && \is_subclass_of($criteria, \CriteriaElement::class)) {
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
     * count objects matching a condition
     * @param null|\Criteria|\CriteriaCompo $criteria
     * @return false|int count of objects
     */
    public function getCount($criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->db_table;
        if (\is_object($criteria) && \is_subclass_of($criteria, \CriteriaElement::class)) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        $result = $this->db->query($sql);
        if (!$result) {
            return false;
        }
        [$count] = $this->db->fetchRow($result);

        return $count;
    }

    /**
     * @param        $obj
     * @param string $type
     * @return bool
     */
    public function forceDeactivate($obj, $type = 'rssf_activated')
    {
        $criteria = new \Criteria($this->obj_key, $obj->getVar($this->obj_key));
        $fields   = ['rssf_activated' => 0, 'subfeed' => 0];
        $this->modifyObjects($criteria, $fields, true);

        return true;
    }

    /**
     * @return false|array
     */
    public function &getPluginFileList()
    {
        $ret  = false;
        $objs = $this->getObjects2(null, 'rssf_filename');
        if ($objs) {
            $ret   = [];
            foreach ($objs as $o) {
                $ret[] = $o->getVar('rssf_filename');
            }
        }

        return $ret;
    }

    /**
     * @param \XoopsObject $obj
     * @return false|mixed
     */
    public function checkPlugin($obj)
    {
        $ret = false;
        global $moduleHandler;
        $file = \RSSFIT_ROOT_PATH . 'class/Plugins/' . $obj->getVar('rssf_filename');
        if (\is_file($file)) {
            $ret   = [];
            //mb            $require_once = require $file;
            $name         = \explode('.', $obj->getVar('rssf_filename'));
            $class        = __NAMESPACE__ . '\Plugins\\' . \ucfirst($name[0]);
            if (\class_exists($class)) {
                $handler = new $class();
                if (!\method_exists($handler, 'loadmodule') || !\method_exists($handler, 'grabentries')) {
                    $obj->setErrors(\_AM_RSSFIT_PLUGIN_FUNCNOTFOUND);
                } else {
                    $dirname = $handler->dirname;
                    if (!empty($dirname) && \is_dir(XOOPS_ROOT_PATH . '/modules/' . $dirname)) {
                        if (!$handler->loadModule()) {
                            $obj->setErrors(\_AM_RSSFIT_PLUGIN_MODNOTFOUND);
                        } else {
                            $ret = $handler;
                        }
                    } else {
                        $obj->setErrors(\_AM_RSSFIT_PLUGIN_MODNOTFOUND);
                    }
                }
            } else {
                $obj->setErrors(\_AM_RSSFIT_PLUGIN_CLASSNOTFOUND . ' ' . $class);
            }
        } else {
            $obj->setErrors(\_AM_RSSFIT_PLUGIN_FILENOTFOUND);
        }

        return $ret;
    }
}
