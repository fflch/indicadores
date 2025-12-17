<?php

namespace Drupal\indicadores\Relatorios;

use Drupal\webform\Entity\Webform;

Class FrequenciaBibliotecas {
    public static function prepareData($webform){
        $frequencia = [];

        $submissions = \Drupal::entityTypeManager()
            ->getStorage('webform_submission')
            ->loadByProperties(['webform_id' => $webform->id()]);

        $total_frequencia_usp     = 0;
        $total_frequencia_externo = 0;

        foreach($submissions as $submission){
            if ($submission->isDraft()) continue;

            $data = $submission->getData();
            
            $unidade = strtoupper($submission->getOwner()->getDisplayName());

            $total_frequencia_usp     += self::toInt($data['usp']);
            $total_frequencia_externo += self::toInt($data['externos_usp']);
            $total_frequencia_unidade = self::toInt($data['usp']) + self::toInt($data['externos_usp']);

            $linha = [
                $unidade, 
                $data['usp'], 
                $data['externos_usp'],
                $total_frequencia_unidade,
            ];

            array_push($frequencia, $linha);
        }

        $total_frequencia_unidade = $total_frequencia_usp + $total_frequencia_externo;

        array_push($frequencia, [
            'TOTAL',
            $total_frequencia_usp,
            $total_frequencia_externo,
            $total_frequencia_unidade
        ]);
        
        return $frequencia;
    }

    public static function createHtmlTable(array $dataArray) {
        $html = '
        <style>
            body {
                font-family: Arial, sans-serif;
                font-size: 10px;
                margin: 10px 20px;
            }
            table {
                border-collapse: collapse;
                width: 100%;
            }
            th, td {
                border: 1px solid #444;
                padding: 4px 6px;
                text-align: center;
                vertical-align: top;
            }
            thead th {
                background-color: #ddd;
            }
            tbody tr:nth-child(even) {
                background-color: #f9f9f9;
            }
            h2 {
                text-align: center;
                margin-top: 0;
            }
        </style>
    
        <h2>Frequência às Bibliotecas da USP, distribuída por Unidade/h2>
        <table>
            <thead>
                <tr>
                    <th rowspan=2>Biblioteca</th>
                    <th colspan=2>Frequencia às Bibliotecas</th>
                    <th rowspan=2>Total</th>
                </tr>
                <tr>
                    <th>Usuários USP</th>
                    <th>Usuários Externos</th>
                </tr>
            </thead>
            <tbody>';
    
        // Linhas da tabela
        for ($i = 0; $i < count($dataArray); $i++) {
            $html .= '<tr>';
            foreach ($dataArray[$i] as $cell) {
                $html .= '<td>' . htmlspecialchars($cell) . '</td>';
            }
            $html .= '</tr>';
        }
    
        $html .= '</tbody></table>';
    
        return $html;
    } 
    
    private static function toInt($value) {
        return (int) preg_replace('/\D/', '', $value ?? '');
    }
}