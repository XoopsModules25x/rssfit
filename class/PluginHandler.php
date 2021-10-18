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
 * Class PluginHandler
 * @package XoopsModules\Rssfit
 */
class PluginHandler extends \XoopsPersistableObjectHandler
{
    public $dbTable;
    public $objClass = Plugin::class;
    public $objKey   = 'rssf_conf_id';
    public $sortby   = 'rssf_order';
    public $order    = 'ASC';
    /**
     * @var \XoopsModules\Rssfit\Helper
     */
    public $helper;

    public function __construct(?\XoopsDatabase $db = null, ?Helper $helper = null)
    {
        if (null === $helper) {
            $helper = \XoopsModules\Rssfit\Helper::getInstance();
        }
        $this->helper = $helper;

        if (null === $db) {
            $db = \XoopsDatabaseFactory::getDatabaseConnection();
        }
        $this->db = $db;
        //        $this->dbTable = $db->prefix($helper->getDirname() . '_plugins');

        $table         = $db->prefix($helper->getDirname() . '_plugins');
        $this->dbTable = $table;

        parent::__construct($db, $table, Plugin::class, 'rssf_conf_id', 'rssf_filename');
    }

    public function getInstance(?\XoopsDatabase $db = null): \XoopsPersistableObjectHandler
    {
        static $instance;
        if (null === $instance) {
            $instance = new static($db);
        }

        return $instance;
    }

    public function create(bool $isNew = true): ?\XoopsObject
    {
        $object = parent::create($isNew);
        //        if ($isNew) {
        //            $object->setDefaultPermissions();
        //        }
        $object->helper = $this->helper;

        return $object;
    }

    //    public function get($id, $fields = '*')

    public function get(?int $id = null, ?array $fields = null): ?\XoopsObject
    {
        $ret      = null;
        $criteria = new \Criteria($this->objKey, (int)$id);
        $objs     = $this->getObjects2($criteria);
        if (\is_array($objs) && 1 === \count($objs)) {
            $ret = &$objs[0];
        }

        return $ret;
    }

    /**
     * @param bool $force flag to force the query execution despite security settings
     * @return array|bool|int|mixed|null
     */
    public function insert(\XoopsObject $object, bool $force = true)
    {
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
        if ($object->isNew() || empty($cleanvars[$this->objKey])) {
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
        //        if (false === $object->getVar($this->objKey)) {
        if (0 === (int)$object->getVar($this->objKey)) {
            $object->assignVar($this->objKey, $this->db->getInsertId());
        }

        return $object->getVar($this->objKey);
    }


    public function delete(\XoopsObject $object, bool $force = false): bool
    {
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

    /**
     * @param null|\Criteria|\CriteriaCompo $criteria
     */
    public function getObjects2($criteria = null, string $fields = '*', string $key = ''): ?array
    {
        $ret   = null;
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
        $sql = 'SELECT ' . $fields . ' FROM ' . $this->dbTable;
        if (($criteria instanceof \CriteriaCompo) || ($criteria instanceof \Criteria)) {
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
            $ret = [];
            while (false !== ($myrow = $this->db->fetchArray($result))) {
                $object = new $this->objClass();
                $object->assignVars($myrow);
                switch ($key) {
                    default:
                        $ret[] = &$object;
                        break;
                    case 'id':
                        $ret[$myrow[$this->objKey]] = &$object;
                        break;
                }
                unset($object);
            }
        }
        return $ret;
    }

    /**
     * @param null|\Criteria|\CriteriaCompo $criteria
     */
    public function modifyObjects($criteria = null, array $fields = [], bool $force = false): ?string
    {
        if ($fields && \is_array($fields)) {
            $object = new $this->objClass();
            $sql    = '';
            foreach ($fields as $k => $v) {
                $sql .= $k . ' = ';
                $sql .= 3 == $object->vars[$k]['data_type'] ? (int)$v : $this->db->quoteString($v);
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

    /**
     * count objects matching a condition
     * @param null|\Criteria|\CriteriaCompo $criteria
     */
    public function getCount($criteria = null): ?int
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->dbTable;
        if (($criteria instanceof \CriteriaCompo) || ($criteria instanceof \Criteria)) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        $result = $this->db->query($sql);
        if (!$result) {
            return null;
        }
        [$count] = $this->db->fetchRow($result);

        return $count;
    }

    public function forceDeactivate(\XoopsObject $object, string $type = 'rssf_activated'): bool
    {
        $criteria = new \Criteria($this->objKey, $object->getVar($this->objKey));
        $fields   = ['rssf_activated' => 0, 'subfeed' => 0];
        $this->modifyObjects($criteria, $fields, true);

        return true;
    }

    public function &getPluginFileList(): ?array
    {
        $ret  = null;
        $objs = $this->getObjects2(null, 'rssf_filename');
        if (\is_array($objs) && !empty($objs)) {
            $ret = [];
            foreach ($objs as $o) {
                $ret[] = $o->getVar('rssf_filename');
            }
        }

        return $ret;
    }

    /**
     * @return null|mixed
     */
    public function checkPlugin(\XoopsObject $object)
    {
        $ret = null;
        global $moduleHandler;
        $file = \RSSFIT_ROOT_PATH . 'class/Plugins/' . $object->getVar('rssf_filename');
        if (\is_file($file)) {
            $ret = [];
            //mb            $require_once = require $file;
            $name  = \explode('.', $object->getVar('rssf_filename'));
            $class = __NAMESPACE__ . '\Plugins\\' . \ucfirst($name[0]);
            if (\class_exists($class)) {
                $handler = new $class();
                if (!\method_exists($handler, 'loadmodule') || !\method_exists($handler, 'grabentries')) {
                    $object->setErrors(\_AM_RSSFIT_PLUGIN_FUNCNOTFOUND);
                } else {
                    $dirname = $handler->dirname;
                    if (!empty($dirname) && \is_dir(XOOPS_ROOT_PATH . '/modules/' . $dirname)) {
                        if ($handler->loadModule()) {
                            $ret = $handler;
                        } else {
                            $object->setErrors(\_AM_RSSFIT_PLUGIN_MODNOTFOUND);
                        }
                    } else {
                        $object->setErrors(\_AM_RSSFIT_PLUGIN_MODNOTFOUND);
                    }
                }
            } else {
                $object->setErrors(\_AM_RSSFIT_PLUGIN_CLASSNOTFOUND . ' ' . $class);
            }
        } else {
            $object->setErrors(\_AM_RSSFIT_PLUGIN_FILENOTFOUND);
        }

        return $ret;
    }
}
