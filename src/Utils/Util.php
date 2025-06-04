<?php

namespace Drupal\indicadores\Utils;

use Drupal\webform\Entity\Webform;

Class Util {

    // Anna: Explicar o que essa função faz, criar lista? qual lista?
    public static function createList($url){
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

    /*
     * Dado uma sigla, retorna o tipo da unidade na USP
     */
    public static function classificacao_unidades_usp($sigla){
        $ensino = ['fflch','fe','eca'];
        $museus = ['mac','mae'];

        if(in_array($sigla,$ensino )) return 'Ensino e Pesquisa';
        if(in_array($sigla,$ensino )) return 'Museus';

    }
  
}