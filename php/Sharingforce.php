<?php

class Sharingforce {

    const NONCE = 'Sharingforce rocks!';
    const API_PUBLIC_KEY_OPTION_NAME = 'sharingforceApiPublicKey';
    const API_SECRET_KEY_OPTION_NAME = 'sharingforceApiSecretKey';
    const PER_PAGE_WIDGET_ID_OPTION_NAME = 'sharingforcePerPageWidgetId';
    const PER_PAGE_WIDGET_NAME_OPTION_NAME = 'sharingforcePerPageWidgetName';
    const PER_POST_WIDGET_ID_OPTION_NAME = 'sharingforcePerPostWidgetId';
    const PER_POST_WIDGET_NAME_OPTION_NAME = 'sharingforcePerPostWidgetName';
    const WIDGETS_OPTIONS_GROUP_NAME = 'sharingforceWidgets';
    const SECRET_OPTION_NAME = 'sharingforceSecret';
    const PER_POST_WIDGET_DISABLED_URLS_OPTION_NAME =
        'sharingforcePerPostWidgetDisabledUrls';
    const PER_PAGE_WIDGET_DISABLED_URLS_OPTION_NAME =
        'sharingforcePerPageWidgetDisabledUrls';

    /** @var string */
    private $pluginDirUrl;
    /** @var string */
    private $authorizationMessage;

    static public function getInstance()
    {
        static $instance;
        if (!$instance) {
            $instance = new self;
        }
        return $instance;
    }

    public function run()
    {
        require_once(dirname(__FILE__) . '/misc.php');
        require_once(dirname(__FILE__) . '/UrlService.php');

        // $this->pluginDirUrl = plugin_dir_url(__FILE__); //- this doesn't work on Windows when you create a symlink, so...
        $this->pluginDirUrl = plugins_url() . '/sharingforce/';

        if (is_admin()) {
            $this->admin();
        } else {
            $this->nonAdmin();
        }
    }

    private function admin()
    {
        // admin_menu is to add Sharingforce menu items
        add_action('admin_menu', array($this, 'actionAdminMenu'));
        // admin_notices is for message "Sharingforce is almost ready"
        add_action('admin_notices', array($this, 'actionAdminNotices'));
        // admin_init is to register settings
        add_action('admin_init', array($this, 'actionAdminInit'));
        // plugin_action_links_sharingforce is for Setting link in Plugins grid
        add_filter(
            'plugin_action_links_sharingforce/sharingforce.php',
            array($this, 'filterPluginActionLinks')
        );

        if ($this->isOnConfigPage()) {
            // admin_enqueue_scripts is to add CSS and JS
            add_action(
                'admin_enqueue_scripts',
                array($this, 'actionAdminEnqueueScripts')
            );
            add_action('admin_footer', array($this, 'adminFooter'));
        }

        add_action(
            'wp_ajax_sharingforce_store_api_keys',
            array($this, 'wpAjaxSharingforceStoreApiKeys')
        );

        add_action(
            'wp_ajax_sharingforce_store_widget',
            array($this, 'wpAjaxSharingforceStoreWidget')
        );

        add_action(
            'wp_ajax_sharingforce_store_per_post_widget_disabled_urls',
            array($this, 'wpAjaxSharingforceStorePerPostWidgetDisabledUrls')
        );

        add_action(
            'wp_ajax_sharingforce_store_per_page_widget_disabled_urls',
            array($this, 'wpAjaxSharingforceStorePerPageWidgetDisabledUrls')
        );

        add_action('publish_post', array($this, 'publishPost'));
    }

    public function publishPost($postId)
    {
        $postUrl = get_permalink($postId);
        $clearPreviewCacheUrl = 'http://www.sharingforce.com/api/clear-preview-cache/';
        $curl = curl_init($clearPreviewCacheUrl);
        curl_setopt($curl, CURLOPT_POSTFIELDS, 'url=' . urlencode($postUrl));
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_exec($curl);
        curl_close($curl);
    }

    public function adminFooter()
    {
        $ajaxNonce = $this->getNonce();
        echo '
            <script>
                var sharingforceNonce = "' . $ajaxNonce . '";
                var sharingforceApiPublicKey = "' .
                    $this->getApiPublicKey() . '";
                var sharingforceApiSecretKey = "' .
                    $this->getApiSecretKey() . '";
                var sharingforcePerPostWidgetId = ' .
                    $this->getPerPostWidgetId() . ';
                var sharingforcePerPageWidgetId = ' .
                    $this->getPerPageWidgetId() . ';
                var sharingforceConfigUrl = "' .
                    Sharingforce_UrlService::removeParam(
                        $_SERVER['REQUEST_URI'],
                        'skip'
                    ) . '";
            </script>
        ';
    }

    public function wpAjaxSharingforceStoreApiKeys()
    {
        check_ajax_referer(self::NONCE, 'nonce');
        $result = array();
        $errorHtml = '';
        update_option(
            self::API_PUBLIC_KEY_OPTION_NAME,
            $_POST['sharingforceApiPublicKey']
        );
        update_option(
            self::API_SECRET_KEY_OPTION_NAME,
            $_POST['sharingforceApiSecretKey']
        );
        if ($errorHtml) {
            $result['errorHtml'] = $errorHtml;
        }
        $this->printJson($result);
    }

    private function printJson($json)
    {
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        header('Content-type: application/json');
        echo json_encode($json);
        die();
    }

    public function wpAjaxSharingforceStoreWidget()
    {
        check_ajax_referer(self::NONCE, 'nonce');
        $result = array();
        $errorHtml = '';
        if ($_POST['widgetType'] == 'PerPost') {
            $widgetIdOptionName = self::PER_POST_WIDGET_ID_OPTION_NAME;
            $widgetNameOptionName = self::PER_POST_WIDGET_NAME_OPTION_NAME;
        } else {
            $widgetIdOptionName = self::PER_PAGE_WIDGET_ID_OPTION_NAME;
            $widgetNameOptionName = self::PER_PAGE_WIDGET_NAME_OPTION_NAME;
        }
        update_option(
            $widgetIdOptionName,
            ToInt($_POST['widgetId'])
        );
        update_option(
            $widgetNameOptionName,
            $_POST['widgetName']
        );
        $this->clearCache();
        if ($errorHtml) {
            $result['errorHtml'] = $errorHtml;
        }
        $this->printJson($result);
    }

    public function wpAjaxSharingforceStorePerPostWidgetDisabledUrls()
    {
        check_ajax_referer(self::NONCE, 'nonce');
        $result = array();
        $errorHtml = '';
        update_option(
            self::PER_POST_WIDGET_DISABLED_URLS_OPTION_NAME,
            $_POST['perPostWidgetDisabledUrls']
        );
        $this->clearCache();
        if ($errorHtml) {
            $result['errorHtml'] = $errorHtml;
        }
        $this->printJson($result);
    }

    public function wpAjaxSharingforceStorePerPageWidgetDisabledUrls()
    {
        check_ajax_referer(self::NONCE, 'nonce');
        $result = array();
        $errorHtml = '';
        update_option(
            self::PER_PAGE_WIDGET_DISABLED_URLS_OPTION_NAME,
            $_POST['perPageWidgetDisabledUrls']
        );
        $this->clearCache();
        if ($errorHtml) {
            $result['errorHtml'] = $errorHtml;
        }
        $this->printJson($result);
    }

    private function clearCache()
    {
        if (function_exists('wp_cache_clear_cache')) {
            wp_cache_clear_cache();
        }
    }

    private function isOnConfigPage()
    {
        return (isset($_GET['page']) && $_GET['page'] == 'sharingforce-config');
    }

    public function filterPluginActionLinks($links)
    {
    	$links[] = '<a href="options-general.php?page=sharingforce-config">' . __('Settings', 'sharingforce-settings') . '</a>';
	    return $links;
    }

    public function actionAdminEnqueueScripts()
    {
		wp_register_script(
		    'jquery.colorbox.js',
		    $this->pluginDirUrl . 'js/includes/colorbox/jquery.colorbox.js',
		    array('jquery'),
		    '2.0'
        );
		wp_enqueue_script('jquery.colorbox.js');

		wp_register_style(
		    'colorbox.css',
		    $this->pluginDirUrl . 'js/includes/colorbox/colorbox.css',
		    array(),
		    '2.0'
        );
        wp_enqueue_style('colorbox.css');

		wp_register_script(
		    'jquery.confirm.js',
		    $this->pluginDirUrl .
		        'js/includes/colorbox/extended/jquery.confirm.js',
		    array('jquery'),
		    '2.0'
        );
		wp_enqueue_script('jquery.confirm.js');

		wp_register_script(
		    'messageBox.js',
		    $this->pluginDirUrl . 'js/includes/colorbox/extended/messageBox.js',
		    array('jquery'),
		    '2.0'
        );
		wp_enqueue_script('messageBox.js');

		wp_register_style(
		    'messageBox.css',
		    $this->pluginDirUrl .
		        'js/includes/colorbox/extended/messageBox.css',
		    array(),
		    '2.0'
        );
        wp_enqueue_style('messageBox.css');

		wp_register_style(
		    'sharingforce.css',
		    $this->pluginDirUrl . 'css/sharingforce.css',
		    array(),
		    '2.0'
        );
        wp_enqueue_style('sharingforce.css');

		wp_register_style(
		    'sharingforce-config.css',
		    $this->pluginDirUrl . 'css/config.css',
		    array(),
		    '2.0'
        );
        wp_enqueue_style('sharingforce-config.css');

		wp_register_script(
		    'sharingforce-widgets.js',
		    $this->pluginDirUrl . 'js/sharingforce-widgets.js',
		    array('jquery'),
		    '2.0'
        );
		wp_enqueue_script('sharingforce-widgets.js');

		wp_register_script(
		    'XMLHttpRequest.js',
		    $this->pluginDirUrl . 'js/includes/XMLHttpRequest/XMLHttpRequest.js',
		    array(),
		    '2.0'
        );
		wp_enqueue_script('XMLHttpRequest.js');

		wp_register_script(
		    'x2j.js',
		    $this->pluginDirUrl . 'js/includes/XMLObjectifier/x2j.js',
		    array(),
		    '2.0'
        );
		wp_enqueue_script('x2j.js');

		wp_register_script(
		    'sharingforce-webservice.js',
		    $this->pluginDirUrl . 'js/sharingforce-webservice.js',
		    array(),
		    '2.0'
        );
		wp_enqueue_script('sharingforce-webservice.js');

		wp_register_script(
		    'sharingforce-quick-message.js',
		    $this->pluginDirUrl . 'js/includes/sharingforce-quick-message.js',
		    array(),
		    '2.0'
        );
		wp_enqueue_script('sharingforce-quick-message.js');

		wp_register_style(
		    'sharingforce-quick-message.css',
		    $this->pluginDirUrl .
		        'js/includes/sharingforce-quick-message.css',
		    array(),
		    '2.0'
        );
        wp_enqueue_style('sharingforce-quick-message.css');

		wp_register_script(
		    'sharingforce-urls.js',
		    $this->pluginDirUrl . 'js/sharingforce-urls.js',
		    array(),
		    '2.0'
        );
		wp_enqueue_script('sharingforce-urls.js');

		wp_register_script(
		    'sharingforce-misc.js',
		    $this->pluginDirUrl . 'js/includes/sharingforce-misc.js',
		    array(),
		    '2.0'
        );
		wp_enqueue_script('sharingforce-misc.js');

		wp_register_script(
		    'sharingforce-config.js',
		    $this->pluginDirUrl . 'js/sharingforce-config.js',
		    array(),
		    '2.0'
        );
		wp_enqueue_script('sharingforce-config.js');
    }

    public function actionAdminInit()
    {
        register_setting(
            self::WIDGETS_OPTIONS_GROUP_NAME,
            self::PER_PAGE_WIDGET_ID_OPTION_NAME
        );
        register_setting(
            self::WIDGETS_OPTIONS_GROUP_NAME,
            self::PER_POST_WIDGET_ID_OPTION_NAME
        );
    }

    public function getPerPageWidgetId()
    {
        return ToInt(get_option(self::PER_PAGE_WIDGET_ID_OPTION_NAME));
    }

    public function getPerPostWidgetDisabledUrls()
    {
        return get_option(self::PER_POST_WIDGET_DISABLED_URLS_OPTION_NAME);
    }

    public function getPerPageWidgetDisabledUrls()
    {
        return get_option(self::PER_PAGE_WIDGET_DISABLED_URLS_OPTION_NAME);
    }

    public function getPerPageWidgetName()
    {
        $result = get_option(self::PER_PAGE_WIDGET_NAME_OPTION_NAME);
        if (!$result) {
            $result = 'widget #' . $this->getPerPageWidgetId();
        }
        return $result;
    }

    public function getPerPostWidgetId()
    {
        return ToInt(get_option(self::PER_POST_WIDGET_ID_OPTION_NAME));
    }

    public function getPerPostWidgetName()
    {
        $result = get_option(self::PER_POST_WIDGET_NAME_OPTION_NAME);
        if (!$result) {
            $result = 'widget #' . $this->getPerPostWidgetId();
        }
        return $result;
    }

    public function getApiPublicKey()
    {
        return get_option(self::API_PUBLIC_KEY_OPTION_NAME);
    }

    public function getApiSecretKey()
    {
        return get_option(self::API_SECRET_KEY_OPTION_NAME);
    }

    public function actionAdminNotices()
    {
        if ($this->isOnConfigPage()) {
            return;
        }
        if (!$this->isEnabled()) {
            require_once(dirname(__FILE__) . '/view/AlmostReadyMessage.php');
            echo Sharingforce_View_AlmostReadyMessage::getInstance()
                ->render();
        }
    }

    private function nonAdmin()
    {
        // wp_head is for javascript inclusion, and for og:tags
        add_action('wp_head', array($this, 'actionWpHead'));
        // wp_footer is for per-page widget (docked)
        add_action('wp_footer', array($this, 'actionWpFooter'));
        // the_content is for one per-post widget (not docked)
        add_filter('the_content', array($this, 'filterTheContent'));
        // xmlrpc for authorization
        add_filter('xmlrpc_methods', array($this, 'xmlrpcMethods'));
    }

    public function actionWpHead()
    {
        if (!$this->getPerPageWidgetId() && !$this->getPerPostWidgetId()) {
            return;
        }
        echo '<script>var a=document.getElementsByTagName("script")[0],b=document.createElement("script");b.src=document.location.protocol+"//' . Sharingforce_UrlService::bJsUrl() . '";a.parentNode.insertBefore(b,a);</script>';
        $this->addOgTags();
    }

    public function actionWpFooter()
    {
        if (!$this->getPerPageWidgetId()) {
            return;
        }
        $pageUrl = site_url() . $_SERVER['REQUEST_URI'];
        $disabledUrls = $this->getPerPageWidgetDisabledUrls();
        if ($disabledUrls) {
            $a = explode("\n", $disabledUrls);
            global $post;
            if (in_array($pageUrl, $a) || ($post && in_array($post->ID, $a))) {
                return;
            }
        }
        echo '<div class="sfwidget" data-widget="' . $this->getPerPageWidgetId() . '"></div>';
    }

    public function filterTheContent($postContent)
    {
        if (!$this->getPerPostWidgetId()) {
            return $postContent;
        }
        $postUrl = get_permalink();
        $disabledUrls = $this->getPerPostWidgetDisabledUrls();
        if ($disabledUrls) {
            $a = explode("\n", $disabledUrls);
            global $post;
            if (in_array($postUrl, $a) || in_array($post->ID, $a)) {
                return $postContent;
            }
        }

        $widgetDiv = '<div class="sfwidget" data-widget="' . $this->getPerPostWidgetId() . '" data-url="' . $postUrl . '"></div>';
        $postContent .= '<div style="margin-top: 10px; height:20px;">' .
            $widgetDiv . '</div>' .
            '<div style="clear:both"></div>' .
            '<div style="height:20px;"></div>';
        return $postContent;
    }

    public function actionAdminMenu()
    {
		add_submenu_page(
		    'options-general.php',
		    __('Sharingforce Config'),
		    __('Sharingforce Config'),
		    'manage_options',
		    'sharingforce-config',
		    array($this, 'config')
        );
    }

    public function config()
    {
        if (isset($_GET['clear'])) {
            $this->clearSharingforceData();
            return;
        }
        if (isset($_GET['authorized'])) {
            $this->finalizeAuthorization();
        }
        $this->showConfigForm();
    }

    private function finalizeAuthorization()
    {
        if ($this->isEnabled()) {
            return;
        }
        $result = wp_remote_post(
            'https://www.sharingforce.com/wordpress-plugin/connect/finalize',
            array('body' => array(
                'siteUrl' => site_url(),
                'secret' => get_option(self::SECRET_OPTION_NAME),
                'apiPublicKey' => get_option(self::API_PUBLIC_KEY_OPTION_NAME)
            ))
        );
        if ($result instanceof WP_Error) {
            $this->authorizationMessage = '<b>Error:</b> could not connect to Sharingforce server at this time, please try again later. Error: ' . $result->get_error_message();
            return;
        }
        if (200 != $result['response']['code']) {
            $this->authorizationMessage = '<b>Error:</b> could not connect to Sharingforce server at this time, please try again later.';
            return;
        }
        $a = explode("\n", $result['body']);
        if (1 == count($a)) {
            $this->authorizationMessage = '<b>Error:</b> ' . $a[0];
            return;
        }
        if (5 != count($a)) {
            $this->authorizationMessage = '<b>Error:</b> malformed response received from Sharingforce.';
            return;
        }
        update_option(self::API_SECRET_KEY_OPTION_NAME, $a[0]);
        update_option(self::PER_PAGE_WIDGET_ID_OPTION_NAME, $a[1]);
        update_option(self::PER_PAGE_WIDGET_NAME_OPTION_NAME, $a[2]);
        update_option(self::PER_POST_WIDGET_ID_OPTION_NAME, $a[3]);
        update_option(self::PER_POST_WIDGET_NAME_OPTION_NAME, $a[4]);
        $this->clearCache();
        $this->authorizationMessage = '<b>Congratulations!</b> Your WordPress&trade; installation is now connected to your Sharingforce&trade; account.';
    }

    private function clearSharingforceData()
    {
        delete_option(self::API_PUBLIC_KEY_OPTION_NAME);
        delete_option(self::API_SECRET_KEY_OPTION_NAME);
        delete_option(self::PER_PAGE_WIDGET_ID_OPTION_NAME);
        delete_option(self::PER_PAGE_WIDGET_NAME_OPTION_NAME);
        delete_option(self::PER_POST_WIDGET_ID_OPTION_NAME);
        delete_option(self::PER_POST_WIDGET_NAME_OPTION_NAME);
        delete_option(self::PER_POST_WIDGET_DISABLED_URLS_OPTION_NAME);
        require_once(dirname(__FILE__) . '/view/Cleared.php');
        $continueUrl = $_SERVER['REQUEST_URI'];
        $continueUrl = Sharingforce_UrlService::removeParam(
            $continueUrl,
            'clear'
        );
        $continueUrl = Sharingforce_UrlService::removeParam(
            $continueUrl,
            'authorized'
        );
        Sharingforce_View_Cleared::getInstance()
            ->setContinueUrl($continueUrl)
            ->render();
    }

    private function isEnabled()
    {
        return $this->getApiPublicKey() && $this->getApiSecretKey();
    }

    private function showConfigForm()
    {
        require_once(dirname(__FILE__) . '/view/Config.php');
        $view = Sharingforce_View_Config::getInstance()
            ->setIsEnabled($this->isEnabled());
        if ($view->getIsEnabled()) {
            $view
                ->setPerPageWidgetId($this->getPerPageWidgetId())
                ->setPerPageWidgetName($this->getPerPageWidgetName())
                ->setPerPostWidgetId($this->getPerPostWidgetId())
                ->setPerPostWidgetName($this->getPerPostWidgetName())
                ->setSettingsGroupName(self::WIDGETS_OPTIONS_GROUP_NAME)
                ->setUrlWidgetAdd(Sharingforce_UrlService::widgetAddUrl())
                ->setPerPostWidgetDisabledUrls(
                    $this->getPerPostWidgetDisabledUrls()
                )
                ->setPerPageWidgetDisabledUrls(
                    $this->getPerPageWidgetDisabledUrls()
                );
        } else {
            global $current_user;
            get_currentuserinfo();
            $secret = $this->getNonce();
            update_option(self::SECRET_OPTION_NAME, $secret);
            $view
                ->setFirstName($current_user->user_firstname)
                ->setLastName($current_user->user_lastname)
                ->setEmail($current_user->user_email)
                ->setUrl(site_url())
                ->setBlogName(get_bloginfo('name'))
                ->setIsEnterApiKeysForm(isset($_GET['skip']))
                ->setBackUrl($_SERVER['REQUEST_URI'])
                ->setSecret($secret);
        }
        $view
            ->setAuthorizationMessage($this->authorizationMessage)
            ->render();
    }

    private function getNonce()
    {
        return wp_create_nonce(self::NONCE);
    }

    private function addOgTags()
    {
		if (is_admin() || (!is_single() && !is_page())) {
		    return;
        }
        global $post;
        $imageUrl = null;
        if (get_the_post_thumbnail($post->ID, 'thumbnail')) {
            $thumbnail_id = get_post_thumbnail_id($post->ID);
            $thumbnail_object = get_post($thumbnail_id);
            $imageUrl = $thumbnail_object->guid;
        } else {
            $args = array(
                'order' => 'ASC',
                'orderby' => 'menu_order',
                'post_type' => 'attachment',
                'post_parent' => $post->ID,
                'post_mime_type' => 'image',
                'post_status' => null,
                'numberposts' => 1
            );
	        $attachments = get_posts($args);
            if ($attachments) {
                $imageUrl = wp_get_attachment_url(
                    $attachments[0]->ID,
                    'thumbnail',
                    false,
                    false
                );
            }
        }
        if ($post->post_excerpt) {
            $description = $post->post_excerpt;
        } else {
            $description = $post->post_content;
        }
	    $description = strip_shortcodes($description);
	    $description = apply_filters('the_content', $description);
	    $description = strip_tags($description);
        $description = GetStringBeginningWithEllipsis($description, 200);
        $description = str_replace("\"", "'", $description);

        if ($imageUrl) {
            echo '<meta property="og:image" content="' . $imageUrl . '" />';
        }
        if ($description) {
            echo '<meta property="og:description" content="' . $description .
                '" />';
        }
	}

    public function xmlrpcMethods()
    {
        return array(
            'sharingforce.xmlrpcApiPublicKey' =>
                array($this, 'xmlrpcApiPublicKey')
        );
    }

    public function xmlrpcApiPublicKey($args)
    {
        if (count($args) != 2) {
            return 'Incorrect call: 1 argument expected';
        }
        if (!isset($args['secret'])) {
            return 'Incorrect call: secret is missing';
        }
        if (!isset($args['apiPublicKey'])) {
            return 'Incorrect call: apiPublicKey is missing';
        }
        if ($args['secret'] != get_option(self::SECRET_OPTION_NAME)) {
            return 'Incorrect secret';
        }
        update_option(
            self::API_PUBLIC_KEY_OPTION_NAME,
            $args['apiPublicKey']
        );
        return 'k';
    }

}