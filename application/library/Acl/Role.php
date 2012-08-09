<?php
class Acl_Role implements Zend_Acl_Role_Interface
{
	protected	$_id		= null;
	protected	$_name		= null;
	protected	$_parents	= array();
	protected	$_childs	= array();
	protected	$_created	= null;
	protected	$_updated	= null;

	/**
	 * Parameterizes Acl_Db_Table_Row_Role and extends it so it can
	 * store both parent and child inheritances.
	 *
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param Acl_Db_Table_Row_Role $role
	 */
	public function __construct(Acl_Db_Table_Row_Role $role)
	{
		$arrRole = $role->toArray();
		$this->_id = $role->getColumn('roleId');
		$this->_name = $role->getColumn('roleName');
		$this->_created = $role->getColumn('created');
		$this->_updated = $role->getColumn('updated');
		return $this;
	}

	/**
	 * Add parent to this role.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param	Acl_Role $aclChildRole
	 * @return	Acl_Role $this
	 */
	public function addParent( Acl_Role $aclChildRole )
	{
		$this->_parents[] = $aclChildRole;
		return $this;
	}

	/**
	 * Add child to this role.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param	Acl_Role $aclParentRole
	 * @return Acl_Role $this
	 */
	public function addChild( Acl_Role $aclParentRole )
	{
		$this->_childs[] = $aclParentRole;
		return $this;
	}

	/**
	 * Get registered childs.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @return array:
	 */
	public function getChilds()
	{
		return $this->_childs;
	}

	/**
	 * Get child specified on id.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param string $id
	 */
	public function getChild($id)
	{
		$theChild = false;
		foreach ($this->_childs as $child)
		{
			if($id === $child->getRoleId())
			{
				$theChild = &$child;
				break;
			}
		}
		return $theChild;
	}

	/**
	 * Get child recursevely.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param string $id
	 * @return Acl_Role|boolean
	 */
	public function getChildRecursively($id)
	{
		$theChild = false;
		$theChild = $this->getChild($id);
		if ( $theChild instanceof self )
		{
			return $theChild;
		}
		//If $theChild still is not found, check if at least one of this role's childs has that role as a child.
		foreach ($this->_childs as $child)
		{
			$recursiveResult = $child->getChildRecursively($id);
			if($recursiveResult instanceof self)
			{
				$theChild = &$recursiveResult;
			}
		}
		return $theChild;
	}

	/**
	 * Check if it has a child with id $id.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param	string $id
	 * @return	boolean
	 */
	public function hasChild($id)
	{
		foreach ($this->_childs as $child)
		{
			if($id === $child->getRoleId())
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Does a nestled search for a child with a specific id.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param	string $id
	 * @return boolean
	 */
	public function hasChildRecursively($id)
	{
		if($this->hasChild($id))
		{
			return true;
		}
		//If $theChild still is not found, check if at least one of this role's childs has that role as a child.
		foreach ($this->_childs as $child)
		{
			if(true === $child->hasChildRecursively($id))
			{
				return true;
			}
		}
		return false;
	}

	/**
	* Check if it has a parent with id $id.
	* @author	Daniel Josefsson <dannejosefsson@gmail.com>
	* @since	v0.1
	* @param	string $id
	* @return	boolean
	*/
	public function hasParent($id)
	{
		foreach ($this->_parents as $parent)
		{
			if($id === $parent->getRoleId())
			{
				return true;
			}
		}
		return false;
	}

	/**
	* Does a nestled search for a parent with a specific id.
	* @author	Daniel Josefsson <dannejosefsson@gmail.com>
	* @since	v0.1
	* @param	string $id
	* @return boolean
	*/
	public function hasParentRecursively($id)
	{
		if($this->hasParent($id))
		{
			return true;
		}
		//If $theParent still is not found, check if at least one of this role's childs has that role as a child.
		foreach ($this->_parents as $parent)
		{
			if(true === $parent->hasParentRecursively($id))
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Recursive function that adds $roleId => $parentIdsArray to $theList if all its parents is in it and thereafter calls itself on its childs.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param	array $theList
	 */
	public function getList( &$theList )
	{
		//Check so that all its parents is already in the list.
		$parents = $this->getParentIds();
		$rolesInList = array_keys($theList);
		$parentsNotInList = array_diff($parents, $rolesInList);
		if ( empty($parentsNotInList) )
		{
			$theList[$this->getRoleId()] = &$this;
			foreach ($this->_childs as $child)
			{
				$child->getList($theList);
			}
		}
	}

	/**
	 * Return an array containing parent ids.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @return array
	 */
	public function getParentIds()
	{
		$parents = array();
		foreach ($this->_parents as $parent)
		{
			$parents[] = $parent->getRoleId();
		}
		return $parents;
	}

	/**
	 * Get ID.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @return string
	 */
	public function getRoleId()
	{
		return $this->_id;
	}
}
