<?php
/**
 * Acl_Db_Table_Abstact
 * @author		Daniel Josefsson
 * @version	0.1
 */
class Acl_Db_Table_Abstract extends Zend_Db_Table_Abstract
{
	/**
	* Returns `$string`
	* @author	Daniel Josefsson <dannejosefsson@gmail.com>
	* @since	v0,1
	* @param 	string $string
	* @return	string
	*/
	function quoteString( $string )
	{
		return '`'.$string.'`';
	}

	public static function getTableName( )
	{
		return static::TABLE_NAME;
	}

	public static function getRowClassName( )
	{
		return static::ROW_CLASS;
	}

	public static function getColumnName($columnName)
	{
		$rowClass = static::ROW_CLASS;
		return $rowClass::getColumnName($columnName);
	}

	public static function getColumnNames($option = null)
	{
		$rowClass = static::ROW_CLASS;
		return $rowClass::getColumnNames($option);
	}
}
