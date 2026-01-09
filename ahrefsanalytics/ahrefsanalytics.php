<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class AhrefsAnalytics extends Module
{
    const CFG_KEY = 'AHREFS_ANALYTICS_KEY';
    const CFG_ENABLED = 'AHREFS_ANALYTICS_ENABLED';

    /**
     * Tiny built-in translation helper (EN/FR only).
     * We use this instead of PrestaShop translation files to keep the module
     * fully self-contained and predictable out-of-the-box.
     */
    private function t($en, $fr = null)
    {
        $iso = isset($this->context->language->iso_code) ? $this->context->language->iso_code : 'en';
        if ($iso === 'fr') {
            return $fr !== null ? $fr : $en;
        }
        return $en;
    }

    public function __construct()
    {
        $this->name = 'ahrefsanalytics';
        $this->tab = 'analytics_stats';
        $this->version = '1.1.0';
        $this->author = 'ADLX';
        $this->need_instance = 0;

        // Ensures BO form uses Bootstrap styles
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->t('Ahrefs Web Analytics', 'Ahrefs Web Analytics');
        $this->description = $this->t(
            'Injects the Ahrefs Web Analytics script in the <head> via displayHeader (theme-update safe).',
            'Injecte le script Ahrefs Web Analytics dans le <head> via displayHeader (sans modifier le thème).'
        );
        $this->ps_versions_compliancy = ['min' => '1.7.0.0', 'max' => _PS_VERSION_];
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayHeader')
            && Configuration::updateValue(self::CFG_ENABLED, 0)
            && Configuration::updateValue(self::CFG_KEY, '');
    }

    public function uninstall()
    {
        return parent::uninstall()
            && Configuration::deleteByName(self::CFG_ENABLED)
            && Configuration::deleteByName(self::CFG_KEY);
    }

    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submitAhrefsAnalytics')) {
            $enabled = (int) Tools::getValue(self::CFG_ENABLED);

            $clearKey = (int) Tools::getValue('AHREFS_ANALYTICS_CLEAR_KEY');
            $newKey = trim((string) Tools::getValue(self::CFG_KEY));

            // Always persist enabled state (even if key field is empty)
            Configuration::updateValue(self::CFG_ENABLED, $enabled);

            if ($clearKey === 1) {
                Configuration::updateValue(self::CFG_KEY, '');
            } else {
                // Only replace stored key if user typed something
                if ($newKey !== '') {
                    Configuration::updateValue(self::CFG_KEY, $newKey);
                }
            }

            $output .= $this->displayConfirmation($this->t('Settings saved.', 'Paramètres enregistrés.'));
        }

        // One single, clear info panel (no duplicated icons)
        $output .= $this->renderInfoPanel();

        // Render Bootstrap helper form
        $output .= '<div class="bootstrap">' . $this->renderForm() . '</div>';

        return $output;
    }

    protected function renderInfoPanel()
    {
        $enabled = (int) Configuration::get(self::CFG_ENABLED);
        $key = (string) Configuration::get(self::CFG_KEY);
        $hasKey = ($key !== '');

        $statusLabel = $enabled ? $this->t('Enabled', 'Activé') : $this->t('Disabled', 'Désactivé');
        $statusClass = $enabled ? 'label-success' : 'label-default';

        // User requested: show the saved key in clear (this is not a secret key)
        $shownKey = $hasKey ? $key : $this->t('No key saved yet', 'Aucune clé enregistrée pour le moment');

        $ahrefsUrl = 'https://ahrefs.com/webmaster-tools';

        // Use a clean Bootstrap alert (no icons) to avoid odd duplicated glyphs in some BO setups
        $html = '
        <div class="bootstrap" style="margin-top:10px;">
          <div class="alert alert-info" role="alert" style="margin-bottom:15px;">
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:8px;">
              <img src="' . $this->_path . 'logo.png" alt="Ahrefs" style="width:20px; height:20px;" />
              <strong style="font-size:16px;">' . $this->t('How to set up Ahrefs Web Analytics', 'Comment configurer Ahrefs Web Analytics') . '</strong>
            </div>

            <ol style="margin:0 0 10px 18px;">
              <li>' . $this->t(
                  'Create a free Ahrefs Webmaster Tools account (if you don\'t have one).',
                  'Créez un compte gratuit Ahrefs Webmaster Tools (si vous n\'en avez pas).'
              ) . '</li>
              <li>' . $this->t(
                  'Open Web Analytics settings and copy your data-key value.',
                  'Ouvrez les paramètres Web Analytics et copiez la valeur data-key.'
              ) . '</li>
              <li>' . $this->t(
                  'Paste the key below, enable tracking, and save.',
                  'Collez la clé ci-dessous, activez le suivi, puis enregistrez.'
              ) . '</li>
              <li>' . $this->t(
                  'Then click "Verify installation" in Ahrefs.',
                  'Ensuite, cliquez sur "Verify installation" dans Ahrefs.'
              ) . '</li>
            </ol>

            <p style="margin:0 0 10px 0;">
              <a class="btn btn-default" href="' . $ahrefsUrl . '" target="_blank" rel="noopener noreferrer">
                ' . $this->t('Open Ahrefs Webmaster Tools', 'Ouvrir Ahrefs Webmaster Tools') . '
              </a>
            </p>

            <p style="margin:0;">
              <strong>' . $this->t('Current status:', 'Statut actuel :') . '</strong>
              <span class="label ' . $statusClass . '">' . $statusLabel . '</span>
              &nbsp;&nbsp;|&nbsp;&nbsp;
              <strong>' . $this->t('Saved key:', 'Clé enregistrée :') . '</strong>
              <code>' . Tools::htmlentitiesUTF8($shownKey) . '</code>
            </p>
            <p style="margin:6px 0 0 0; font-size:12px; opacity:0.85;">
              ' . $this->t(
                  'Note: this module injects the script inside the <head> tag only (displayHeader).',
                  'Remarque : ce module injecte uniquement le script dans la balise <head> (displayHeader).'
              ) . '
            </p>
          </div>
        </div>';

        return $html;
    }

    protected function renderForm()
    {
        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->t('Ahrefs Web Analytics settings', 'Paramètres Ahrefs Web Analytics'),
                    'icon'  => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->t('Enable tracking', 'Activer le suivi'),
                        'name' => self::CFG_ENABLED,
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'enabled_on', 'value' => 1, 'label' => $this->t('Enabled', 'Activé')],
                            ['id' => 'enabled_off', 'value' => 0, 'label' => $this->t('Disabled', 'Désactivé')],
                        ],
                        'desc' => $this->t('Turn Ahrefs tracking on or off.', 'Active ou désactive le tracking Ahrefs Analytics.'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->t('Ahrefs Analytics key', 'Clé Ahrefs Analytics'),
                        'name' => self::CFG_KEY,
                        'required' => false,
                        'desc' => $this->t(
                            'Paste your Ahrefs data-key value (from the installation snippet).',
                            'Collez votre valeur data-key Ahrefs (depuis le snippet d’installation).'
                        ),
                        'hint' => $this->t('Example format: OIsSv/REMT+oZHSA8x4AqA', 'Exemple de format : OIsSv/REMT+oZHSA8x4AqA'),
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->t('Clear stored key', 'Supprimer la clé enregistrée'),
                        'name' => 'AHREFS_ANALYTICS_CLEAR_KEY',
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'clear_on', 'value' => 1, 'label' => $this->t('Yes', 'Oui')],
                            ['id' => 'clear_off', 'value' => 0, 'label' => $this->t('No', 'Non')],
                        ],
                        'desc' => $this->t(
                            'If enabled, the currently saved key will be deleted when you save.',
                            'Si activé, la clé actuellement enregistrée sera supprimée lors de l’enregistrement.'
                        ),
                    ],
                ],
                'submit' => [
                    'title' => $this->t('Save', 'Enregistrer'),
                ],
            ],
        ];

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->allow_employee_form_lang = (int) Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitAhrefsAnalytics';
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->bootstrap = true;

        // Show current saved values (user requested transparency)
        $helper->fields_value[self::CFG_ENABLED] = (int) Configuration::get(self::CFG_ENABLED);
        $helper->fields_value[self::CFG_KEY] = (string) Configuration::get(self::CFG_KEY);
        $helper->fields_value['AHREFS_ANALYTICS_CLEAR_KEY'] = 0;

        return $helper->generateForm([$fields_form]);
    }

    public function hookDisplayHeader($params)
    {
        if (!(int) Configuration::get(self::CFG_ENABLED)) {
            return '';
        }

        $key = trim((string) Configuration::get(self::CFG_KEY));
        if ($key === '') {
            return '';
        }

        $keyEsc = htmlspecialchars($key, ENT_QUOTES, 'UTF-8');
        return '<script src="https://analytics.ahrefs.com/analytics.js" data-key="' . $keyEsc . '" async></script>';
    }
}
