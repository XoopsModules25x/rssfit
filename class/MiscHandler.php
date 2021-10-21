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
 * Class MiscHandler
 * @package XoopsModules\Rssfit
 */
class MiscHandler extends \XoopsPersistableObjectHandler
{
    /**
     * @var \XoopsMySQLDatabase
     */
    public $db;
    public $dbTable;
    public $objClass = Misc::class;
    public $objKey   = 'misc_id';
    public $helper;

    public function __construct(?\XoopsMySQLDatabase $db = null, ?Helper $helper = null)
    {
        if (null === $helper) {
            $helper = Helper::getInstance();
        }
        $this->helper = $helper;

        if (null === $db) {
            $db = \XoopsDatabaseFactory::getDatabaseConnection();
        }
        $this->db      = $db;
        $table         = $db->prefix($helper->getDirname() . '_misc');
        $this->dbTable = $table;

        parent::__construct($db, $table, Misc::class, 'misc_id', 'misc_title');
    }

    public function getInstance(?\XoopsMySQLDatabase $db = null): MiscHandler
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
    public function create($isNew = true): ?\XoopsObject
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
     */
    public function get($id = null, $fields = null): ?\XoopsObject
    {
        $criteria = new \Criteria($this->objKey, (string)$id);
        $objs     = $this->getObjects2($criteria);
        if (\is_array($objs) && !empty($objs)) {
            return 1 != \count($objs) ? null : $objs[0];
        }

        return null;
    }

    /**
     * count objects matching a condition
     * @param null|\Criteria|\CriteriaCompo $criteria
     */
    public function getCount($criteria = null): ?int
    {
        $ret = null;
        $sql = 'SELECT COUNT(*) FROM ' . $this->dbTable;
        if (($criteria instanceof \CriteriaCompo) || ($criteria instanceof \Criteria)) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        $result = $this->db->query($sql);
         if ($result instanceof \mysqli_result) {
             [$ret] = $this->db->fetchRow($result);
         }
        return $ret;
    }

    /**
     * @param null|\Criteria|\CriteriaCompo $criteria
     */
    public function getObjects2($criteria = null, string $fields = '*', string $key = ''): ?array
    {
        $ret   = null;
        $start = 0;
        $limit = $start;
        //        $fields = '*';
        $sql = 'SELECT ' . $fields . ' FROM ' . $this->dbTable;
        if (($criteria instanceof \CriteriaCompo) || ($criteria instanceof \Criteria)) {
            $sql .= ' ' . $criteria->renderWhere();
            if ('' != $criteria->getSort()) {
                $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        if (!\preg_match('/ORDER BY/', $sql)) {
            $sql .= ' ORDER BY ' . $this->objKey . ' ASC';
        }
        $result = $this->db->query($sql, $limit, $start);
        if ($result instanceof \mysqli_result) {
            $ret = [];
            while (false !== ($myrow = $this->db->fetchArray($result))) {
                $obj = new $this->objClass();
                $obj->assignVars($myrow);
                switch ($key) {
                    default:
                        $ret[] = $obj;
                        break;
                    case 'title':
                        $ret[$myrow['misc_title']] = $obj;
                        break;
                    case 'id':
                        $ret[$myrow[$this->objKey]] = $obj;
                        break;
                }
                unset($obj);
            }
        }
        return $ret;
    }

    /**
     * @param bool $force flag to force the query execution despite security settings
     * @return array|bool|int|mixed|null
     */
    public function insert(\XoopsObject $object, $force = false)
    {
        //        $force = false;
        if (mb_strtolower(\get_class($object)) != mb_strtolower($this->objClass)) {
            return false;
        }
        if (!$object->isDirty()) {
            return true;
        }
        if (!$object->cleanVars()) {
            return false;
        }
        foreach ($object->cleanVars as $k => $v) {
            if (\XOBJ_DTYPE_INT == $object->vars[$k]['data_type']) {
                $cleanvars[$k] = (int)$v;
            } else {
                $cleanvars[$k] = $this->db->quoteString($v);
            }
        }
        if (\count($object->getErrors()) > 0) {
            return false;
        }
        if (empty($cleanvars[$this->objKey]) || $object->isNew()) {
            $cleanvars[$this->objKey] = $this->db->genId($this->dbTable . '_' . $this->objKey . '_seq');
            $sql                      = 'INSERT INTO ' . $this->dbTable . ' (' . \implode(',', \array_keys($cleanvars)) . ') VALUES (' . \implode(',', $cleanvars) . ')';
        } else {
            unset($cleanvars[$this->objKey]);
            $sql = 'UPDATE ' . $this->dbTable . ' SET';
            foreach ($cleanvars as $k => $v) {
                $sql .= ' ' . $k . '=' . $v . ',';
            }
            $sql = mb_substr($sql, 0, -1);
            $sql .= ' WHERE ' . $this->objKey . ' = ' . $object->getVar($this->objKey);
        }
        if ($force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
            $object->setErrors('Could not store data in the database.<br>' . $this->db->error() . ' (' . $this->db->errno() . ')<br>' . $sql);

            return false;
        }
        if (false === $object->getVar($this->objKey)) {
            $object->assignVar($this->objKey, $this->db->getInsertId());
        }

        return $object->getVar($this->objKey);
    }

    /**
     * @param bool                          $force flag to force the query execution despite security settings
     * @param null|\Criteria|\CriteriaCompo $criteria
     */
    public function modifyObjects(?\Criteria $criteria = null, array $fields = [], bool $force = false): ?string
    {
        if (count($fields) > 0) {
            $obj = new $this->objClass();
            $sql = '';
            foreach ($fields as $k => $v) {
                $sql .= $k . ' = ';
                $sql .= 3 == $obj->vars[$k]['data_type'] ? (int)$v : $this->db->quoteString($v);
                $sql .= ', ';
            }
            $sql = mb_substr($sql, 0, -2);
            $sql = 'UPDATE ' . $this->dbTable . ' SET ' . $sql;
            if (($criteria instanceof \CriteriaCompo) || ($criteria instanceof \Criteria)) {
                $sql .= ' ' . $criteria->renderWhere();
            }
            if ($force) {
                $result = $this->db->queryF($sql);
            } else {
                $result = $this->db->query($sql);
            }
            if (!$result) {
                return 'Could not store data in the database.<br>' . $this->db->error() . ' (' . $this->db->errno() . ')<br>' . $sql;
            }
        }

        return null;
    }

    //TODO this should be deleted?
    /**
     * delete an object from the database
     *
     * @param  \XoopsObject $object reference to the object to delete
     * @param  bool        $force
     * @return bool        FALSE if failed.
     */
    public function delete(\XoopsObject $object, $force = false): bool
    {
        //        $force = false;
        if (mb_strtolower(\get_class($object)) != mb_strtolower($this->objClass)) {
            return false;
        }
        $sql = 'DELETE FROM ' . $this->dbTable . ' WHERE ' . $this->objKey . '=' . $object->getVar($this->objKey);
        if ($force) {
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
