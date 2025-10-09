<?php

namespace Drupal\indicadores\Relatorios;

use Drupal\webform\Entity\Webform;

Class ConsultaEntreBibliotecas {
    public static function prepareData($webform){
        $consultas = [];

        $submissions = \Drupal::entityTypeManager()
            ->getStorage('webform_submission')
            ->loadByProperties(['webform_id' => $webform->id()]);

        foreach($submissions as $submission){
            if ($submission->isDraft()) continue;

            $data = $submission->getData();
            
            $unidade = strtoupper($submission->getOwner()->getDisplayName());
            
            $linha = [
                $unidade, 
                $data['consultas_acervo'], 
                $data['entre_bibliotecas_biblioteca_solicitante']
            ];

            array_push($consultas, $linha);
        }
        
        return $consultas;
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
    
        <h2>Consultas ao Acervo e Empréstimo entre Bibliotecas como Biblioteca Solicitante/h2>
        <table>
            <thead>
                <tr>
                    <th>Biblioteca</th>
                    <th>Consultas ao Acervo</th>
                    <th>Empréstimos entre Bibliotecas - Como Biblioteca Solicitante</th>
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