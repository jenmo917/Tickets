<?php

class Admin_Form_EventInfo extends Generic_Form_Base
{
	const SAVE_EVENT_SUBMIT			= 'save_event';
	const NEW_TICKET_TYPE_SUBMIT	= 'new_ticket_type';

	const STEP_1	= 'step1';
	const STEP_2	= 'step2';
	const STEP_3	= 'step3';
	/**
	 * Instance of Zend_Translate
	 * @var Zend_Translate $_translator
	 */
	protected $_translator;

	/**
	 * Set event ID
	 * @author	Jens Moser <jenmo917@gmail.com>
	 * @since	v0.1
	 * @return	null
	 */
	public function setEventId($eventId)
	{
		$hidden = $this->createElement('hidden', 'event_id');
		$hidden->setValue($eventId);
		$this->addElement($hidden);
	}

	/**
	 * Init default translator
	 * @author	Jens Moser <jenmo917@gmail.com>
	 * @since	v0.1
	 * @return	null
	 */
	public function init()
	{
		$this->_translator = $this->getTranslator();
		$this->setName('EventInfo');
	}

	/**
	 * This function add all form elements
	 * @author	Jens Moser <jenmo917@gmail.com>
	 * @since	v0.1
	 * @return	null
	 */
	public function create($numOfTicketTypes)
	{
		// Form element names
		$nameElementName		= Attend_Db_Table_Row_Event::getColumnNameForUrl('name', '_');
		$locationElementName	= Attend_Db_Table_Row_Event::getColumnNameForUrl('location', '_');
		$startTimeElementName	= Attend_Db_Table_Row_Event::getColumnNameForUrl('startTime', '_');
		$endTimeElementName		= Attend_Db_Table_Row_Event::getColumnNameForUrl('endTime', '_');
		$detailsElementName		= Attend_Db_Table_Row_Event::getColumnNameForUrl('details', '_');
		$publicElementName		= Attend_Db_Table_Row_Event::getColumnNameForUrl('public', '_');

		/*
		 * Overall actions
		*/
		// Set up step sub forms.
		$step1 = new Generic_Form_SubForm_Base(array(
			'name' =>			self::STEP_1,
			'elementsBelongTo'	=> self::STEP_1,
			'legend'			=> $this->_translator->translate('Step 1 - Add your Event Details')
		));
		$step2 = new Generic_Form_SubForm_Base(array(
			'name' => self::STEP_2,
			'elementsBelongTo' => self::STEP_2,
			'legend'			=> $this->_translator->translate('Step 2 - Create Tickets')
		));
		$step3 = new Generic_Form_SubForm_Base(array(
			'name'				=> self::STEP_3,
			'elementsBelongTo'	=> self::STEP_3,
			'legend'			=> $this->_translator->translate('Step 3 - Promote your Event Page')
		));

		// Attach sub forms to main form
		$this->addSubForms(array(
			self::STEP_1 => $step1,
			self::STEP_2 => $step2,
			self::STEP_3 => $step3
		));
		$this->setSubFormDecorators(array());

		/*
		 * STEP 1
		*/
		// Add name
		$step1->addElement('text', $nameElementName, array(
			'label'			=> $this->_translator->translate('Name'),
			'required'		=> true,
			'filters'		=> array('StringTrim','StripTags'),
			'validators'	=> array('notEmpty'),
			'errorMessages'	=> array($this->_translator->translate('Name please').'.')
		));

		// Add location
		$step1->addElement('text', $locationElementName, array(
			'label'			=> $this->_translator->translate('Location'),
			'required'		=> true,
			'filters'		=> array('StringTrim','StripTags'),
			'validators'	=> array('notEmpty'),
			'errorMessages'	=> array($this->_translator->translate('Location please').'.')
		));

		// Add start time
		$step1->addElement('text', $startTimeElementName, array(
			'label'			=> $this->_translator->translate('Event starts'),
			'required'		=> true,
			'filters'		=> array('StringTrim','StripTags'),
			'errorMessages'	=> array($this->_translator->translate('Start time please').'.'),
			'class'			=> array('date-pick')
		));

		// Add end time
		$step1->addElement('text', $endTimeElementName, array(
			'label'			=> $this->_translator->translate('Event ends'),
			'required'		=> true,
			'filters'		=> array('StringTrim','StripTags'),
			'errorMessages'	=> array($this->_translator->translate('End time please').'.'),
			'class'			=> array('date-pick')
		));

		// Add details
		$step1->addElement('textarea', $detailsElementName, array(
			'label'			=> $this->_translator->translate('Details'),
			'required'		=> false,
			'filters'		=> array('StringTrim','StripTags'),
			'errorMessages'	=> array(),
			'cols'			=> '130',
			'rows'			=> '2',
		));

		/*
		 * STEP 2
		*/
		// Add ticket types
		for ($i = 0; $i < $numOfTicketTypes; $i++)
		{
			$name = 'ticketType_'.$i;
			$ticketType = new Admin_Form_SubForm_TicketType(array(
				'name' => $name,
				'elementsBelongTo' => $name,
				'order' => $i+1
				));

			$step2->addSubForm($ticketType, $name);
		}
		$step2->setSubFormDecorators(array());

		// Add submit button. This is used by Jquery to add more ticket type subforms
		$step2->addElement(	'submit', self::NEW_TICKET_TYPE_SUBMIT, array(
							'label' => $this->_translator->translate('New Ticket Type'),
							'order' => $numOfTicketTypes + 1));

		/*
		 *  STEP 3
		*/

		// Add public
		$step3->addElement(	'select', $publicElementName,array(
			'label' => $this->_translator->translate('Publicize, or keep it private'),
			'multiOptions' => array(
				"1" => $this->_translator->translate('Public'),
				"0" => $this->_translator->translate('Private'))
		));

		// Add main submit button
		$this->addElement('submit', self::SAVE_EVENT_SUBMIT, array('label' => $this->_translator->translate('Save Event')));
	}
}
