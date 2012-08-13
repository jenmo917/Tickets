<?php
/**
 * Automates the ACL resource population.
 * This is a rebuild and extension from David J Clarks implementation.
 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
 * @link	http://blog.davidjclarke.co.uk/database-driven-zend-acl-tutorial-part-one.html
 * @todo	Write a method that removes unexisting resources.
 */
class Acl_Resources
{
	/**
	 * Delimiter constant
	 * @var	string
	 */
	const	DELIMITER	= '::';
	/**
	 * Page prefix constant
	 * @var string
	 */
	const	PAGEPREFIX	= 'page';

	/**#@+
	* @access	private
	* @var		array
	*/

	/** Contains the structure of this project.
	 *	Structure is array(	modulesLoaded = bool,
	 *						moduleNames => array(	controllersLoaded = bool,
	 *												controllerNames => array(actionsLoaded = bool, actionNames))).
	 **/
	private $_pageResources	= array('modulesLoaded' => false);

	/** Contains ignored folders. */
	private $_arrIgnore		= array('.','..',);
	/**#@-*/

	private $_resourcesTable;

	/**
	 * Getter function.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param	string $strVar
	 * @return	mixed
	 */
	public function __get($strVar)
	{
		return ( isset($this->$strVar) ) ? $this->$strVar : null;
	}

	/**
	 * Setter function.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param string $strVar
	 * @param mixed $strValue
	 * @return	mixed
	 */
	public function __set($strVar, $strValue)
	{
		$this->$strVar = $strValue;
	}

	/**
	 * Write resources to db.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @return
	 */
	public function writePageResourcesToDB()
	{
		$this->checkForData();

		$resources = array();

		// Set up Resources table select.
		if ( is_null($this->_resourceTable) )
		{
			$this->_resourceTable = new Acl_Db_Table_Resources();
		}
		$select = $this->_resourceTable->select();
		$resourceTableName = $this->_resourceTable->getColumnName('resource');

		// Create a master resource
		$resources[] = $resource = self::PAGEPREFIX . self::DELIMITER . '*' . self::DELIMITER . '*' . self::DELIMITER . '*';
		// Try to find this resource.
		$select->orWhere(	$resourceTableName. ' = ?', $resource	);
		// Loop through the page resource array to be able to populate the resource db table.
		foreach ( $this->_pageResources as $module => $controllers)
		{
			if ( strcmp('modulesLoaded', $module) )
			{
				// Create resource with wild cards for controller and action.
				$resources[] = $resource =
					self::PAGEPREFIX . self::DELIMITER . $module . self::DELIMITER . '*' . self::DELIMITER . '*';

				// Try to find this resource.
				$select->orWhere(	$resourceTableName. ' = ?', $resource	);

				foreach ($controllers as $controller => $actions)
				{
					if ( strcmp('controllersLoaded', $controller) )
					{
						// Create resource with wild card for action.
						$resources[] = $resource =
							self::PAGEPREFIX . self::DELIMITER . $module . self::DELIMITER . $controller . self::DELIMITER . '*';

						// Try to find this resource.
						$select->orWhere(	$resourceTableName. ' = ?', $resource	);

						foreach ($actions as $key => $action)
						{
							if ( strcmp('actionsLoaded', $key) )
							{
								// Create fixed resource.
								$resources[] = $resource =
									self::PAGEPREFIX . self::DELIMITER . $module . self::DELIMITER . $controller . self::DELIMITER . $action;

								// Try to find this resource.
								$select->orWhere(	$resourceTableName. ' = ?', $resource	);
							}
						}
					}
				}
			}
		}
		// Fetch all existing resources
		$rowSet = $this->_resourceTable->fetchAll($select);

		// If all resources already is in the database, nothing has to be done. Otherwise; save all new resources.
		if ( count($this->_resourceTable !== $rowSet->count()) )
		{
			//Extract resources.
			$existingResources = array();
			foreach ($rowSet as $row)
			{
				$existingResources[] = $row->__get($resourceTableName);
			}

			$newResources = array_diff($resources, $existingResources);

			foreach ($newResources as $resource)
			{
				$this->_resourceTable->createRow()->setColumn($resourceTableName, $resource)->save();
			}
		}

		return $this;
	}

	/**
	 * Check so that at least one module, controller and action each are found.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @throws Zend_Exception
	 */
	private function checkForData()
	{
		$controllersFound = false;
		$actionsFound = false;
		if ( false === $this->_pageResources['modulesLoaded'] )
		{
			throw new Zend_Exception('No modules found.');
		}
		else
		{
			foreach ($this->_pageResources as $module)
			{
				// Since controllersLoaded is a key, skip this one.
				if ( !is_bool($module) && $module['controllersLoaded'] )
				{
					$controllersFound = true;

					foreach ($module as $controller)
					{
						// Since actionsLoaded is a key, skip this one.
						if ( !is_bool($controller) && $controller['actionsLoaded'])
						{
							$actionsFound = true;
						}
					}
				}
			}
		}

		// Check so that atleast one controller and one action is found.
		if ( false === $controllersFound )
		{
			throw new Zend_Exception('No Controllers found.');
		}
		if ( false === $actionsFound )
		{
			throw new Zend_Exception('No Actions found.');
		}
	}

	/**
	 * Store the page resource tree into $this->pageResources.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @return	Acl_BuildResources $this
	 */
	public function buildResourceArray()
	{
		$this->_buildModulesArray( APPLICATION_PATH . '/modules', $this->_pageResources);
		return $this;
	}

	/**
	 * Loop through the modules folder and store module names in given storage.
	 * Passes each module to buildControllersArray function.
	 *
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param	string $path
	 * @param	array $storage
	 */
	private function _buildModulesArray( $path, array &$storage )
	{
		$moduleFolder = opendir( $path );
		// Load models
		while ( ($moduleName = readdir($moduleFolder) ) !== false )
		{
			// Skip ignored folders.
			if( ! in_array($moduleName, $this->_arrIgnore) )
			{
				if( is_dir($path . '/' . $moduleName) )
				{
					$storage['modulesLoaded'] = true;

					// Load module controllers.
					$storage[$moduleName] = array('controllersLoaded' => false );
					$this->_buildControllersArray($path, $moduleName, $storage[$moduleName]);
				}
			}
		}
		closedir($moduleFolder);
	}

	/**
	 * Loop through a module's controllers folder and store controller names in given storage.
	 * Passes each controller to buildActionsArray function.
	 *
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param 	string	$modulesPath
	 * @param 	string	$moduleName
	 * @param	array	$storage
	 */
	private function _buildControllersArray( $modulesPath, $moduleName, array &$storage )
	{
		// Load controllers
		$controllerFolder = opendir($controllersPath = $modulesPath. '/' . $moduleName . '/controllers' );

		while ( ($controllerName = readdir($controllerFolder) ) !== false )
		{
			// Skip ignored folders.
			if( !in_array($controllerName, $this->_arrIgnore))
			{
				if( preg_match( '/Controller/', $controllerName) )
				{
					// Skip Controller.php
					$controllerName = strtolower( substr( $controllerName,0,-14 ) );

					$storage['controllersLoaded'] = true;

					$storage[$controllerName] = array('actionsLoaded' => false );

					// Load controller actions.
					$this->_buildActionsArray($controllersPath, $moduleName, $controllerName, $storage[$controllerName]);
				}
			}
		}
		closedir($controllerFolder);
	}

	/**
	 * Loop through a controller's actions and store action names in given storage.
	 *
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param	string	$controllersPath
	 * @param	string	$moduleName
	 * @param	string	$controllerName
	 * @param	array	$storage
	 */
	private function _buildActionsArray( $controllersPath, $moduleName, $controllerName, array &$storage )
	{
		$className = ucfirst( $moduleName ).'_'.ucfirst( $controllerName . 'Controller' );

		if( ! class_exists( $className ) )
		{
			Zend_Loader::loadFile($controllersPath . '/'. ucfirst( $controllerName ) . 'Controller.php');
		}

		$objReflection = new Zend_Reflection_Class( $className );
		$arrMethods = $objReflection->getMethods();
		foreach( $arrMethods as $objMethods )
		{
			if( preg_match( '/Action/', $objMethods->name ) )
			{
				$storage['actionsLoaded'] = true;

				$storage[] = substr($this->_camelCaseToHyphens($objMethods->name),0,-6 );
			}
		}
	}

	/**
	 * Changes camel case to hyphens.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param string $string
	 */
	private function _camelCaseToHyphens($string)
	{
		if($string == 'currentPermissionsAction')
		{
			$found = true;
		}
		$length = strlen($string);
		$convertedString = '';
		for($i = 0; $i <$length; $i++)
		{
			if(ord($string[$i]) > ord('A') && ord($string[$i]) < ord('Z'))
			{
				$convertedString .= '-' .strtolower($string[$i]);
			}
			else
			{
				$convertedString .= $string[$i];
			}
		}
		return strtolower($convertedString);
	}

	/**
	 * Get resources as tree.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @return array
	 */
	public function getResourcesTree()
	{
		return $this->_pageResources;
	}

	/**
	 * Get the resource names in an array.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @return	array
	 */
	public function getNamesAsArray()
	{
		$resourceArray = array();
		if ( null === $this->_resourceTable )
		{
			$this->_resourceTable = new Acl_Db_Table_Resources();
		}
		$rowSet = $this->_resourceTable->fetchAll();
		foreach ($rowSet as $row)
		{
			$resourceArray[] = $row->getColumn('resource');
		}
		return $resourceArray;
	}

}
