<?php
/**
 * Acl_Db_Table_Row_UserLiuLogin
 * @author		Daniel Josefsson
 * @version	0.1
 * @date		2012-05-18
 */
class Acl_Db_Table_Row_UserLiuLogin extends Generic_Db_Table_Row_Abstract
{
	const TABLE_CLASS = 'Acl_Db_Table_UserLiuLogins';
	/**
	 * #@+
	 * @access	protected
	 * @var		string
	 */

	/**
	 * The table class is given by TABLE_CLASS.
	 */
	protected $_tableClass		= self::TABLE_CLASS;

	/**
	 * Primary column is ll_id.
	 */
	protected $_primary		= array('user_id', 'liu_id');

	/**
	 * Table columns
	 * @access	protected
	 * @var		array
	 */
	protected static $_columns =
	array(	'userId'			=> 'user_id',
			'liuId'				=> 'liu_id',
			'created'			=> 'created',
			'updated'			=> 'updated',);
}
