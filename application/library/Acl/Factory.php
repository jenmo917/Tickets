<?php
class Acl_Factory
{
	private static $_sessionNameSpace = 'Attend_ACL';
	private static $_objAcl;
	private static $_cache;

	/**
	 * Get the ACL object.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param Zend_Auth $objAuth
	 * @param bool $clearACL
	 */
	public static function get($clearACL = false)
	{
		self::$_cache = Zend_Registry::get('AclCache');
		if($clearACL)
		{
			self::clearCache();
		}

		return self::_loadAclFromCache();
	}

	/**
	 * Load ACL from db.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v
	 */
	private static function _loadAclFromCache()
	{
		if(!$result = self::$_cache->load('acl'))
		{
			self::$_objAcl = new Acl_Acl();
			// Fetch roles.
			$roleTree = new Acl_RoleTree();
			$roleTree->buildFullTree();
			foreach ($roleTree->getRoleList() as $role)
			{
				self::$_objAcl->addRole($role, $role->getParentIds());
			}
			// Get resources from application structure and from db resources.
			$resources = new Acl_Resources();
			//Update the resource list and fetch the resource names as an array.
			$resourcesArray = $resources->buildResourceArray()->writePageResourcesToDB()->getNamesAsArray();
			foreach ($resourcesArray as $resource)
			{
				self::$_objAcl->add(new Zend_Acl_Resource($resource));
			}
			// Fetch permissions.
			$permissionsTable = new Acl_Db_Table_Permissions();
			$permissions = $permissionsTable->getPermissionsArrayWithResourceNames();
			// Fetch column names that will be keys in permissions array.
			$dbnPermission = $permissionsTable->getColumnName('permission');
			$dbnAssertion = $permissionsTable->getColumnName('assertion');
			$dbnRoleId = $permissionsTable->getColumnName('roleId');
			$dbnResource = Acl_Db_Table_Resources::getColumnName('resource');
			// Store the permissions.
			foreach ($permissions as $permission)
			{
				if ( !is_null($permission[$dbnAssertion]) )
				{
					$assertion = new $permission[$dbnAssertion]();
				}
				else
				{
					$assertion = null;
				}
				// $permission[$dbnPermission] => allow or deny.
				self::$_objAcl->allow($permission[$dbnRoleId], $permission[$dbnResource], $permission[$dbnPermission], $assertion);
			}

			self::$_cache->save(serialize(self::$_objAcl), 'acl');

		}
		else
		{
			self::$_objAcl = unserialize($result);
		}
		return self::$_objAcl;
	}

	public static function addPrivilegeToStorage( $settings )
	{
		// Check input
		if ( !is_array($settings) )
			throw new Zend_Exception('$settings must be an array');
		$settingsKeys = array_keys($settings);
		$mustHave =	array(	Acl_Db_Table_Privileges::getColumnName('userId'),
							Acl_Db_Table_Privileges::getColumnName('roleId'));
		$mayHave =	Acl_Db_Table_Privileges::getColumnNames();
		$mustHaveDiff = array_diff($mustHave, $settingsKeys);
		$mayHaveDiff = array_diff($settingsKeys, $mayHave);
		if ( !empty($mustHaveDiff) )
		{
			$mustHaveKeysString = implode(', ', $mustHaveDiff);
			throw new Zend_Acl_Exception('$settings lacks following keys: '. $mustHaveKeysString);
		}

		if ( !empty($mayHaveDiff) )
		{
			$mayHaveKeysString = implode(', ', $mayHave);
			throw new Zend_Exception('Only '.$mayHaveKeysString . ' can be keys in $settings');
		}
		$permissionsTable = new Acl_Db_Table_Privileges();
		return $permissionsTable->createRow($settings)->save();
	}

	public static function getPrivilegesForUser( $userId )
	{
		$privilegesTable = new Acl_Db_Table_Privileges();
		return $privilegesTable->getPrivilegesForUserId($userId, true);
	}

	/**
	 * Add default privileges to user on given categories.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param	string	$userId
	 * @param	array	$categories
	 * @todo	Limit so the user cant get serveral of the same privileges.
	 * 			As for now, the user will get several equal service default privileges.
	 * @throws	Zend_Acl_Exception if $categories is not an array or a string.
	 * @throws	Zend_Acl_Exception if privilegeSettings is not an array.
	 * @throws	Zend_Acl_Exception if user id column is not found in privilegeSettings.
	 */
	public static function addDefaultPrivileges( $privilegeSettings, $categories )
	{
		// Check input
		if (!is_array($categories) && !is_string($categories))
			throw new Zend_Acl_Exception('$categories must be an array or a string');
		if ( !is_array($privilegeSettings) )
		throw new Zend_Acl_Exception('$privilegeSettings must be an array');
		$settingsKeys = array_keys($privilegeSettings);

		// make sure that user id is set.
		$mustHave =	array(	Acl_Db_Table_Privileges::getColumnName('userId'));
		$mustHaveDiff = array_diff($mustHave, $settingsKeys);
		if ( !empty($mustHaveDiff) )
		{
			$mustHaveKeysString = implode(', ', $mustHave);
			throw new Zend_Acl_Exception('$privilegeSettings lacks following keys: '. $mustHaveKeysString);
		}

		// Make sure that $categories is an array.
		if ( is_string($categories) )
			$categories = array($categories);

		$defaultPrivileges = new Acl_Db_Table_DefaultPrivileges();
		$roleIdColName = $defaultPrivileges->getColumnName('roleId');
		$roleIdPrivilegeColName = Acl_Db_Table_Privileges::getColumnName('roleId');

		//Fetch all privileges that should be set to the user.
		$privilegesArray = $defaultPrivileges->getCategoriesPrivileges($categories);
		foreach ($privilegesArray as $privilege)
		{
			$privilegeSettings[$roleIdPrivilegeColName] = $privilege[$roleIdColName];
			// Store the privilege.
			self::addPrivilegeToStorage($privilegeSettings);
		}
	}

	public static function clearCache()
	{
		self::$_cache->remove('acl');
	}
}
