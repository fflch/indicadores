<?php

namespace Drupal\indicadores\Relatorios;

use Drupal\webform\Entity\Webform;

Class AcervoBibliotecas {
    public static function prepareData(){
        $info_acervos = [
            ['Livros', 'Teses', 'Periódicos - Títulos', 'Periódicos - Fascículos', 'Multimeios', 'Outros', 'Total'],
        ];

        $submissions = \Drupal::entityTypeManager()
            ->getStorage('webform_submission')
            ->loadByProperties(['webform_id' => $webform->id()]);

        foreach($submissions as $submission){
            if ($submission->isDraft()) continue;

            $data = $submission->getData();
            $unidade = strtoupper($submission->getOwner()->getDisplayName());
        }
            
        $primeira_iteracao = TRUE;

        foreach($data['formulario_coleta_de_dados'] as $coleta){
            if($primeira_iteracao) {
                // $publico = ($coleta['XXXX'] == '1') ? 'Comunidade USP' : 'Outras Instituições';

                $linha = [$unidade, $coleta['XXXX'], $coleta['XXXX']];

                $primeira_iteracao = FALSE;
            } else {
                $linha = ['', $coleta['XXXXXXX']];
            }

            array_push($capacitacoes, $linha);
        }
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