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
			self::$_objAcl = new Zend_Acl();
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
		$settingsKeys = array_keys($settings);
		$mustHave =	array(	Acl_Db_Table_Privileges::getColumnName('userId'),
							Acl_Db_Table_Privileges::getColumnName('roleId'));
		$mayHave =	Acl_Db_Table_Privileges::getColumnNames();
		$mustHaveDiff = array_diff($mustHave, $settingsKeys);
		$mayHaveDiff = array_diff($settingsKeys, $mayHave);
		if ( !empty($mustHaveDiff) )
		{
			$mustHaveKeysString = implode(', ', $mustHave);
			throw new Zend_Exception($mustHaveKeysString . ' have to be keys in settings');
		}

		if ( !empty($mayHaveDiff) )
		{
			$mayHaveKeysString = implode(', ', $mayHave);
			throw new Zend_Exception('Only '.$mayHaveKeysString . ' can be keys in settings');
		}
		$permissionsTable = new Acl_Db_Table_Privileges();
		return $permissionsTable->createRow($settings)->save();
	}

	public static function getPrivilegesForUser( $userId )
	{
		$privilegesTable = new Acl_Db_Table_Privileges();
		return $privilegesTable->getPrivilegesForUserId($userId, true);
	}

	public static function clearCache()
	{
		self::$_cache->remove('acl');
	}
}
