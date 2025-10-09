<?php

namespace Drupal\indicadores\Relatorios;

use Drupal\webform\Entity\Webform;

Class InternetRedes {
    public static function prepareData($webform){
        $internetRedes = [
            ['Unidade', 'Oferece serviços pela internet?', 'Possui rede sem fio?']
        ];

        $counterSim = [
            'ofereceServicosInternet' => 0,
            'possuiRedeSemFio'         => 0,
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
                'ofereceServicosInternet' => $data['servicos_pela_internet'],
                'possuiRedeSemFio'        => $data['rede_sem_fio'],
            ];

            foreach($respostas as $chave => $valor) {
                if($valor === 'Sim'){
                    $counterSim[$chave]++;
                } else {
                    $counterNao[$chave]++;
                }
            }

            array_push($internetRedes, array_merge([$unidade], array_values($respostas)));
        }

        $counterTotal = [];

        foreach ($counterSim as $chave => $valorSim) {
            $valorNao = $counterNao[$chave];
            $counterTotal[$chave] = $valorSim + $valorNao;
        }

        array_push($internetRedes, array_merge(['TOTAL SIM'], array_values($counterSim)));
        array_push($internetRedes, array_merge(['TOTAL NÃO'], array_values($counterNao)));
        array_push($internetRedes, array_merge(['TOTAL'], array_values($counterTotal)));
        
        return $internetRedes;
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
        <table>';
        
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