<?PHP
class Plugins_LangLoader extends Zend_Controller_Plugin_Abstract
{
    /*
    * Init translation and give forms and views access to the translation object
    * @author	Jens Moser <jenmo917@gmail.com>
    * @since	v0.1
    * @return	null
    */         
   public function preDispatch(Zend_Controller_Request_Abstract $request)
   {
        // Get the current URI
        $currentUri = $request->getRequestUri();

        // Explode URI
        $explodedUri = explode('/',$currentUri);
        
        // Get language
        $lang = $explodedUri[1];
        
        // Filter and remove slashes if any?
        $filter = new Zend_Filter_BaseName();
        $lang = $filter->filter($lang);

        // Translation file path
        $filename = APPLICATION_PATH.'/lang/lang.'.$lang.'.mo';

        // Check if file exists
        if (file_exists($filename))
        {
            // Create the translation object if the file exists
            $translate = new Zend_Translate('gettext', $filename, $lang);
        } 
        else
        {
            // Redirect with browser locale if there is any else redirect to /en
            $locale = new Zend_Locale();

            // Get the list of language files from lang directory
            // Define the ROOT_DIR in your bootstrap
            $dir = APPLICATION_PATH.'/lang/';
            $dirIterator = new DirectoryIterator($dir);
      
            // Loop through all files in lang folder
            $langs = array();
            foreach ($dirIterator as $subDir)
            {
                // Skip . and ..
                if ($subDir->isDot() || $subDir->isDir())
                {
                    continue;
                }
                                                
                // Get filename of current file
                $file = $subDir->getFilename();

                // Skip if it ends with .po
                if (strstr($file,'.po'))
                {
                    continue;
                }
                // Now $file = 'lang.xx.mo'
                // Get the language
                $lang = explode('.', $file);
                $lang = $lang[1];

                // Now $lang = 'xx'         
                $langs[] = $lang;
            }            

            if (in_array($locale->getLanguage(), $langs)) {
                $lang = $locale->getLanguage();
            }
            else
            {
                $lang = 'en';
            }
            
            // Redirect to locale of browser or /sv if lang doesnt exist
            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
            $redirector->gotoUrl($lang);
        }
         // Set the current language for view
         $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
         if (null === $viewRenderer->view)
         {
            $viewRenderer->initView();
         }
         
         $view = $viewRenderer->view;
         $layout= Zend_Controller_Action_HelperBroker::getStaticHelper('Layout');
         
         // $translate is important for translation to work
         $view->assign('translate', $translate);

         // Lang is only for some urls in different views
         $view->assign('lang', $lang);
         $layout->assign('lang',$lang);
         
         // Default translator for forms
         Zend_Form::setDefaultTranslator($translate);
         
         // Put translation object in zend registry
         Zend_Registry::set('Zend_Translate', $translate);
   }
}
