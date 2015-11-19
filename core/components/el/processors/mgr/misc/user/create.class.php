<?php

require_once MODX_CORE_PATH . 'model/modx/processors/security/user/create.class.php';

class modelUserCreateProcessor extends modUserCreateProcessor
{

	public $classKey = 'modUser';
	public $languageTopics = array('core:default', 'core:user');
	public $permission = '';
	public $beforeSaveEvent = 'OnBeforeUserFormSave';
	public $afterSaveEvent = 'OnUserFormSave';

	public function initialize()
	{
		return parent::initialize();
	}

	/**
	 * Override in your derivative class to do functionality before the fields are set on the object
	 * @return boolean
	 */
	public function beforeSet()
	{
		$q = $this->modx->newQuery('modUserProfile', array('email' => $this->getProperty('email')));
		if ($this->modx->getCount('modUserProfile', $q)) {
			$this->addFieldError('email', $this->modx->lexicon('user_err_not_specified_email'));
		}

		$this->setProperty('passwordnotifymethod', $this->getProperty('passwordnotifymethod', 's'));
		return parent::beforeSet();
	}

	/**
	 * {@inheritDoc}
	 * @return boolean
	 */
	public function beforeSave()
	{
		$this->addProfile();

		$this->validator = new modUserValidation($this, $this->object, $this->profile);
		$this->validator->validate();

		return !$this->hasErrors();
	}

	/**
	 * Add User Group memberships to the User
	 * @return array
	 */
	public function setUserGroups()
	{
		$memberships = array();
		$groups = $this->getProperty('groups', '');
		$groups = explode(',', $groups);
		if (count($groups) > 0) {
			$groupsAdded = array();
			$idx = 0;
			foreach ($groups as $tmp) {
				@list($group, $role) = explode(':', $tmp);
				if (in_array($group, $groupsAdded)) {
					continue;
				}
				if (empty($role)) {
					$role = 1;
				}
				if ($tmp = $this->modx->getObject('modUserGroup', array('name' => $group))) {
					$gid = $tmp->get('id');
					/** @var modUserGroupMember $membership */
					$membership = $this->modx->newObject('modUserGroupMember');
					$membership->set('user_group', $gid);
					$membership->set('role', $role);
					$membership->set('member', $this->object->get('id'));
					$membership->set('rank', $idx);
					$membership->save();
					$memberships[] = $membership;
					$groupsAdded[] = $group;
					$idx++;
				}
			}
		}
		return $memberships;
	}

	/**
	 * {@inheritDoc}
	 * @return boolean
	 */
	public function afterSave()
	{
		$this->setUserGroups();
		return true;
	}

}

return 'modelUserCreateProcessor';