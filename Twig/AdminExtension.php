<?php

namespace Istorm\Bundle\AdminExtBundle\Twig;

use Twig_Extension;

class AdminExtension extends Twig_Extension
{

    public function getFilters()
    {
        return array();
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('admin_json_decode', array($this, 'jsonDecode'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('is_array', array($this, 'isArray'), array('is_safe' => array('html'))),
        );
    }

    public function getName()
    {
        return 'istorm_admin_twig_extensions';
    }

    public function jsonDecode($json){
        return json_decode($json,true);
    }

    public function isArray($val){
        return is_array($val);
    }

    public function printData($data){

    }

}