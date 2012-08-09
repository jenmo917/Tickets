<?php
class Acl_RoleTree
{
	protected	$_rolesTable = null;
	protected	$_rolesInheritancesTable = null;
	protected	$_rolesTree = array();

	/**
	 * Add Acl_Role to tree.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param Acl_Role $role
	 */
	private function addRole(Acl_Role $role)
	{
		return $this->_rolesTree[] = $role;
	}

	/**
	 * Builds a tree structure of roles where each role can have multiple inheritance.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @throws Zend_Exception
	 */
	public function buildFullTree()
	{
		$this->fetchRoles()->createInheritances();


		return $this;
	}

	/**
	 * Get the roles from the database and create Acl_Role objects of them. These are stored in $_rolesTree.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @return self
	 */
	private function fetchRoles()
	{
		// Get the role table.
		$this->_rolesTable = new Acl_Db_Table_Roles();
		$dbRoles = $this->_rolesTable->fetchAll();

		// Create Acl_Roles of each fetched row.
		foreach ($dbRoles as $dbRole)
		{
			// Check if the role is in the array. If not, add it.
			if(false === $this->findRole($dbRole->getColumn('roleId')))
			{
				$this->addRole(new Acl_Role($dbRole));
			}
		}
		return $this;
	}

	/**
	 * Fetch inheritances from the database and structure $_rolesTree according to this.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @throws Zend_Exception
	 * @return	self
	 */
	private function createInheritances()
	{
		// Get the role inheritances and put them into the objects.
		$this->_rolesInheritancesTable = new Acl_Db_Table_RolesInheritances();
		$dbInheritances = $this->_rolesInheritancesTable->fetchAll();

		$childRole	= false;
		$parentRole = false;
		// Placeholder for each child role, so those can be removed from the tree root.
		$arrChildRoles = array();

		foreach ($dbInheritances as $dbInheritance)
		{
			if (	($childRole = $this->findRole($roleId = $dbInheritance->getColumn('roleId'))) instanceof Acl_Role &&
			($parentRole = $this->findRole($parentRoleId = $dbInheritance->getColumn('parentRoleId'))) instanceof Acl_Role )
			{
				//Do checks so that the roles are not round inheriting.
				if ( $childRole->hasChildRecursively($parentRoleId) || $parentRole->hasParentRecursively($roleId))
				{
					throw new Zend_Exception('Role '. $roleId . ' and '. $parentRoleId . ' has a round inheritance in the data base.');
				}
				else
				{
					//Link the roles together.
					$childRole->addParent($parentRole);
					$parentRole->addChild($childRole);
					$arrChildRoles[] = $roleId;
				}
			}
			// If none of the roles are not found, throw exceptions.
			else if (!($childRole instanceof Acl_Role) && ($parentRole instanceof Acl_Role ))
			{
				throw new Zend_Exception('Role with ID of '. $roleId . ' not found.');
			}
			else if (($childRole instanceof Acl_Role) && !($parentRole instanceof Acl_Role ))
			{
				throw new Zend_Exception('Parent role with ID of '. $parentRoleId . ' not found.');
			}
			else
			{
				throw new Zend_Exception('Role with ID of '. $roleId . ', neither parent role with ID of '. $parentRoleId . ' were found.');
			}
		}
		// Clean up the references in the root
		$this->removeRoles($arrChildRoles);
		return $this;
	}

	/**
	 * Find a role inside the three. The search is done in each node. Returns false if not found.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param	string $id
	 * @return	Acl_Role|boolean
	 */
	public function findRole($id)
	{
		// Search the root first.
		//TODO: Make as function.
		foreach ($this->_rolesTree as $role)
		{
			if ( $id === $role->getRoleId() )
			{
				return $role;
			}
		}

		$theChild = false;
		foreach ($this->_rolesTree as $role)
		{
			$theChild = $role->getChildRecursively($id);
			if ( $theChild instanceof Acl_Role )
			{
				return $theChild;
			}
		}
		return $theChild;
	}

	/**
	 * Check if  a role is inside the three. The search is done in each node.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param	string $id
	 * @return	boolean
	 */
	public function hasRole($id)
	{
		// Search the root first.
		foreach ($this->_rolesTree as $role)
		{
			if ( $id === $role->getRoleId() )
			{
				return true;
			}
		}
		// Then search within its childs, if neccesary.
		foreach ($this->_rolesTree as $role)
		{
			if ( $role->hasChildRecursively($id) )
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Remove roles from the root according to $arrIds.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param	array $arrIds
	 * @return boolean
	 */
	private function removeRoles($arrIds)
	{
		// Make sure that the parameter array is unique.
		$arrIds = array_unique($arrIds);
		foreach ($this->_rolesTree as $key => $role)
		{
			if ( in_array($id = $role->getRoleId(), $arrIds ) )
			{
				unset($this->_rolesTree[$key]);
				$arrIds = array_diff($arrIds, array($id));
			}
		}
		return (empty($arrIds))? true: false;
	}

	/**
	 * Get all roles as an array where the order is so that all parents are before their childs.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @return	array
	 */
	public function getRoleList()
	{
		$theList = array();
		foreach ($this->_rolesTree as $role)
		{
			$role->getList($theList);
		}
		return $theList;
	}
}
