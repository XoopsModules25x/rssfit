<?php
// $Id$
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

if( !defined('RSSFIT_ROOT_PATH') ){ exit(); }

/**
 * Class RssPlugins
 */
class RssPlugins extends XoopsObject{
	function __construct(){
		parent::__construct();
	//	key, data_type, value, req, max, opt
		$this->initVar("rssf_conf_id", XOBJ_DTYPE_INT, NULL);
		$this->initVar("rssf_filename", XOBJ_DTYPE_TXTBOX, '');
		$this->initVar("rssf_activated", XOBJ_DTYPE_INT, 0);
		$this->initVar("rssf_grab", XOBJ_DTYPE_INT, 0, true);
		$this->initVar("rssf_order", XOBJ_DTYPE_INT, 0);
		$this->initVar("subfeed", XOBJ_DTYPE_INT, 0);
		$this->initVar("sub_entries", XOBJ_DTYPE_INT, 0);
		$this->initVar("sub_link", XOBJ_DTYPE_TXTBOX, '');
		$this->initVar("sub_title", XOBJ_DTYPE_TXTBOX, '');
		$this->initVar("sub_desc", XOBJ_DTYPE_TXTBOX, '');
		$this->initVar("img_url", XOBJ_DTYPE_TXTBOX, '');
		$this->initVar("img_link", XOBJ_DTYPE_TXTBOX, '');
		$this->initVar("img_title", XOBJ_DTYPE_TXTBOX, '');
	}
}

/**
 * Class RssPluginsHandler
 */
class RssPluginsHandler extends XoopsObjectHandler {
	var $db;
	var $db_table;
	var $obj_class = 'RssPlugins';
	var $obj_key = 'rssf_conf_id';
	var $sortby = 'rssf_order';
	var $order = 'ASC';

    /**
     * @param $db
     */
    function RssPluginsHandler(&$db){
		$this->db =& $db;
		$this->db_table = $this->db->prefix('rssfit_plugins');
	}

    /**
     * @param $db
     *
     * @return RssPluginsHandler
     */function &getInstance(&$db){
		static $instance;
		if( !isset($instance) ){
			$instance = new RssPluginsHandler($db);
		}
		return $instance;
	}
	function &create(){
		$obj = new $this->obj_class();
		$obj->setNew();
		return $obj;
	}

    /**
     * @param int    $id
     * @param string $fields
     *
     * @return bool
     */function &get($id, $fields='*'){
		$ret = false;
		$criteria = new Criteria($this->obj_key, intval($id));
		if( $objs =& $this->getObjects($criteria) && count($objs) === 1 ){
			$ret =& $objs[0];
		}
		return $ret;
	}

    /**
     * @param object $obj
     * @param bool   $force
     *
     * @return bool
     */function insert(&$obj, $force=false){
		if( strtolower(get_class($obj)) != strtolower($this->obj_class) ){
			return false;
		}
		if( !$obj->isDirty() ){
			return true;
		}
		if( !$obj->cleanVars() ){
			return false;
		}
		foreach( $obj->cleanVars as $k=>$v ){
			if( $obj->vars[$k]['data_type'] == XOBJ_DTYPE_INT ){
				$cleanvars[$k] = intval($v);
			}else{
				$cleanvars[$k] = $this->db->quoteString($v);
			}
		}
		if( count($obj->getErrors()) > 0 ){
			return false;
		}
		if( $obj->isNew() || empty($cleanvars[$this->obj_key]) ){
			$cleanvars[$this->obj_key] = $this->db->genId($this->db_table.'_'.$this->obj_key.'_seq');
			$sql = 'INSERT INTO '.$this->db_table.' ('.implode(',', array_keys($cleanvars)).') VALUES ('.implode(',', array_values($cleanvars)) .')';
		}else{
			unset($cleanvars[$this->obj_key]);
			$sql = 'UPDATE '.$this->db_table.' SET';
			foreach ($cleanvars as $k => $v ){
				$sql .= ' '.$k.'='.$v.',';
			}
			$sql = substr($sql, 0, -1);
			$sql .= ' WHERE '.$this->obj_key.' = '.$obj->getVar($this->obj_key);
		}
		if( false != $force ){
			$result = $this->db->queryF($sql);
		}else{
			$result = $this->db->query($sql);
		}
		if( !$result ){
			$obj->setErrors('Could not store data in the database.<br />'.$this->db->error().' ('.$this->db->errno().')<br />'.$sql);
			return false;
		}
		if( false == $obj->getVar($this->obj_key) ){
			$obj->assignVar($this->obj_key, $this->db->getInsertId());
		}
		return $obj->getVar($this->obj_key);
	}

    /**
     * @param object $obj
     * @param bool   $force
     *
     * @return bool
     */function delete(&$obj, $force = false){
		if( strtolower(get_class($obj)) != strtolower($this->obj_class) ){
			return false;
		}
		$sql = 'DELETE FROM '.$this->db_table.' WHERE '.$this->obj_key.'='.$obj->getVar($this->obj_key);
		if( false != $force ){
			$result = $this->db->queryF($sql);
		}else{
			$result = $this->db->query($sql);
		}
		if( !$result ){
			return false;
		}
		return true;
	}

    /**
     * @param null   $criteria
     * @param string $fields
     * @param string $key
     *
     * @return array|bool
     */function &getObjects($criteria=null, $fields='*', $key=''){
		$ret = false;
		$limit = $start = 0;
		switch($fields){
		case 'p_activated';
			$fields = 'rssf_conf_id, rssf_filename, rssf_grab, rssf_order';
		break;
		case 'p_inactive';
			$fields = 'rssf_conf_id, rssf_filename';
		break;
		case 'sublist';
			$fields = 'rssf_conf_id, rssf_filename, subfeed, sub_title';
		break;
		}
		$sql = 'SELECT '.$fields.' FROM '.$this->db_table;
		if( isset($criteria) && is_subclass_of($criteria, 'criteriaelement') ){
			$sql .= ' '.$criteria->renderWhere();
			if( $criteria->getSort() != '' ){
				$sql .= ' ORDER BY '.$criteria->getSort().' '.$criteria->getOrder();
			}
			$limit = $criteria->getLimit();
			$start = $criteria->getStart();
		}
		if( !preg_match('/ORDER\ BY/', $sql) ){
			$sql .= ' ORDER BY '.$this->sortby.' '.$this->order;
		}
		$result = $this->db->query($sql, $limit, $start);
		if( !$result ){
			return false;
		}
		while( $myrow = $this->db->fetchArray($result) ){
			$obj = new $this->obj_class();
			$obj->assignVars($myrow);
			switch($key){
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
     *
     * @return bool|string
     */function modifyObjects($criteria=null, $fields=array(), $force=false){
		if( is_array($fields) && count($fields) > 0 ){
			$obj = new $this->obj_class();
			$sql = '';
			foreach( $fields as $k => $v ){
				$sql .= $k.' = ';
				$sql .= $obj->vars[$k]['data_type'] == 3 ? intval($v) : $this->db->quoteString($v);
				$sql .= ', ';
			}
			$sql = substr($sql, 0, -2);
			$sql = 'UPDATE '.$this->db_table.' SET '.$sql;
			if( isset($criteria) && is_subclass_of($criteria, 'criteriaelement') ){
				$sql .= ' '.$criteria->renderWhere();
			}
			if( false != $force ){
				$result = $this->db->queryF($sql);
			}else{
				$result = $this->db->query($sql);
			}
			if( !$result ){
				return 'Could not store data in the database.<br />'.$this->db->error().' ('.$this->db->errno().')<br />'.$sql;
			}
		}
		return false;
	}

    /**
     * @param null $criteria
     *
     * @return bool
     */
    function getCount($criteria = null){
		$sql = 'SELECT COUNT(*) FROM '.$this->db_table;
		if( isset($criteria) && is_subclass_of($criteria, 'criteriaelement') ){
			$sql .= ' '.$criteria->renderWhere();
		}
		$result = $this->db->query($sql);
		if( !$result ){
			return false;
		}
		list($count) = $this->db->fetchRow($result);
		return $count;
	}

    /**
     * @param        $obj
     * @param string $type
     *
     * @return bool
     */function forceDeactivate(&$obj, $type='rssf_activated'){
		$criteria = new Criteria($this->obj_key, $obj->getVar($this->obj_key));
		$fields = array('rssf_activated' => 0,'subfeed' => 0);
		$this->modifyObjects($criteria, $fields, true);
		return true;
	}

    /**
     * @return array|bool
     */
    function &getPluginFileList(){
		$ret = false;
		if( $objs =& $this->getObjects(null, 'rssf_filename') ){
			foreach( $objs as $o ){
				$ret[] = $o->getVar('rssf_filename');
			}
		}
		return $ret;
	}

    /**
     * @param $obj
     *
     * @return bool
     */function &checkPlugin(&$obj){
		$ret = false;
		global $module_handler;
		$file = RSSFIT_ROOT_PATH.'plugins/'.$obj->getVar('rssf_filename');
		if( file_exists($file) ){
			$require = require_once $file;
			$name = explode('.', $obj->getVar('rssf_filename'));
			$class = 'Rssfit'.ucfirst($name[1]);
			if( class_exists($class) ){
				$handler = new $class;
				if( !method_exists($handler, 'loadmodule') || !method_exists($handler, 'grabentries') ){
					$obj->setErrors(_AM_PLUGIN_FUNCNOTFOUND);
				}else{
					$dirname = $handler->dirname;
					if( !empty($dirname) && is_dir(XOOPS_ROOT_PATH.'/modules/'.$dirname) ){
						if( !$handler->loadModule() ){
							$obj->setErrors(_AM_PLUGIN_MODNOTFOUND);
						}else{
							$ret =& $handler;
						}
					}else{
						$obj->setErrors(_AM_PLUGIN_MODNOTFOUND);
					}
				}
			}else{
				$obj->setErrors(_AM_PLUGIN_CLASSNOTFOUND);
			}
		}else{
			$obj->setErrors(_AM_PLUGIN_FILENOTFOUND);
		}
		return $ret;
	}

}

