<?php

namespace Drupal\indicadores\Relatorios;

use Drupal\webform\Entity\Webform;

Class AcervoFasciculosBacklog {
    public static function prepareData($webform){
        $acervo = [];

        $submissions = \Drupal::entityTypeManager()
            ->getStorage('webform_submission')
            ->loadByProperties(['webform_id' => $webform->id()]);

        $total_materiais_cadastrados     = 0;
        $total_materiais_nao_cadastrados = 0;
        $total_backlog_catalogacao       = 0;

        foreach($submissions as $submission){
            if ($submission->isDraft()) continue;

            $data = $submission->getData();
            
            $unidade = strtoupper($submission->getOwner()->getDisplayName());

            $total_materiais_cadastrados     += (int)$data['cadastrados_atualmente_periodicos'];
            $total_materiais_nao_cadastrados += (int)$data['materiais_nao_cadastrados_periodicos'];
            $total_backlog_catalogacao       += (int)$data['backlog_catalogacao'];

            $linha = [
                $unidade, 
                $data['cadastrados_atualmente_periodicos'], 
                $data['materiais_nao_cadastrados_periodicos'], 
                $data['backlog_catalogacao']
            ];

            array_push($acervo, $linha);
        }

        array_push($acervo, [
            'TOTAL',
            $total_materiais_cadastrados,
            $total_materiais_nao_cadastrados,
            $total_backlog_catalogacao
        ]);
        
        return $acervo;
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
    
        <h2>Acervo de Fascículos de Periódicos e Backlog de Catalogação - 2024</h2>
        <table>
            <thead>
                <tr>
                    <th rowspan="2">Biblioteca</th>
                    <th colspan="2">Periódicos (Fascículos)</th>
                    <th rowspan="2" style="width: 30%;">Backlog Catalogação - Materiais não Cadastrados no DEDALUS (livros, teses, multimeios e outros tipos)</th>
                </tr>
                <tr>
                    <th>Materiais cadastrados no DEDALUS até a presente data</th>
                    <th>Materiais não Cadastrados no DEDALUS</th>
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
}