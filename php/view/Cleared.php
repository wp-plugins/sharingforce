<?php

require_once(dirname(dirname(__FILE__)) . '/View.php');

class Sharingforce_View_Cleared extends Sharingforce_View
{
    /**
     * @var string
     */
    private $continueUrl;

    static public function getInstance()
    {
        return new self;
    }

    protected function getPhtmlFileName()
    {
        return dirname(__FILE__) . '/Cleared.phtml';
    }

    public function setContinueUrl($continueUrl)
    {
        $this->continueUrl = $continueUrl;
        return $this;
    }

    public function getContinueUrl()
    {
        return $this->continueUrl;
    }
}