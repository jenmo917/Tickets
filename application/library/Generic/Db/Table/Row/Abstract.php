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

	public static function getColumnNames($option = null, $delimiter = null)
	{
		$vars = get_class_vars(get_called_class());

		if ( !isset($vars['_columns']) )
			throw new Zend_Exception('$_columns is not set.');
		if ( !is_array($vars['_columns']))
			throw new Zend_Exception('$_columns is not an array.');
		if ( !is_null($option) && !is_string($option) )
			throw new Zend_Exception('$option must either be null or a string');
		$diff = array_diff(array($option), array('keys', 'columns', 'both'));
		if ( !is_null($option) && !empty($diff) )
			throw new Zend_Exception('$option must either be keys, columns or both if set.');

		$result;
		switch ($option) {
			case null:
			case 'columns':
				if (null === $delimiter)
					$result = array_values($vars['_columns']);
				else
					foreach (array_keys($vars['_columns']) as $key)
						$result[] = self::getColumnNameForUrl($key, $delimiter);

				break;
			case 'keys':
				$result = array_keys($vars['_columns']);
				break;
			case 'both':
				if (null === $delimiter)
					$result = $vars['_columns'];
				else
					foreach (array_keys($vars['_columns']) as $key)
						$result[$key] = self::getColumnNameForUrl($key, $delimiter);
				break;
			default:
				break;
		}
		return $result;
	}

	public static function getColumnNameForUrl( $columnName, $separator = '-' )
	{
		self::getColumnName($columnName);
		$separated = preg_replace('%(?<!^)\p{Lu}%usD', $separator.'$0', $columnName);
		return mb_strtolower($separated, 'utf-8');
	}

	public function setColumnsFromUrl(array $data, $separator = '-', $capitalizeFirstCharacter = false)
	{
		foreach ($data as $urlKey => $datum)
		{
			$this->setColumnFromUrl($urlKey, $datum, $separator, $capitalizeFirstCharacter);
		}
		return $this;
	}

	public function setColumnFromUrl($urlKey, $datum, $separator, $capitalizeFirstCharacter = false)
	{
		$key = str_replace(' ', '', ucwords(str_replace($separator, ' ', $urlKey)));

		if (!$capitalizeFirstCharacter) {
			$key[0] = strtolower($key[0]);
		}
		$this->setColumn($key, $datum);
		return $this;
	}
}
