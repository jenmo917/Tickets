<?php

class Admin_Form_EventInfo extends Generic_Form_Base
{
	const SAVE_EVENT_SUBMIT			= 'save_event';
	const NEW_TICKET_TYPE_SUBMIT	= 'new_ticket_type';
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
	}

	/**
	 * This function add all form elements
	 * @author	Jens Moser <jenmo917@gmail.com>
	 * @since	v0.1
	 * @return	null
	 */
	public function create($numOfTicketTypes)
	{
		/*
		 * STEP 1
		*/
		$step1 = new Generic_Form_SubForm_Base('step1');

		// Add name
			$nameElementName =
		$step1->addElement('text', 'name', array(
			'label'			=> $this->_translator->translate('Name'),
			'required'		=> true,
			'filters'		=> array('StringTrim','StripTags'),
			'validators'	=> array('notEmpty'),
			'errorMessages'	=> array($this->_translator->translate('Name please').'.')
		));

		// Add location
		$step1->addElement('text', 'location', array(
			'label'			=> $this->_translator->translate('Location'),
			'required'		=> true,
			'filters'		=> array('StringTrim','StripTags'),
			'validators'	=> array('notEmpty'),
			'errorMessages'	=> array($this->_translator->translate('Location please').'.')
		));

		// Add start time
		$step1->addElement('text', 'start_time', array(
			'label'			=> $this->_translator->translate('Event starts'),
			'required'		=> true,
			'filters'		=> array('StringTrim','StripTags'),
			'errorMessages'	=> array($this->_translator->translate('Start time please').'.'),
			'class'			=> array('date-pick')
		));

		// Add end time
		$step1->addElement('text', 'end_time', array(
			'label'			=> $this->_translator->translate('Event ends'),
			'required'		=> true,
			'filters'		=> array('StringTrim','StripTags'),
			'errorMessages'	=> array($this->_translator->translate('End time please').'.'),
			'class'			=> array('date-pick')
		));

		// Add details
		$step1->addElement('textarea', 'details', array(
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
		$step2 = new Generic_Form_SubForm_Base('step2');
		$i = 0;
		// Add ticket types
		while($i < $numOfTicketTypes)
		{
			$ticketType = new Admin_Form_SubForm_TicketType();
			$ticketType->setDefault('order',$i);
			$step2->addSubForm($ticketType, $i);
			$i++;
		}
		$step2->setSubFormDecorators(array(
			'FormElements',
			'Fieldset'
		));

		// Add submit button. This is used by Jquery to add more ticket type subforms
		$step2->addElement(	'submit', self::NEW_TICKET_TYPE_SUBMIT, array(
							'label' => $this->_translator->translate('New Ticket Type')));

		/*
		 *  STEP 3
		*/
		// Initiate subform
		$step3 = new Generic_Form_SubForm_Base();

		// Add public
		$step3->addElement(	'select', 'public',array(
			'label' => $this->_translator->translate('Publicize, or keep it private'),
			'multiOptions' => array(
				"1" => $this->_translator->translate('Public'),
				"0" => $this->_translator->translate('Private'))
		));

		/*
		 * Overall actions
		*/
		// Set legends
		$step1->setLegend($this->_translator->translate('Step 1 - Add your Event Details'));
		$step2->setLegend($this->_translator->translate('Step 2 - Create Tickets'));
		$step3->setLegend($this->_translator->translate('Step 3 - Promote your Event Page'));

		// Attach sub forms to main form
		$this->addSubForms(array(
			'step1'  => $step1,
			'step2'  => $step2,
			'step3'  => $step3
		));
		$this->setSubFormDecorators(array(
			'FormElements',
			'Fieldset'
		));

		// Add main submit button
		$this->addElement('submit', self::SAVE_EVENT_SUBMIT, array('label' => $this->_translator->translate('Save Event')));
	}
}
