<?php
/**
 * Acl_Db_Table_Row_UserOpenId
 * @author		Daniel Josefsson
 * @version	0.1
 * @date		2012-09-10
 */
class Acl_Db_Table_Row_UserOpenId extends Generic_Db_Table_Row_Abstract
{
	const TABLE_CLASS = 'Acl_Db_Table_UserOpenIds';
	/**
	 * #@+
	 * @access	protected
	 * @var		string
	 */

	/**
	 * The table class is given by TABLE_CLASS.
	 */
	protected $_tableClass 	= self::TABLE_CLASS;

	/**
	 * Primary column is ll_id.
	 */
	protected $_primary		= array('user_id', 'open_id');

	/**
	 * Table columns
	 * @access	protected
	 * @var		array
	 */
	protected static $_columns =
	array(	'userId'			=> 'user_id',
			'openId'			=> 'open_id',
			'created'			=> 'created',
			'updated'			=> 'updated',);
}
