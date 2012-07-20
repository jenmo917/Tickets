<?PHP

class Generic_HtmlMailer extends Zend_Mail
{
    /**
    * The from name that appears in the receivers mail box
    * @var string
    */      
    static $fromName  = "TDDD27";

    /**
    * The from email that appears in the receivers mail box
    * @var string
    */    
    static $fromEmail = "noreply@beat12.se";
    
    /*
     * @var Zend_View
     */
    static $_defaultView;
    
    /*
     * Current instance of our Zend_View
     * @var Zend_View
     */
    protected $_view;

    /*
    * Returns the default view and script path is choosen
    * @author	Jens Moser <jenmo917@gmail.com>
    * @since	v0.1
    * @return	Zend_View
    */ 
    protected static function getDefaultView()
    {
        if(self::$_defaultView === null)
        {
            self::$_defaultView = new Zend_View();
            self::$_defaultView->setScriptPath(APPLICATION_PATH . '/mail-templates');
        }
        return self::$_defaultView;
    }
    
    /*
    * Set view param that can be used in the mail template
    * @author	Jens Moser <jenmo917@gmail.com>
    * @since	v0.1
    * @return	Zend_View
    */     
    public function setViewParam($property, $value)
    {
        $this->_view->__set($property,$value);
        return $this;
    }

    /*
    * Set event ID
    * @author	Jens Moser <jenmo917@gmail.com>
    * @since	v0.1
    * @return	null
    */
    public function sendHtmlTemplate($template, $encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE)
    {
        $html = $this->_view->render($template);
        $this->setBodyHtml($html,$this->getCharset(),$encoding);
        $this->send();
    }

    /*
    * Constructor
    * @author	Jens Moser <jenmo917@gmail.com>
    * @since	v0.1
    * @return	Zend_View
    */     
    public function __construct($charset = 'utf-8')
    {
        parent::__construct($charset);
        $this->setFrom(self::$fromEmail, self::$fromName);
        $this->_view = self::getDefaultView();
    }
}