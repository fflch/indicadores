<?php

namespace Drupal\indicadores\Utils;

use Drupal\webform\Entity\Webform;

Class Util {
    /*
     * Gera uma lista em HTML de webforms filtrados com o prefixo 'indicadores_abcd',
     * ordenada por nome, com links para exportação em PDF e Excel.
     * Qualquer webform que não tenha o prefixo 'indicadores_abcd' será ignorado.
    */
    public static function createList($url){
        $webforms = Webform::loadMultiple();
    
        $formularios_filtrados = [];
    
        foreach ($webforms as $webform) {
            if (str_starts_with($webform->id(), 'indicadores_abcd')) {
                $formularios_filtrados[] = [
                    'id' => $webform->id(),
                    'label' => $webform->label(),
                ];
            }
        }
    
        usort($formularios_filtrados, function($a, $b){
            return strcmp($a['label'], $b['label']);
        });
    
        $str = '<ul>';
        foreach($formularios_filtrados as $formulario){
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