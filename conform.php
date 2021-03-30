<?php
/**
* 2007-2021 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2021 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Conform extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'conform';
        $this->tab = 'emailing';
        $this->version = '1.0.0';
        $this->author = 'Ukoo';
        $this->need_instance = 0;

        $this->context = Context::getContext();
        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Conform - Contact Form');
        $this->description = $this->l('Create your contact form in 1 clic !');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall the module ?');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        if (Shop::isFeatureActive()) {
            return Shop::setContext(Shop::CONTEXT_ALL);
        }
        return parent::install() &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('displayFooter');
            Configuration::updateValue('NAME', '') &&
            Configuration::updateValue('SURNAME', '') &&
            Configuration::updateValue('OBJECT', '') &&
            Configuration::updateValue('MESSAGE', '') &&
            Configuration::updateValue('SUBMIT', '');


    }

    public function uninstall()
    {
        Configuration::deleteByName('CONFORM_LIVE_MODE');

        include(dirname(__FILE__).'/sql/uninstall.php');

        return parent::uninstall() &&
            Configuration::deleteByName('NAME', '') &&
            Configuration::deleteByName('SURNAME', '') &&
            Configuration::deleteByName('OBJECT', '') &&
            Configuration::deleteByName('MESSAGE', '') &&
            Configuration::deleteByName('SUBMIT', '');
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        $output = null;
        if (Tools::isSubmit('submit' . $this->name)) {
            $Name = Tools::getValue('NAME');
            $Surname = Tools::getValue('SURNAME');
            $Object = Tools::getValue('OBJECT');
            $Message = Tools::getValue('MESSAGE');
            $Submit = Tools::getValue('SUBMIT');
            if (!$Name || empty($Name) || !Validate::isGenericName($Name)) {
                $output .= $this->displayError($this->l('Configuration failed'));
            } else {
                Configuration::updateValue('NAME', $Name,);
                Configuration::updateValue('SURNAME', $Surname);
                Configuration::updateValue('OBJECT', $Object);
                Configuration::updateValue('MESSAGE', $Message);
                Configuration::updateValue('SUBMIT', $Submit);
                $output .= $this->displayConfirmation($this->l('Update successful'));
            }
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function displayForm()

        public function displayForm()
    {
        $fields_form = array();
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Contact Form settings'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'NAME',
                    'size' => 5,
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Surname'),
                    'name' => 'SURNAME',
                    'size' => 5,
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('E-mail'),
                    'name' => 'EMAIL',
                    'size' => 5,
                    'required' => true
                )
                array(
                    'type' => 'text',
                    'label' => $this->l('Object'),
                    'name' => 'OBJECT',
                    'size' => 5,
                    'required' => true
                )
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Message'),
                        'name' => 'MESSAGE',
                        'size' => 20,
                        'required' => true
                    )
            ),
            'submit' => array(
                'title' => $this->l('Submit'),
                'class' => 'btn btn-default'
            )
        );
    }
    {
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submit' . $this->name;
        $helper->toolbar_btn = array(
            'save' =>
                array(
                    'desc' => $this->l('Save'),
                    'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name .
                        '&token=' . Tools::getAdminTokenLite('AdminModules'),
                ),
            'back' => array(
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );

        $helper->tpl_vars = array(
            'fields_value' => array(
                'NAME' => Configuration::get('NAME'),
                'SURNAME' => Configuration::get('SURNAME'),
                'EMAIL' => Configuration::get('EMAIL'),
                'OBJECT' => Configuration::get('OBJECT'),
                'MESSAGE' => Configuration::get('MESSAGE')
            ),
        );
        return $helper->generateForm($fields_form);
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Live mode'),
                        'name' => 'CONFORM_LIVE_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module in live mode'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc' => $this->l('Enter a valid email address'),
                        'name' => 'CONFORM_ACCOUNT_EMAIL',
                        'label' => $this->l('Email'),
                    ),
                    array(
                        'type' => 'password',
                        'name' => 'CONFORM_ACCOUNT_PASSWORD',
                        'label' => $this->l('Password'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'CONFORM_LIVE_MODE' => Configuration::get('CONFORM_LIVE_MODE', true),
            'CONFORM_ACCOUNT_EMAIL' => Configuration::get('CONFORM_ACCOUNT_EMAIL', 'contact@prestashop.com'),
            'CONFORM_ACCOUNT_PASSWORD' => Configuration::get('CONFORM_ACCOUNT_PASSWORD', null),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }
public function hookDisplayLeftColumn($params)
{
   $this->context->smarty->assign(
       array(
           'Name' => Configuration::get('NAME'),
           'Surname' => Configuration::get('SURNAME'),
           'E-mail' => Configuration::get('EMAIL'),
           'Object' => Configuration::get('OBJECT'),
           'Message' => Configuration::get('MESSAGE')
       )
   );
   return $this->display(__FILE__, 'views/templates/hook/conform.tpl');
}

}
