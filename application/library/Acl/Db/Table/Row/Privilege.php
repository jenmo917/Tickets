<?php
/**
 * Acl_Db_Table_Row_Privilege
 * Connects roles with resources.
 * @author		Daniel Josefsson
 * @version	0.1
 */
class Acl_Db_Table_Row_Privilege extends Generic_Db_Table_Row_Abstract
{
	const TABLE_CLASS = 'Acl_Db_Table_Privileges';
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
	* Primary column is role_id.
	*/
	protected $_primary		= 'privilege_id';
	/**#@-*/
	/**
	* Table columns
	* @access	protected
	* @var		array
	*/
	protected static $_columns =
		array(	'privilegeId'		=> 'privilege_id',
				'userId'			=> 'user_id',
				'organisationId'	=> 'organisation_id',
				'eventId'			=> 'event_id',
				'roleId'			=> 'role_id',
				'startTime'			=> 'start_time',
				'endTime'			=> 'end_time',
				'created'			=> 'created',
				'updated'			=> 'updated',);

}
