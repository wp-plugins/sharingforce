<?php

require_once(dirname(dirname(__FILE__)) . '/View.php');

class Sharingforce_View_AlmostReadyMessage extends Sharingforce_View
{
    // have to have it here for backward compatibility with PHP <5.3
    static public function getInstance()
    {
        return new self;
    }

    protected function getPhtmlFileName()
    {
        return dirname(__FILE__) . '/AlmostReadyMessage.phtml';
    }
}