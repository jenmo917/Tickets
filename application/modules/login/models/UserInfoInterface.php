<?php
interface Login_Model_UserInfoInterface
{
	static public function getServiceName();
	public function logout(&$serviceArray, $userId = null, $redirect = null);
	public function getLoginUrl();
	public function authenticate($identifier);
	public function addAccount($userId, $identifier);
	static public function getDefaultCategories();
	public function isLoggedIn(array $serviceArray, array $identifiers = array());
	//public function getNewUserRoleNames();
}
