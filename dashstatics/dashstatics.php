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

class Dashstatics extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'dashstatics';
        $this->tab = 'dashboard';
        $this->version = '1.0.0';
        $this->author = 'BELLILI RAMI';
        $this->need_instance = 0;
        $this->push_filename = _PS_CACHE_DIR_.'push/activity';
        $this->allow_push = true;
        $this->push_time_limit = 180;
        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('STATISTICS');
        $this->description = $this->l('display statistics on dashbord');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        return parent::install() &&
            $this->registerHook('dashboardZoneTwo') &&
            $this->registerHook('dashboardData') &&
            $this->registerHook('actionAdminControllerSetMedia');
    }
    public function uninstall()
    {
        return parent::uninstall();
    }

    /**
     * Ajout du js spécifique du module dans le dashboard
     */
    public function hookActionAdminControllerSetMedia()
    {
         
            if (method_exists($this->context->controller, 'addJquery')) {
                $this->context->controller->addJquery();
            }
            $this->context->controller->addJs('https://cdn.jsdelivr.net/npm/chart.js@2.8.0');
            $this->context->controller->addJs($this->_path.'views/js/'.$this->name.'.js');

    }

    /**
     * Hook Dasboard Principale
     * @return type
     */
    public function hookDashboardZoneTwo()
    {
        if ( !$this->isCached('hookDashboardZoneTwo.tpl',$this->getCacheId()) ) {
            $this->context->smarty->assign('configForm',$this->renderConfigForm());
        }
          $this->context->smarty->assign(
            array(
                'dashproducts_config_form' => $this->renderConfigForm(),
            )
        );
        return $this->display(__FILE__, 'views/templates/hook/hookDashboardZoneTwo.tpl');
    }

    /**
     * Fonction spécifique aux modules Dashboard
     */
   public function renderConfigForm()
    {
        $fields_form = array(
            'form' => array(
                'input' => array(),
                'submit' => array(
                    'title' => $this->trans('Save', array(), 'Admin.Actions'),
                    'class' => 'btn btn-default pull-right submit_dash_config',
                    'reset' => array(
                        'title' => $this->trans('Cancel', array(), 'Admin.Actions'),
                        'class' => 'btn btn-default cancel_dash_config',
                    )
                )
            ),
        );

        $inputs = array(
            array(
                'label' => $this->trans('Number of "Recent Orders" to display', array(), 'Modules.Dashproducts.Admin'),
                'config_name' => 'DASHPRODUCT_NBR_SHOW_LAST_ORDER'
            ),
            array(
                'label' => $this->trans('Number of "Best Sellers" to display', array(), 'Modules.Dashproducts.Admin'),
                'config_name' => 'DASHPRODUCT_NBR_SHOW_BEST_SELLER'
            ),
            array(
                'label' => $this->trans('Number of "Most Viewed" to display', array(), 'Modules.Dashproducts.Admin'),
                'config_name' => 'DASHPRODUCT_NBR_SHOW_MOST_VIEWED'
            ),
            array(
                'label' => $this->trans('Number of "Top Searches" to display', array(), 'Modules.Dashproducts.Admin'),
                'config_name' => 'DASHPRODUCT_NBR_SHOW_TOP_SEARCH'
            ),
        );

        foreach ($inputs as $input) {
            $fields_form['form']['input'][] = array(
                'type' => 'select',
                'label' => $input['label'],
                'name' => $input['config_name'],
                'options' => array(
                    'query' => array(
                        array('id' => 5, 'name' => 5),
                        array('id' => 10, 'name' => 10),
                        array('id' => 20, 'name' => 20),
                        array('id' => 50, 'name' => 50),
                    ),
                    'id' => 'id',
                    'name' => 'name',
                )
            );
        }

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->id = (int)Tools::getValue('id_carrier');
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitDashConfig';
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form));
    }

    public function getConfigFieldsValues()
    {
        return array(
            'DASHPRODUCT_NBR_SHOW_LAST_ORDER' => Configuration::get('DASHPRODUCT_NBR_SHOW_LAST_ORDER'),
            'DASHPRODUCT_NBR_SHOW_BEST_SELLER' => Configuration::get('DASHPRODUCT_NBR_SHOW_BEST_SELLER'),
            'DASHPRODUCT_NBR_SHOW_MOST_VIEWED' => Configuration::get('DASHPRODUCT_NBR_SHOW_MOST_VIEWED'),
            'DASHPRODUCT_NBR_SHOW_TOP_SEARCH' => Configuration::get('DASHPRODUCT_NBR_SHOW_TOP_SEARCH'),
        );
    }

}
