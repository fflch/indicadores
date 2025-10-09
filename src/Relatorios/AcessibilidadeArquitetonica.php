<?php

namespace Drupal\indicadores\Relatorios;

use Drupal\webform\Entity\Webform;

Class AcessibilidadeArquitetonica {

    public static function prepareData($webform){
        $acessibilidade = [];

        $counterSim = [
            'atendimentoAcessivel' => 0,
            'mobiliarioAcessivel'  => 0,
            'rampaDeAcesso'        => 0,
            'sinalizacaoTatil'     => 0,
            'sinalizacaoVisual'    => 0,
            'sinalizacaoSonora'    => 0,
            'ambientesAcessiveis'  => 0
        ];

        $counterNao = $counterSim;

        $submissions = \Drupal::entityTypeManager()
            ->getStorage('webform_submission')
            ->loadByProperties(['webform_id' => $webform->id()]);

        foreach($submissions as $submission){
            if ($submission->isDraft()) continue;

            $data = $submission->getData();
            $unidade = strtoupper($submission->getOwner()->getDisplayName());
    
            $respostas = [
                'atendimentoAcessivel' => $data['espaco_atendimento_adaptado_seg_versao'],
                'mobiliarioAcessivel'  => $data['mobiliario_adaptado_seg_versao'],
                'rampaDeAcesso'        => $data['rampa_acesso_adaptada'],
                'sinalizacaoTatil'     => $data['sinalizacao_tatil'],
                'sinalizacaoVisual'    => $data['sinalizacao_visual'],
                'sinalizacaoSonora'    => $data['sinalizacao_sonora'],
                'ambientesAcessiveis'  => $data['desobstrucao_ambientes_seg_versao'],
            ];

            foreach($respostas as $chave => $valor) {
                if($valor === 'Sim'){
                    $counterSim[$chave]++;
                } else {
                    $counterNao[$chave]++;
                }
            }

            array_push($acessibilidade, array_merge([$unidade], array_values($respostas)));
        }

        $counterTotal = [];

        foreach ($counterSim as $chave => $valorSim) {
            $valorNao = $counterNao[$chave];
            $counterTotal[$chave] = $valorSim + $valorNao;
        }

        array_push($acessibilidade, array_merge(['TOTAL SIM'], array_values($counterSim)));
        array_push($acessibilidade, array_merge(['TOTAL NÃO'], array_values($counterNao)));
        array_push($acessibilidade, array_merge(['TOTAL'], array_values($counterTotal)));
        
        return $acessibilidade;
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
        </style>
        <table>
            <thead>
                <tr>
                    <th rowspan="2">Biblioteca</th>
                    <th colspan="7">Acessibilidade Arquitetônica ou Física</th>
                </tr>
                <tr>
                    <th>Espaço para atendimento acessível</th>
                    <th>Mobiliário acessível</th>
                    <th>Rampa de acesso com corrimão</th>
                    <th>Sinalização Tátil</th>                    
                    <th>Sinalização Visual</th>
                    <th>Sinalização Sonora</th>
                    <th>Ambientes acessíveis para a movimentação/deslocamento/circulação de pessoas com deficiência e mobilidade reduzida</th>
                </tr>
            </thead>
            <tbody>';
        
        foreach ($dataArray as $linha) {
            $html .= '<tr>';
            foreach ($linha as $cell) {
                $html .= '<td>' . htmlspecialchars($cell) . '</td>';
            }
            $html .= '</tr>';
        }
    
        $html .= '</tbody></table>';
    
        return $html;
    }
}