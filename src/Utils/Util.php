<?php

namespace Drupal\indicadores\Utils;

use Drupal\webform\Entity\Webform;

Class Util {
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

    public static function createExcelTable($webform){
        $capacitacoes = [
            ['Unidade', 'Tipo de Capacitação', 'Nome', 'Total de Ações', 'Público', 'Carga Horária', 'Carga Horária Total', 'Nº Participantes', 'Total de Participantes', 'Data Início', 'Data Término', 'Ministrante' ],
        ];

        $submissions = \Drupal::entityTypeManager()
            ->getStorage('webform_submission')
            ->loadByProperties(['webform_id' => $webform->id()]);

        foreach($submissions as $submission){
            if ($submission->isDraft()) continue;

            $data = $submission->getData();
            $unidade = strtoupper($submission->getOwner()->getDisplayName());

            $quantidade_capacitacoes = 0;
            $quantidade_participantes = 0;
            $quantidade_horas = 0;

            foreach($data['formulario_coleta_de_dados'] as $coleta){
                $quantidade_capacitacoes++;
                $quantidade_participantes += (int)$coleta['participantes'];
                $quantidade_horas += (int)$coleta['carga_horaria'];
            }
            
            $primeira_iteracao = TRUE;

            foreach($data['formulario_coleta_de_dados'] as $coleta){
                if($primeira_iteracao) {
                    $publico = ($coleta['publico'] == '1') ? 'Comunidade USP' : 'Outras Instituições';

                    $linha = [$unidade, $coleta['tipo'], $coleta['nome'], $quantidade_capacitacoes, $publico, $coleta['carga_horaria'], $quantidade_horas, $coleta['participantes'], $quantidade_participantes, $coleta['data_inicio'], $coleta['data_termino'], $coleta['ministrante']];

                    $primeira_iteracao = FALSE;
                } else {
                    $linha = ['', $coleta['tipo'], $coleta['nome'], '', $publico, $coleta['carga_horaria'],'', $coleta['participantes'], '', $coleta['data_inicio'], $coleta['data_termino'], $coleta['ministrante']];
                }

                array_push($capacitacoes, $linha);
            }
        }
        
        return $capacitacoes;
    }

    public static function createHtmlTable(array $dataArray) {
        $html = '
        <style>
            body {
                font-family: Arial, sans-serif;
                font-size: 10px; /* Fonte menor */
                margin: 10px 20px; /* Margens mais estreitas */
            }
            table {
                border-collapse: collapse;
                width: 100%;
            }
            th, td {
                border: 1px solid #444;
                padding: 4px 6px;
                text-align: left;
                vertical-align: top;
            }
            thead th {
                background-color: #ddd;
            }
            tbody tr:nth-child(even) {
                background-color: #f9f9f9;
            }
        </style>
        <table>';
    
        if (count($dataArray) > 0) {
            $html .= '<thead><tr>';
            foreach ($dataArray[0] as $header) {
                $html .= '<th style="background-color: #f0f0f0; text-align: left;">' . htmlspecialchars($header) . '</th>';
            }
            $html .= '</tr></thead>';
        }
    
        $html .= '<tbody>';
        for ($i = 1; $i < count($dataArray); $i++) {
            $html .= '<tr>';
            foreach ($dataArray[$i] as $cell) {
                $html .= '<td>' . htmlspecialchars($cell) . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody>';
    
        $html .= '</table>';
    
        return $html;
    }    
}