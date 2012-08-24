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
	protected $_numOfTickets = 0;

	/**
	 * Set event ID
	 * @author	Jens Moser <jenmo917@gmail.com>
	 * @since	v0.1
	 * @return	null
	 */
	public function setEventId($eventId)
	{
		$eventIdElementName		= Attend_Db_Table_Row_Event::getColumnNameForUrl('eventId', '_');
		$hidden = $this->getSubForm(self::STEP_1)->getElement($eventIdElementName);
		$hidden->setValue($eventId);
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
		// Form element names
		$formNames = Attend_Db_Table_Row_Event::getColumnNames('both', '_');

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

		/*
		 * STEP 1
		*/
		// Add name
		$step1->addElement('text', $formNames['name'], array(
					'label'			=> $this->_translator->translate('Name'),
					'required'		=> true,
					'filters'		=> array('StringTrim','StripTags'),
					'validators'	=> array('notEmpty'),
					'errorMessages'	=> array($this->_translator->translate('Name please').'.')
		));

		// Add location
		$step1->addElement('text', $formNames['location'], array(
					'label'			=> $this->_translator->translate('Location'),
					'required'		=> true,
					'filters'		=> array('StringTrim','StripTags'),
					'validators'	=> array('notEmpty'),
					'errorMessages'	=> array($this->_translator->translate('Location please').'.')
		));

		// Add start time
		$step1->addElement('text', $formNames['startTime'], array(
					'label'			=> $this->_translator->translate('Event starts'),
					'required'		=> true,
					'filters'		=> array('StringTrim','StripTags'),
					'errorMessages'	=> array($this->_translator->translate('Start time please').'.'),
					'class'			=> array('date-pick')
		));

		// Add end time
		$step1->addElement('text', $formNames['endTime'], array(
					'label'			=> $this->_translator->translate('Event ends'),
					'required'		=> true,
					'filters'		=> array('StringTrim','StripTags'),
					'errorMessages'	=> array($this->_translator->translate('End time please').'.'),
					'class'			=> array('date-pick')
		));

		// Add details
		$step1->addElement('textarea', $formNames['details'], array(
					'label'			=> $this->_translator->translate('Details'),
					'required'		=> false,
					'filters'		=> array('StringTrim','StripTags'),
					'errorMessages'	=> array(),
					'cols'			=> '130',
					'rows'			=> '2',
		));

		$step1->addElement('hidden', $formNames['eventId'], array(
					'value'			=> null));

		/*
		 * STEP 2
		*/
		// Add default ticket and increment the ticket count.
		$ticketType = new Admin_Form_SubForm_TicketType(array(
					'name'				=> $this->_numOfTickets,
					'elementsBelongTo'	=> $this->_numOfTickets,
					'order'				=> $this->_numOfTickets + 1,
		));
		$step2->addSubForm($ticketType, $this->_numOfTickets);

		$this->_numOfTickets++;


		// Add submit button. This is used by Jquery to add more ticket type subforms
		$step2->addElement(	'submit', self::NEW_TICKET_TYPE_SUBMIT, array(
					'label' => $this->_translator->translate('New Ticket Type'),
					'order' => $this->_numOfTickets + 1000));
		//TODO: The dynamic order of this button is not recognized by the form.

		/*
		 *  STEP 3
		*/

		// Add public
		$step3->addElement(	'select', $formNames['public'],array(
					'label' => $this->_translator->translate('Publicize, or keep it private'),
					'multiOptions' => array(
						"1" => $this->_translator->translate('Public'),
						"0" => $this->_translator->translate('Private'))
		));

		// Add main submit button
		$this->addElement('submit', self::SAVE_EVENT_SUBMIT, array('label' => $this->_translator->translate('Save Event')));
	}

	public function addTicketType()
	{
		$step2 = $this->getSubForm(self::STEP_2);
		$step2->getElement(self::NEW_TICKET_TYPE_SUBMIT)->setOrder($this->_numOfTickets + 2);
		$ticketType = new Admin_Form_SubForm_TicketType(array(
				'name' => $this->_numOfTickets,
				'elementsBelongTo' => $this->_numOfTickets,
				'order' => $this->_numOfTickets + 1
		));
		$step2->addSubForm($ticketType, $this->_numOfTickets );
		$this->_numOfTickets++;
		$ticketType->removeButtonDisabled(false);
		$step2->getSubForm(0)->removeButtonDisabled(false);
	}

	public function removeTicketType($subFormIndex)
	{
		$step2 = $this->getSubForm(self::STEP_2);
		$remove = $step2->removeSubForm($subFormIndex);
		if( $remove )
		{
			$maxIndex = $this->_numOfTickets--;
			// Update order.
			for ($i = $subFormIndex + 1; $i < $maxIndex; $i ++)
			{
				$step2->getSubForm($i)->setOrder($i+1);
			}
			if (1 === $this->_numOfTickets)
			{
				$subForms = $step2->getSubForms();
				$onlySubForm = current($subForms);
				$onlySubForm->removeButtonDisabled(true);
			}
			elseif (0 === $this->_numOfTickets)
			{
				$this->addTicketType();
			}
			return true;
		}
		else
			return false;
	}
}
