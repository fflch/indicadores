<?php

namespace Drupal\indicadores\Relatorios;

use Drupal\webform\Entity\Webform;

Class AtendimentoComutacaoSolicitante {
    public static function prepareData($webform){
        $atendimento_comutacao_solicitante = [];

        $submissions = \Drupal::entityTypeManager()
            ->getStorage('webform_submission')
            ->loadByProperties(['webform_id' => $webform->id()]);

        $total_bibliusp      = 0;
        $total_comut         = 0;
        $total_outros        = 0;
        $total_internacional = 0;

        $campos_total_unidade = [
            'solicitante_atendidos_nacional_bibliusp',
            'solicitante_atendidos_nacional_comut',
            'solicitante_atendidos_nacional_outros',
            'solicitante_atendidos_internacional',
        ];

        foreach($submissions as $submission){
            if ($submission->isDraft()) continue;

            $data = $submission->getData();
            
            $unidade = strtoupper($submission->getOwner()->getDisplayName());

            $total_bibliusp      += self::toInt($data['solicitante_atendidos_nacional_bibliusp']);
            $total_comut         += self::toInt($data['solicitante_atendidos_nacional_comut']);
            $total_outros        += self::toInt($data['solicitante_atendidos_nacional_outros']);
            $total_internacional += self::toInt($data['solicitante_atendidos_internacional']);
            
            $total_unidade = 0;
            foreach($campos_total_unidade as $campo){
                $total_unidade += self::toInt($data[$campo]); 
            }

            $linha = [
                $unidade, 
                $data['solicitante_atendidos_nacional_bibliusp'], 
                $data['solicitante_atendidos_nacional_comut'], 
                $data['solicitante_atendidos_nacional_outros'],
                $data['solicitante_atendidos_internacional'],
                $total_unidade
            ];

            array_push($atendimento_comutacao_solicitante, $linha);
        }

        $total = $total_bibliusp + $total_comut + $total_outros + $total_internacional;

        array_push($atendimento_comutacao_solicitante, [
            'TOTAL',
            $total_bibliusp,
            $total_comut,
            $total_outros,
            $total_internacional,
            $total,
        ]);
        
        return $atendimento_comutacao_solicitante;
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
    
        <h2>Atendimento de Comutação Bibliográfica pelas Bibliotecas da USP - como Biblioteca Solicitante</h2>
        <table>
            <thead>
                <tr>
                    <th rowspan="2">Biblioteca</th>
                    <th colspan="3">Comutação Nacional como Biblioteca Solicitante</th>
                    <th rowspan="2">Comutação Internacional como Biblioteca Solicitante</th>
                    <th rowspan="2">Total</th>
                </tr>
                <tr>
                    <th>Bibliotecas USP</th>
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