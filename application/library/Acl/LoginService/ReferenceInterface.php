<?php
interface Acl_LoginService_ReferenceInterface
{
	/**
	 * Find a user with given identifier connected.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param	string $identifier
	 * @return	bool|array
	 */
	public function findByIdentifier($identifier);
	public function storeAccount($userId, $identifier);
}