<?php

if (!defined('_PS_VERSION_'))
	exit;

class Rx_recaptcharegisterform extends Module
{

    private $_html = '';
    
	public function __construct()
	{
        $this->name = 'rx_recaptcharegisterform';
        $this->tab = 'administration';           
        $this->version = '1.0.1';
        $this->author = 'Łukasz Ryszkiewicz';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array(
            'min' => '1.7', 
            'max' => _PS_VERSION_
        );
        $this->bootstrap = true;
        $this->pfx = strtoupper($this->name).'_';
            
        parent::__construct();

        $this->displayName = $this->l('Google reCaptcha for registration form');
        $this->description = $this->l('Adds a Google reCaptcha validation to customer registration form.');
        $this->confirmUninstall = $this->l('Are you sure you want to delete this module?');           
	}
	
	public function install()
	{
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        $module_hooks = array(
            'displayCustomerAccountForm',
            'actionSubmitAccountBefore'
        );
        
        if (!parent::install()
            || !$this->registerHook($module_hooks)
            || !Configuration::updateValue($this->pfx.'CENTER', false))
            
            return false;

        return true;

	}
	
	public function uninstall()
	{
        if (!Configuration::deleteByName($this->pfx.'SITE_KEY') 
            || !Configuration::deleteByName($this->pfx.'SECRET_KEY')
            || !Configuration::deleteByName($this->pfx.'THEME')
            || !Configuration::deleteByName($this->pfx.'SIZE')
            || !Configuration::deleteByName($this->pfx.'TABINDEX')
            || !Configuration::deleteByName($this->pfx.'FORCE_LANGUAGE')
            || !Configuration::deleteByName($this->pfx.'CENTER')
            || !parent::uninstall()
            ) {
                return false;
        }

        return true;     
	}

	public function getContent()
	{
        $this->_postProcess();
        $this->_displayForm();

        return $this->_html;
	}       
        
	private function _displayForm()
	{
            $this->_html .= $this->_generateForm();
	}

	private function _generateForm()
	{

		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs'
					),
                    'input' => array(
                        array(
                            'name' => 'separator',
                            'type' => 'html',
                            'html_content' => '
                                <div class="row">
                                    <div class="alert alert-info" role="alert">
                                        <p>This is reCaptcha v2 module for Prestashop 1.7. Secure new customer register form with reCaptcha checkbox. Captcha is displayed only on <a href="/index.php?controller=authentication&create_account=1">new customer register form</a>, not in checkout process or other places. This should stop internet bots to register fake accounts.</p>
                                    </div>
                                </div>                   
                                ',
                            'ignore' => true
                        ),
                        array(
                            'type' => 'text',
                            'prefix' => '<i class="icon icon-google "></i>',
                            'desc' => $this->l('Your Google reCaptcha v2 site key'),
                            'name' => $this->pfx.'SITE_KEY',
                            'label' => $this->l('reCaptcha site key'),
                            'required' => true,
                            'empty_message' => $this->l('Please fill the public key'),
                        ),
                        array(
                            'type' => 'text',
                            'prefix' => '<i class="icon icon-google"></i>',
                            'desc' => $this->l('Your Google reCaptcha v2 secret key'),
                            'name' => $this->pfx.'SECRET_KEY',
                            'label' => $this->l('reCaptcha secret key'),
                            'required' => true,
                            'empty_message' => $this->l('Please fill the captcha secret key'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Theme'),
                            'name' => $this->pfx.'THEME',
                            'desc' => $this->l('Optional. The color theme of the widget.'),
                            'class' => 'fixed-width-xs',
                            'options' => array(
                                    'query' => array(
                                        array(
                                            'id' => 'light',
                                            'name' => 'light - '.$this->l('(default)')
                                        ),
                                        array(
                                                'id' => 'dark',
                                                'name' => 'dark - '.$this->l('better for dark shop themes')
                                            ),
                                    ),
                                    'id' => 'id',
                                    'name' => 'name',
                            )                           
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Size'),
                            'name' => $this->pfx.'SIZE',
                            'desc' => $this->l('Optional. The size of the widget.'),
                            'class' => 'fixed-width-md',
                            'options' => array(
                                    'query' => array(
                                        array(
                                            'id' => 'normal',
                                            'name' => 'normal'
                                        ),
                                        array(
                                            'id' => 'compact',
                                            'name' => 'compact'
                                        ),
                                    ),
                                    'id' => 'id',
                                    'name' => 'name',
                            )                           
                        ),
                        array(
                            'type' => 'text',
                            'prefix' => '<i class="icon icon-google"></i>',
                            'desc' => $this->l('Optional. The tabindex of the widget and challenge. If other elements in your page use tabindex, it should be set to make user navigation easier.'),
                            'name' => $this->pfx.'TABINDEX',
                            'label' => $this->l('Tabindex'),
                            'class' => 'fixed-width-xs',
                        ),
                        array(
                            'type' => 'text',
                            'prefix' => '<i class="icon icon-google"></i>',
                            'desc' => $this->l('Optional. Forces the widget to render in a specific language. Auto-detects the user\'s language if unspecified.')
                                .'<br>'.$this->l('See more languages at:').' '
                                .'<a href="https://developers.google.com/recaptcha/docs/language" target="_blank">https://developers.google.com/recaptcha/docs/language</a>',
                            'name' => $this->pfx.'FORCE_LANGUAGE',
                            'label' => $this->l('Language'),
                            'class' => 'fixed-width-xs',
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Centered in row?'),
                            'name' => $this->pfx.'CENTER',
                            'is_bool' => true,
                            'desc' => $this->l('Set "Yes" to center reCaptcha box in form.'),
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => true,
                                    'label' => $this->l('Yes')
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => false,
                                    'label' => $this->l('No')
                                )
                            ),
                        ),
                        array(
                            'name' => 'separator',
                            'type' => 'html',
                            'html_content' => '                
                                    <div class="row">
                                        <div class="alert alert-info" role="alert">
                                            <h4 class="alert-heading">How to configure?</h4>
                                           <p>1. Go to reCaptcha admin console at <a href="https://www.google.com/recaptcha/admin" target="_blank">https://www.google.com/recaptcha/admin</a></p>
                                           <p>2. Register new site</p>
                                           <p>3. Insert site label and choose reCAPTCHA v2 and "I am not a robot" option. This module don`t work with other v2 methods and v3.</p>
                                           <p>4. Insert shop domain. If you use URL like www.example.com, insert just example.com</p>
                                           <p>5. Accept agreement, check alerts if you want and hit "Send" button.</p>
                                           <p>6. Copy and paste site key and secret key to your module settings.</p> 
                                           <p>7. You can customize other settings such as size, color, tabindex, </p>
                                           <hr>
                                            <p>Done!. Double check if it works at: <a href="/index.php?controller=authentication&create_account=1">new customer register form</a></p>
                                        </div>
                                    </div>
                                ',
                            'ignore' => true
                        ),
                    ),
				'submit' => array(
					'title' => $this->l('Save'),
					)
				)
			);

		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper = new HelperForm();
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit_'.$this->name;
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules',false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
        
        $helper->fields_value[$this->pfx.'SITE_KEY'] = Configuration::get($this->pfx.'SITE_KEY');
        $helper->fields_value[$this->pfx.'SECRET_KEY'] = Configuration::get($this->pfx.'SECRET_KEY');
        $helper->fields_value[$this->pfx.'SIZE'] = Configuration::get($this->pfx.'SIZE');
        $helper->fields_value[$this->pfx.'THEME'] = Configuration::get($this->pfx.'THEME');
        $helper->fields_value[$this->pfx.'TABINDEX'] = Configuration::get($this->pfx.'TABINDEX');
        $helper->fields_value[$this->pfx.'FORCE_LANGUAGE'] = Configuration::get($this->pfx.'FORCE_LANGUAGE');
        $helper->fields_value[$this->pfx.'CENTER'] = Configuration::get($this->pfx.'CENTER');

        
		return $helper->generateForm(array($fields_form));
	}


	private function _postProcess()
	{
        if (Tools::isSubmit('submit_'.$this->name)){
            $site_key = Tools::getValue($this->pfx.'SITE_KEY');
            $secret_key = Tools::getValue($this->pfx.'SECRET_KEY');
            if (empty($site_key)) {
                $this->_html .= $this->displayError($this->l('reCaptcha site key is required!'));
            } 
            if (empty($secret_key)) {
                $this->_html .= $this->displayError($this->l('reCaptcha secret key is required!'));
            }
            
            Configuration::updateValue($this->pfx.'SITE_KEY', $site_key);
            Configuration::updateValue($this->pfx.'SECRET_KEY', $secret_key);
            Configuration::updateValue($this->pfx.'SIZE', Tools::getValue($this->pfx.'SIZE'));
            Configuration::updateValue($this->pfx.'THEME', Tools::getValue($this->pfx.'THEME'));
            Configuration::updateValue($this->pfx.'TABINDEX', Tools::getValue($this->pfx.'TABINDEX'));
            Configuration::updateValue($this->pfx.'FORCE_LANGUAGE', Tools::getValue($this->pfx.'FORCE_LANGUAGE'));
            Configuration::updateValue($this->pfx.'CENTER', Tools::getValue($this->pfx.'CENTER'));
            $this->_html .= $this->displayConfirmation($this->l('Settings saved. Check customer registration form page in your shop to see if everythings works fine.'));
            
        }
	}
    
    public function hookDisplayCustomerAccountForm($params)
	{          
        if ($this->context->controller instanceof AuthController) {
            if (!empty(Configuration::get($this->pfx.'SITE_KEY')) && !empty(Configuration::get($this->pfx.'SECRET_KEY'))) {
                $vars = [
                        $this->pfx.'SITE_KEY' => Configuration::get($this->pfx.'SITE_KEY'),
                        $this->pfx.'SIZE' => Configuration::get($this->pfx.'SIZE'),
                        $this->pfx.'THEME' => Configuration::get($this->pfx.'THEME'),
                        $this->pfx.'TABINDEX' => Configuration::get($this->pfx.'TABINDEX'),
                        $this->pfx.'FORCE_LANGUAGE' => Configuration::get($this->pfx.'FORCE_LANGUAGE'),
                        $this->pfx.'CENTER' => (bool)Configuration::get($this->pfx.'CENTER')
                    ];
            
                $this->context->smarty->assign($vars);
                
                return $this->display(__FILE__, 'views/templates/hook/displayCustomerAccountForm.tpl');
            }
        }
	}
	
	public function hookActionSubmitAccountBefore($params)
	{
        if ($this->context->controller instanceof AuthController) {

            if (!empty(Configuration::get($this->pfx.'SITE_KEY')) 
                && !empty(Configuration::get($this->pfx.'SECRET_KEY'))){
                
                if (!isset($_POST['g-recaptcha-response']) || empty($_POST['g-recaptcha-response'])) {
                    $this->context->controller->errors[] = $this->l('You must use reCaptcha form to continue');
            
                    return;
                }
            }
            
            $verification_url = 'https://www.google.com/recaptcha/api/siteverify';
            $secret_key = Configuration::get($this->pfx.'SECRET_KEY');

            $cl = curl_init();
            curl_setopt_array($cl, [
                CURLOPT_URL => $verification_url,
                CURLOPT_POST => True,
                CURLOPT_POSTFIELDS => array(
                    'secret' => $secret_key,
                    'response' => $_POST['g-recaptcha-response'],
                    'remoteip' => $_SERVER['REMOTE_ADDR']
                ),
                CURLOPT_RETURNTRANSFER => True
            ]);
            $output = curl_exec($cl);
            curl_close($cl);            
            
            $response = json_decode($output);
            if($response->success !== True){
                $this->context->controller->errors[] = $this->l('reCaptcha verification failed!');
            
                return;
            } 
            else {
            
                return True;
            }                            
        }
    }
}


