<?php

namespace Drupal\indicadores\Relatorios;

use Drupal\webform\Entity\Webform;

Class AssistenciaNormalizacao {
    public static function prepareData($webform){
        $assistencia_normalizacao = [];

        $submissions = \Drupal::entityTypeManager()
            ->getStorage('webform_submission')
            ->loadByProperties(['webform_id' => $webform->id()]);

        $total_assistencias             = 0;
        $total_normalizacao_documento   = 0;
        $total_normalizacao_referencias = 0;

        foreach($submissions as $submission){
            if ($submission->isDraft()) continue;

            $data = $submission->getData();
            
            $unidade = strtoupper($submission->getOwner()->getDisplayName());

            $total_assistencias             += self::toInt($data['assistencias_efetuadas']);
            $total_normalizacao_documento   += self::toInt($data['documento_inteiro']);
            $total_normalizacao_referencias += self::toInt($data['referencias_bibliograficas']);

            $linha = [
                $unidade, 
                $data['assistencias_efetuadas'], 
                $data['documento_inteiro'], 
                $data['referencias_bibliograficas'],
            ];

            array_push($assistencia_normalizacao, $linha);
        }

        array_push($assistencia_normalizacao, [
            'TOTAL',
            $total_assistencias,
            $total_normalizacao_documento,
            $total_normalizacao_referencias,
        ]);
        
        return $assistencia_normalizacao;
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
    
        <h2>Assistência ao Usuário e Normalização Técnica</h2>
        <table>
            <thead>
                <tr>
                    <th rowspan="2">Biblioteca</th>
                    <th rowspan="2">Número de Assistências Efetuadas</th>
                    <th colspan="2">Normalização Técnica</th>
                </tr>
                <tr>
                    <th>Documento como um todo</th>
                    <th>Referências Bibliográficas</th>
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