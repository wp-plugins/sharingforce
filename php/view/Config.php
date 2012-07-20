<?php

require_once(dirname(dirname(__FILE__)) . '/View.php');

class Sharingforce_View_Config extends Sharingforce_View
{
    /**
     * @var int
     */
    private $perPageWidgetId;
    /**
     * @var string
     */
    private $perPageWidgetName;
    /**
     * @var int
     */
    private $perPostWidgetId;
    /**
     * @var string
     */
    private $perPostWidgetName;
    /**
     * @var string
     */
    private $settingsGroupName;
    /**
     * @var string
     */
    private $firstName;
    /**
     * @var string
     */
    private $lastName;
    /**
     * @var string
     */
    private $email;
    /**
     * @var string
     */
    private $url;
    /**
     * @var string
     */
    private $blogName;
    /**
     * @var boolean
     */
    private $isEnabled;
    /**
     * @var boolean
     */
    private $isEnterApiKeysForm;
    /**
     * @var string
     */
    private $urlWidgetAdd;
    /**
     * @var string
     */
    private $backUrl;
    /**
     * @var string
     */
    private $secret;
    /**
     * @var string
     */
    private $authorizationMessage;
    /**
     * @var string
     */
    private $perPostWidgetDisabledUrls;
    /**
     * @var string
     */
    private $perPageWidgetDisabledUrls;

    // have to have it here for backward compatibility with PHP <5.3
    static public function getInstance()
    {
        return new self;
    }

    protected function getPhtmlFileName()
    {
        return dirname(__FILE__) . '/Config.phtml';
    }

    public function setPerPageWidgetId($perPageWidgetId)
    {
        $this->perPageWidgetId = $perPageWidgetId;
        return $this;
    }

    public function getPerPageWidgetId()
    {
        return $this->perPageWidgetId;
    }

    public function setPerPostWidgetId($perPostWidgetId)
    {
        $this->perPostWidgetId = $perPostWidgetId;
        return $this;
    }

    public function getPerPostWidgetId()
    {
        return $this->perPostWidgetId;
    }

    public function setSettingsGroupName($settingsGroupName)
    {
        $this->settingsGroupName = $settingsGroupName;
        return $this;
    }

    public function getSettingsGroupName()
    {
        return $this->settingsGroupName;
    }

    public function setBlogName($blogName)
    {
        $this->blogName = $blogName;
        return $this;
    }

    public function getBlogName()
    {
        return $this->blogName;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;
        return $this;
    }

    public function getIsEnabled()
    {
        return $this->isEnabled;
    }

    public function setIsEnterApiKeysForm($isEnterApiKeysForm)
    {
        $this->isEnterApiKeysForm = $isEnterApiKeysForm;
        return $this;
    }

    public function getIsEnterApiKeysForm()
    {
        return $this->isEnterApiKeysForm;
    }

    public function setPerPageWidgetName($perPageWidgetName)
    {
        $this->perPageWidgetName = $perPageWidgetName;
        return $this;
    }

    public function getPerPageWidgetName()
    {
        return $this->perPageWidgetName;
    }

    public function setPerPostWidgetName($perPostWidgetName)
    {
        $this->perPostWidgetName = $perPostWidgetName;
        return $this;
    }

    public function getPerPostWidgetName()
    {
        return $this->perPostWidgetName;
    }

    public function setUrlWidgetAdd($urlWidgetAdd)
    {
        $this->urlWidgetAdd = $urlWidgetAdd;
        return $this;
    }

    public function getUrlWidgetAdd()
    {
        return $this->urlWidgetAdd;
    }

    public function setBackUrl($backUrl)
    {
        $this->backUrl = $backUrl;
        return $this;
    }

    public function getBackUrl()
    {
        return $this->backUrl;
    }

    public function setSecret($secret)
    {
        $this->secret = $secret;
        return $this;
    }

    public function getSecret()
    {
        return $this->secret;
    }

    public function setAuthorizationMessage($authorizationMessage)
    {
        $this->authorizationMessage = $authorizationMessage;
        return $this;
    }

    public function getAuthorizationMessage()
    {
        return $this->authorizationMessage;
    }

    public function setPerPostWidgetDisabledUrls($perPostWidgetDisabledUrls)
    {
        $this->perPostWidgetDisabledUrls = $perPostWidgetDisabledUrls;
        return $this;
    }

    public function getPerPostWidgetDisabledUrls()
    {
        return $this->perPostWidgetDisabledUrls;
    }

    public function setPerPageWidgetDisabledUrls($perPageWidgetDisabledUrls)
    {
        $this->perPageWidgetDisabledUrls = $perPageWidgetDisabledUrls;
        return $this;
    }

    public function getPerPageWidgetDisabledUrls()
    {
        return $this->perPageWidgetDisabledUrls;
    }
}