<?php

namespace Drupal\indicadores\Utils;

use Drupal\webform\Entity\Webform;

Class Util {
    public static function criaLista($url){
        $webforms = Webform::loadMultiple();
        
        $formularios = [];

        foreach($webforms as $webform){
            $formularios[] = [
                'id' => $webform->id(),
                'label' => $webform->label(),
            ];
        }

        usort($formularios, function($a, $b){
            return strcmp($a['label'], $b['label']);
        });

        $str = '<ul>';
        foreach($formularios as $formulario){
            $id = $formulario['id'];
            $label = $formulario['label'];
            
            $str .= "<li>$label <a href='/$url/$id/pdf'>pdf</a> <a href='/$url/$id/excel'>excel</a></li>";
        }
        $str .= '</ul>';

        return $str;
    }
}