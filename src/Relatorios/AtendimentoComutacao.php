<?php

namespace Drupal\indicadores\Relatorios;

use Drupal\webform\Entity\Webform;

Class AtendimentoComutacao {
    public static function prepareData($webform){
        $atendimento_comutacao = [];

        $submissions = \Drupal::entityTypeManager()
            ->getStorage('webform_submission')
            ->loadByProperties(['webform_id' => $webform->id()]);

        $total_abcd          = 0;
        $total_comut         = 0;
        $total_outros        = 0;
        $total_internacional = 0;

        $campos_total_unidade = [
            'pedidos_atendidos_nacional_bibliusp',
            'pedidos_atendidos_nacional_comut',
            'pedidos_atendidos_nacional_outros',
            'pedidos_atendidos_internacional',
        ];

        foreach($submissions as $submission){
            if ($submission->isDraft()) continue;

            $data = $submission->getData();
            
            $unidade = strtoupper($submission->getOwner()->getDisplayName());

            $total_abcd          += self::toInt($data['pedidos_atendidos_nacional_bibliusp']);
            $total_comut         += self::toInt($data['pedidos_atendidos_nacional_comut']);
            $total_outros        += self::toInt($data['pedidos_atendidos_nacional_outros']);
            $total_internacional += self::toInt($data['pedidos_atendidos_internacional']);
            
            $total_unidade = 0;
            foreach($campos_total_unidade as $campo){
                $total_unidade += self::toInt($data[$campo]); 
            }

            $linha = [
                $unidade, 
                $data['pedidos_atendidos_nacional_bibliusp'], 
                $data['pedidos_atendidos_nacional_comut'], 
                $data['pedidos_atendidos_nacional_outros'],
                $data['pedidos_atendidos_internacional'],
                $total_unidade
            ];

            array_push($atendimento_comutacao, $linha);
        }

        $total = $total_abcd + $total_comut + $total_outros + $total_internacional;

        array_push($atendimento_comutacao, [
            'TOTAL',
            $total_abcd,
            $total_comut,
            $total_outros,
            $total_internacional,
            $total,
        ]);
        
        return $atendimento_comutacao;
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
    
        <h2>Atendimento de Comutação Bibliográfica pelas Bibliotecas da USP</h2>
        <table>
            <thead>
                <tr>
                    <th rowspan="2">Biblioteca</th>
                    <th colspan="3">Comutação Nacional</th>
                    <th rowspan="2">Comutação Internacional</th>
                    <th rowspan="2">Total</th>
                </tr>
                <tr>
                    <th>ABCD</th>
                    <th>COMUT</th>
                    <th>OUTROS</th>
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