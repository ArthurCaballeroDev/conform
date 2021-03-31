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

    public function __construct()
    {
        $this->name = 'conform';
        $this->tab = 'front_office_features';
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
            $this->registerHook('displayLeftColumn') &&
            $this->registerHook('displayRightColumn') &&
            $this->registerHook('header') &&
            Configuration::updateValue('CONFORM_EMAIL', '');


    }

    public function uninstall()
    {

        return parent::uninstall() &&
            Configuration::deleteByName('CONFORM_EMAIL', '');
    }

    public function getContent()
    {
        $output = null;
        if (Tools::isSubmit('submit' . $this->name)) {
            $email = Tools::getValue('CONFORM_EMAIL');
            if (!$email || empty($email) || !Validate::isGenericName($email)) {
                $output .= $this->displayError($this->l('Configuration failed'));
            } else {
                Configuration::updateValue('CONFORM_EMAIL', $email);
                $output .= $this->displayConfirmation($this->l('Update successful'));
            }
        }
        return $output . $this->displayForm();


    }


    public function displayForm()
    {
        $fields_form = array();
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Conform settings'),
            ),
            'input' => array(

                array(
                    'type' => 'text',
                    'label' => $this->l('E-mail'),
                    'name' => 'CONFORM_EMAIL',
                    'size' => 5,
                    'required' => true
                )

            ),
            'submit' => array(
                'title' => $this->l('Submit'),
                'class' => 'btn btn-default'
            )
        );

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
                'CONFORM_EMAIL' => Configuration::get('CONFORM_EMAIL'),
            ),
        );

        return $helper->generateForm($fields_form);
    }

}







