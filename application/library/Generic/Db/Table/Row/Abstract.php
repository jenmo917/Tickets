<?php
/**
 * Generic_Db_Table_Row_Abstract
 * @author		Daniel Josefsson
 * @version	0.1
 */
class Generic_Db_Table_Row_Abstract extends Zend_Db_Table_Row_Abstract
{
	/**
	 * Sets $propertyName table column to given $value.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param	string $propertyName
	 * @param	mixed $value
	 * @return	object|boolean
	 */
	public function setColumn( $propertyName, $value )
	{
		if ( $columnName = $this->getColumnName($propertyName) )
		{
			$this->$columnName = $value;
			return $this;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Gets $propertyName table value.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param	string $propertyName
	 * @return	mixed|boolean
	 */
	public function getColumn( $propertyName )
	{
		if ( $columnName = $this->getColumnName($propertyName) )
		{
			{
				return $this->$columnName;
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * Gets $columnName table column name.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param	string $columnName
	 * @return	mixed|boolean
	 */
	public static function getColumnName($columnName)
	{
		$vars = get_class_vars(get_called_class());

		if ( !isset($vars['_columns']) )
		{
			throw new Zend_Exception('$_columns is not set.');
		}
		if ( !is_array($vars['_columns']))
		{
			throw new Zend_Exception('$_columns is not an array.');
		}
		if (!array_key_exists($columnName, $vars['_columns']) )
		{
			throw new Zend_Exception($columnName.' is not found in $_columns.');
		}

		return $vars['_columns'][$columnName];
	}
}
