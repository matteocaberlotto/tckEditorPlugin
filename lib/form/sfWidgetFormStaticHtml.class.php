<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class sfWidgetFormStaticHtml extends sfWidgetForm
{
    protected
        $requiredOptions = array('html');

    public function render($name, $value = null, $attributes = array(), $errors = array())
    {
        return $this->options['html'];
    }
}