<?php
/**
 * Acl_Db_Table_Row_Resource
 * @author		Daniel Josefsson
 * @version	0.1
 * @date		2012-05-18
 */
class Acl_Db_Table_Row_Resource extends Acl_Db_Table_Row_Abstract
{
	const TABLE_CLASS = 'Acl_Db_Table_Resources';
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
	* Primary column is resource_id.
	*/
	protected $_primary		= 'resource_id';
	/**#@-*/

	/**
	 * Table columns
	 * @access	protected
	 * @var		array
	 */
	protected static $_columns =
		array(	'resourceId'		=> 'resource_id',
				'resource'			=> 'resource',
				'created'			=> 'created',
				'updated'			=> 'updated',);
}
