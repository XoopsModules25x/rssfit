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
 * Class MiscHandler
 * @package XoopsModules\Rssfit
 */
class MiscHandler extends \XoopsPersistableObjectHandler
{
    public $db;
    public $db_table;
    public $obj_class = Misc::class;
    public $obj_key   = 'misc_id';
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
        $this->db       = $db;
        $table = $db->prefix($helper->getDirname() . '_misc');
        $this->db_table = $table;

        parent::__construct($db, $table, Misc::class, 'misc_id', 'misc_title');
    }

    /**
     * @param \XoopsDatabase|null $db
     * @return \XoopsModules\Rssfit\MiscHandler
     */
    public function getInstance(\XoopsDatabase $db = null)
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

    //    public function get($id = null, $fields = '*')

    /**
     * @param null|int $id
     * @param null|array $fields
     * @return bool|mixed|\XoopsObject|null
     */
    public function get($id = null, $fields = null)
    {
        $criteria = new \Criteria($this->obj_key, (int)$id);
        $objs     = $this->getObjects2($criteria);
        if (\is_array($objs) && \count($objs) > 0) {
            return 1 != \count($objs) ? false : $objs[0];
        }

        return false;
    }

    /**
     * count objects matching a condition
     * @param null|\Criteria|\CriteriaCompo $criteria
     * @return false|mixed count of objects
     */
    public function getCount($criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->db_table;
        if (\is_object($criteria) && \is_subclass_of($criteria, \CriteriaElement::class)) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        [$count] = $this->db->fetchRow($result);

        return $count;
    }

    /**
     * @param null|\CriteriaElement   $criteria
     * @param string $fields
     * @param string $key
     * @return false|array
     */
    public function getObjects2($criteria = null, $fields = '*', $key = '')
    {
        $ret    = false;
        $limit  = $start = 0;
//        $fields = '*';
        $sql    = 'SELECT ' . $fields . ' FROM ' . $this->db_table;
        if (\is_object($criteria) && \is_subclass_of($criteria, \CriteriaElement::class)) {
            $sql .= ' ' . $criteria->renderWhere();
            if ('' != $criteria->getSort()) {
                $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        if (!\preg_match('/ORDER\ BY/', $sql)) {
            $sql .= ' ORDER BY ' . $this->obj_key . ' ASC';
        }
        $result = $this->db->query($sql, $limit, $start);
        if ($result instanceof \mysqli_result) {
            $ret = [];
            while (false !== ($myrow = $this->db->fetchArray($result))) {
                $obj = new $this->obj_class();
                $obj->assignVars($myrow);
                switch ($key) {
                    default:
                        $ret[] = $obj;
                        break;
                    case 'title':
                        $ret[$myrow['misc_title']] = $obj;
                        break;
                    case 'id':
                        $ret[$myrow[$this->obj_key]] = $obj;
                        break;
                }
                unset($obj);
            }
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
//        $force = false;
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
        if (false === $obj->getVar($this->obj_key)) {
            $obj->assignVar($this->obj_key, $this->db->getInsertId());
        }

        return $obj->getVar($this->obj_key);
    }

    /**
     * @param null|\CriteriaElement  $criteria
     * @param array $fields
     * @param bool $force
     * @return false|string
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
     * @param \XoopsObject $obj
     * @param bool        $force
     * @return bool
     */
    public function delete(\XoopsObject $obj, $force = false)
    {
//        $force = false;
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
}
