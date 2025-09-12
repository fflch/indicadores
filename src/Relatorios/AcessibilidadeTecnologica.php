<?php

namespace Drupal\indicadores\Relatorios;

use Drupal\webform\Entity\Webform;

Class AcessibilidadeTecnologica {

    public static function prepareData($webform){
        $acessibilidade = [];

        $counterSim = [
            'planoAquisicaoConteudoAcessivel' => 0,
            'acervoAcessivel'                 => 0,
            'sitesProgramasAcessiveis'        => 0,
            'servicosBraille'                 => 0,
            'leituraDeTela'                   => 0,
            'tecladoVirtual'                  => 0
        ];

        $counterNao = $counterSim;

        $submissions = \Drupal::entityTypeManager()
            ->getStorage('webform_submission')
            ->loadByProperties(['webform_id' => $webform->id()]);

        foreach($submissions as $submission){
            if ($submission->isDraft()) continue;

            $data = $submission->getData();
            $unidade = strtoupper($submission->getOwner()->getDisplayName());

            if ($data['teclado_virtual'] == 1) {
                $respostas = [
                    'planoAquisicaoConteudoAcessivel' => $data['plano_aquisicao_bibliografica_adaptada_seg_versao'],
                    'acervoAcessivel'                 => $data['acervo_adaptado_seg_versao'],
                    'sitesProgramasAcessiveis'        => $data['websites_apps_adaptados_seg_versao'],
                    'servicosBraille'                 => $data['impressoras_braille_seg_versao'],
                    'leituraDeTela'                   => $data['software_leitura_acessivel_seg_versao'],
                    'tecladoVirtual'                  => 'Sim'
                ];
            } else {
                $respostas = [
                    'planoAquisicaoConteudoAcessivel' => $data['plano_aquisicao_bibliografica_adaptada_seg_versao'],
                    'acervoAcessivel'                 => $data['acervo_adaptado_seg_versao'],
                    'sitesProgramasAcessiveis'        => $data['websites_apps_adaptados_seg_versao'],
                    'servicosBraille'                 => $data['impressoras_braille_seg_versao'],
                    'leituraDeTela'                   => $data['software_leitura_acessivel_seg_versao'],
                    'tecladoVirtual'                  => 'Não'
                ];
            }

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
                    <th colspan="3">Acessibilidade de Conteúdo</th>
                    <th colspan="3">Acessibilidade de Tecnologia</th>
                </tr>
                <tr>
                    <th>Plano de aquisição gradual de acervo bibliográfico dos conteúdos básicos em formato acessível</th>
                    <th>Acervo em formato acessível para pessoas com deficiência visual (cegueira e baixa visão)</th>
                    <th>Sítios, plataformas e programas acessíveis para que pessoas com deficiência naveguem e utilizem os serviços oferecidos com autonomia</th>
                    <th>Serviços de impressão em Braille</th>                    
                    <th>Leitores de tela para pessoas com deficiência visual (cegueira e baixa visão)</th>
                    <th>Teclado virtual</th>
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