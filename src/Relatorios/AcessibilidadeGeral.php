<?php

namespace Drupal\indicadores\Relatorios;

use Drupal\webform\Entity\Webform;

Class AcessibilidadeGeral {

    public static function prepareData($webform){
        $acessibilidade = [];

        $counterSim = [
            'condicoesAcessibilidade' => 0,
            'atendenteLibras'         => 0,
            'banheirosAcessiveis'     => 0,
            'bebedourosAcessiveis'    => 0,
            'entradasAcessiveis'      => 0,
            'equipamentosAcessiveis'  => 0
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
                'condicoesAcessibilidade' => $data['condicoes_de_acessibilidade'],
                'atendenteLibras'         => $data['funcionario_treinado_em_libras'],
                'banheirosAcessiveis'     => $data['banheiros_adaptados'],
                'bebedourosAcessiveis'    => $data['bebedouros_lavabos_adaptados'],
                'entradasAcessiveis'      => $data['dimensionamento_entradas_seg_versao'],
                'equipamentosAcessiveis'  => $data['equipamentos_eletronicos_adaptados'],
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
                    <th colspan="2">Acessibilidade</th>
                    <th colspan="4">Acessibilidade Arquitetônica ou Física</th>
                </tr>
                <tr>
                    <th>Oferece condições de acessibilidade?</th>
                    <th>Atendente treinado na Língua Brasileira de Sinais (Libras)?</th>
                    <th>Banheiros e lavabos acessíveis</th>
                    <th>Bebedouros acessíveis</th>
                    <th>Entrada/saída com vão livre acessível para circulação de pessoas com deficiência e mobilidade reduzida</th>
                    <th>Equipamento eletromecânico (elevadores, esteiras rolantes, entre outros)</th>
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