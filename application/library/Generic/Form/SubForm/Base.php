<?php
/**
 * With inspiration from Phil Brown, this class decorates our subforms as wanted.
 * @link	http://blog.philipbrown.id.au/2009/10/overriding-zend-form-element-default-decorators-for-good/
 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
 */
class Generic_Form_SubForm_Base extends Zend_Form
{
	/** #@+ @access public*/

	/** @var array Decorators to use on self and on child subforms*/
	static public $subFormDecorators = array(
		'FormElements',
		array(array('innerWrap' => 'HtmlTag'), array('tag' => 'ol', 'class' => 'subform-ol')),
		'Fieldset');

	/** @var array Decorators to use for standard form elements */
	// these will be applied to our text, password, select, checkbox and radio elements by default.
	public $elementDecorators = array(
		'ViewHelper',
		'Errors',
		array('Description', array('tag' => 'p', 'class' => 'description')),
		array('Label',       array('class' => 'subform-label', 'requiredSuffix' => ' *')),
		array('HtmlTag',     array('tag' => 'li', 'class' => 'subform-li')),
	);

	/** @var array Decorators for File input elements */
	// these will be used for file elements, but it is not in use yet.
	public $fileDecorators = array(
		'File',
		'Errors',
		array('Description', array('tag' => 'p', 'class' => 'description')),
		array('Label',       array('class' => 'subform-label', 'requiredSuffix' => '*')),
		array('HtmlTag',     array('tag' => 'li', 'class' => 'subform-file-li')),
	);

	/** @var array Decorator to use for standard for elements except do not wrap in HtmlTag */
	// this array gets set up in the constructor
	// this can be used if you do not want an element wrapped in a div tag at all
	public $elementDecoratorsNoTag = array();

	/** @var array Decorators for button and submit elements */
	// decorators that will be used for submit and button elements
	public $buttonDecorators = array(
		'ViewHelper',
		array('HtmlTag', array('tag' => 'li', 'class' => 'subform-button'))
	);
	/** #@- */

	public function __construct($options = null)
	{
		// first set up the $elementDecoratorsNoTag decorator, this is a copy of our regular element decorators, but do not get wrapped in a div tag
		foreach($this->elementDecorators as $decorator) {
			if (is_array($decorator) && $decorator[0] == 'HtmlTag') {
				continue; // skip copying this value to the decorator
			}
			$this->elementDecoratorsNoTag[] = $decorator;
		}

		// set the decorator for the form itself.
		$this->setDecorators(self::getBaseDecorators());

		// set the default decorators to our element decorators, any elements added to the form
		// will use these decorators
		$this->setElementDecorators($this->elementDecorators);

		parent::__construct($options);
		// parent::__construct must be called last because it calls $form->init()
		// and anything after it is not executed
	}

	/**
	 * Returns the standard decorators.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @return	array Standard decoratiors.
	 */
	static public function getBaseDecorators()
	{
		return self::$subFormDecorators;
	}

	/**
	 * Sets decorators for subforms currently in object.
	 * Unlike setElementDecorators, there is no default variable for subforms.
	 * Therefore, this function must be called after insertion of subforms to have any effect.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param	array $decorators Decorators that subforms should use.
	 */
	public function setSubFormDecorators(array $decorators)
	{
		$subformDecorators = (empty($decorators))?self::getBaseDecorators():$decorators;
		$subformDecorators[] = array(array('outerWrap' => 'HtmlTag'), array('tag' => 'li', 'class' => 'form-subform-li'));
		parent::setSubFormDecorators($subformDecorators);
	}
}
