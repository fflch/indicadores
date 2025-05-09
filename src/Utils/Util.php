<?php

namespace Drupal\indicadores\Utils;

use Drupal\webform\Entity\Webform;

Class Util {
    public static function criaLista($url){
        $webforms = Webform::loadMultiple();


        
        $str = '<ul>';

        foreach($webforms as $webform){
            $id = $webform->id();
            $label = $webform->label();
            
            $str .= "<li>$label <a href='/$url/$id/pdf'>pdf</a> <a href='/$url/$id/excel'>excel</a></li>";
        }

        $str .= '</ul>';

        return $str;
    }
}