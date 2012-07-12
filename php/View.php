<?php

abstract class Sharingforce_View
{
    public function render()
    {
        include($this->getPhtmlFileName());
    }

    abstract protected function getPhtmlFileName();
}