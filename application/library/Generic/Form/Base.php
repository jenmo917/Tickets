<?php
/**
 * With inspiration from Phil Brown, this class decorates our forms as wanted.
 * @link	http://blog.philipbrown.id.au/2009/10/overriding-zend-form-element-default-decorators-for-good/
 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
 */
class Generic_Form_Base extends Zend_Form
{
	/** #@+ @access public */
	/** @var array Decorators to use for standard form elements */
	// these will be applied to our text, password, select, checkbox and radio elements by default
	public $elementDecorators = array(
		'ViewHelper',
		'Errors',
		array('Description', array('tag' => 'p', 'class' => 'description')),
		array('Label', array('class' => 'form-label', 'requiredSuffix' => ' *')),
		array('HtmlTag', array('tag' => 'li', 'class' => 'form-li')),
		//array('Decorator' => array('TopLabel' => 'Label'), array('tag' => 'li')),
	);

	/** @var array Decorators for File input elements */
	// these will be used for file elements
	public $fileDecorators = array(
		'File',
		'Errors',
		array('Description', array('tag' => 'p', 'class' => 'description')),
		array('HtmlTag',     array('tag' => 'li', 'class' => 'form-file-li')),
		array('Label',       array('class' => 'form-file-label', 'requiredSuffix' => ' *'))
	);

	/** @var array Decorator to use for standard for elements except do not wrap in HtmlTag */
	// this array gets set up in the constructor
	// this can be used if you do not want an element wrapped in a div tag at all
	public $elementDecoratorsNoTag = array();

	/** @var array Decorators for button and submit elements */
	// decorators that will be used for submit and button elements
	public static $buttonDecorators = array(
		'ViewHelper',
		array('HtmlTag', array('tag' => 'li', 'class' => 'form-button'))
	);
	
	static public $multiChoiseDecorators = array(
			'ViewHelper',
			'Errors',
			array(array('LiTag'	=> 'HtmlTag'), array('tag' => 'li')),
			array(array('OlTag'	=> 'HtmlTag'), array('tag' => 'ol', 'class' => 'multi-ol')),
			array('Description',	array('tag' => 'p', 'class' => 'description')),
			array('Label',			array('class' => 'form-label', 'requiredSuffix' => ' *')),
			array(array('WrapTag'	=> 'HtmlTag'), array('tag' => 'li', 'class' => 'form-li')),
			);
	/** #@- */

	/**
	 * Sets decorators for subforms currently in object.
	 * Unlike setElementDecorators, there is no default variable for subforms.
	 * Therefore, this function must be called after insertion of subforms to have any effect.
	 * @author	Daniel Josefsson <dannejosefsson@gmail.com>
	 * @since	v0.1
	 * @param	array $decorators Decorators that subforms should use.
	 * @uses	Generic_Form_SubForm_Base::getBaseDecorators()
	 */
	public function setSubFormDecorators(array $decorators)
	{
		$subformDecorators = (empty($decorators))?Generic_Form_SubForm_Base::getBaseDecorators():$decorators;
		$subformDecorators[] = array(array('outerWrap' => 'HtmlTag'), array('tag' => 'li', 'class' => 'form-subform-li'));
		parent::setSubFormDecorators($subformDecorators);
	}

	public function __construct($options = null)
	{
		//$this->addPrefixPath('Attend_Form', 'Attend/Form');
		// first set up the $elementDecoratorsNoTag decorator, this is a copy of our regular element decorators, but do not get wrapped in a div tag
		foreach($this->elementDecorators as $decorator) {
			if (is_array($decorator) && $decorator[0] == 'HtmlTag') {
				continue; // skip copying this value to the decorator
			}
			$this->elementDecoratorsNoTag[] = $decorator;
		}

		// set the decorator for the form itself.
		$this->setDecorators(array(
			array('FormElements', array('tag' => 'li', 'position' => 'prepend')),
			array('HtmlTag', array('tag' => 'ol', 'class' => 'form-ol')),
			'Form',
		));

		// set the default decorators to our element decorators, any elements added to the form
		// will use these decorators
		$this->setElementDecorators($this->elementDecorators);

		parent::__construct($options);
		// parent::__construct must be called last because it calls $form->init()
		// and anything after it is not executed
	}
}