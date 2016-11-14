<?php

use CMSMS\Twig\Twig;
use NetDesign\NetDesignModule;

class BootstrapForm extends NetDesignModule {
    use Twig;
    private $forms = null;

    /**
     * Returns the version of the module
     *
     * @return string
     */
    public function GetVersion() {
        return '1.0.0';
    }

    /**
     * @return string
     */
    public function GetFriendlyName() {
        return 'Form Automation';
    }

    /**
     * @return bool
     */
    public function HasAdmin() {
        return true;
    }

    public function GetAdminMenuItems()
    {
        $ret = CmsAdminMenuItem::from_module($this);
        $ret->title = 'Contactgegevens';
        return [$ret];
    }

    /**
     * @return string
     */
    public function GetAdminSection()
    {
        return 'content';
    }


    /**
     * Since we set our constructor as final we define this method in addition to InitializeAdmin() and
     * InitializeFrontend().
     */
    public function Initialize() {
        $this->GetForms();
        $this->twigInit();
    }

    /**
     * Called from within the constructor, ONLY for admin module
     * actions.  This method should be overridden to create routes, and
     * set handled parameters, and perform other initialization tasks
     * that need to be setup for all frontend actions.
     *
     * @see CreateParameter
     */
    protected function InitializeAdmin() {
        parent::InitializeAdmin();
        $this->smarty->registerPlugin('function', 'BootstrapForm', 'BootstrapForm::SmartyPluginBootstrapForm');
    }


    /**
     * Function that will get called as module is installed. This function should
     * do any initialization functions including creating database tables. It
     * should return a string message if there is a failure. Returning nothing (FALSE)
     * will allow the install procedure to proceed.
     *
     * The default behavior of this method is to include a file named method.install.php
     * in the module directory, if one can be found.  This provides a way of splitting
     * secondary functions into other files.
     *
     * @return string|false A value of FALSE indicates no error.  Any other value will be used as an error message.
     */
    public function Install() {
        $this->RegisterSmartyPlugin('BootstrapForm', 'function', 'SmartyPluginBootstrapForm');
        $this->RegisterSmartyPlugin('ContactData', 'function', 'SmartyPluginContactData');
        return false;
    }

    /**
     * Function that will get called as module is uninstalled. This function should
     * remove any database tables that it uses and perform any other cleanup duties.
     * It should return a string message if there is a failure. Returning nothing
     * (FALSE) will allow the uninstall procedure to proceed.
     *
     * The default behaviour of this function is to include a file called method.uninstall.php
     * in your module directory to do uninstall operations.
     *
     * @return string|false A result of FALSE indicates that the module uninstalled correctly, any other value indicates an error message.
     */
    public function Uninstall() {
        $this->RemoveSmartyPlugin();
        return false;
    }

    /**
     * Returns true if the modules thinks it has the capability specified
     *
     * @param string $capability an id specifying which capability to check for, could be "wysiwyg" etc.
     * @param array $params An associative array further params to get more detailed info about the capabilities. Should be syncronized with other modules of same type
     * @return bool
     */
    public function HasCapability($capability, $params = array()) {
        switch($capability) {
            case CmsCoreCapabilities::PLUGIN_MODULE: return true;
        }
        return false;
    }

    public function GetForms() {
        if (!ModuleOperations::get_instance()->IsModuleActive($this->GetName())) return [];
        if (is_null($this->forms)) {
            $this->forms = array();
            $glob = glob(cms_join_path($this->ClientModulePath(), '*.php'));
            foreach($glob as $filename) {
                $form = pathinfo($filename, PATHINFO_FILENAME);
                require_once($filename);
                if (
                    !is_a($form, 'BootstrapForm\\Form', true)
                ) continue;
                $this->forms[] = $form;
            }
            sort($this->forms);
        }
        return $this->forms;
    }

    public static function SmartyPluginBootstrapForm($params, Smarty_Internal_Template $template) {
        $mod = BootstrapForm::GetInstance();
        $forms = $mod->GetForms();
        if (!array_key_exists('form', $params) && count($forms) > 0) $params['form'] = $forms[0];
        if (!is_a($params['form'], 'BootstrapForm\\Form', true)) {
            throw new InvalidArgumentException(sprintf('"%s" is not a valid form class', $params['form']));
        }
        $form = new $params['form']();
        $mod->twig->addGlobal('actionId', $mod->ActionId());
        return $mod->twigRender('default.twig', ['form' => new $form()]);
    }

    public static function SmartyPluginContactData($params, Smarty_Internal_Template $template) {
        if (!array_key_exists('assign', $params) || empty($params['assign'])) {
            $var = 'contactData';
        } else {
            $var = $params['assign'];
        }
        $mod = BootstrapForm::GetInstance();
        $mod->assign($var, new \BootstrapForm\ContactData());
    }
}