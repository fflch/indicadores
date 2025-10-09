<?php

namespace Drupal\indicadores\Relatorios;

use Drupal\webform\Entity\Webform;

class CapacitacaoResumo {

    public static function prepareData($webform) {
        $capacitacoes = [];

        $submissions = \Drupal::entityTypeManager()
            ->getStorage('webform_submission')
            ->loadByProperties(['webform_id' => $webform->id()]);

        foreach ($submissions as $submission) {
            if ($submission->isDraft()) continue;

            $data = $submission->getData();
            $unidade = strtoupper($submission->getOwner()->getDisplayName());

            // se a unidade ainda não existe, inicializa
            if (!isset($capacitacoes[$unidade])) {
                $capacitacoes[$unidade] = [
                    'unidade' => $unidade,
                    'acoes' => 0,
                    'participantes' => 0,
                ];
            }

            // percorre os registros do composite
            foreach($data['formulario_coleta_de_dados'] as $coleta){
                $capacitacoes[$unidade]['acoes']++;
                $capacitacoes[$unidade]['participantes'] += self::toInt($coleta['participantes']);
            }
        }

        // retorna apenas os valores (sem chaves de unidade)
        return array_values($capacitacoes);
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
    
        <h2>Capacitação de Usuários pelas Bibliotecas da USP</h2>
        <table>
            <thead>
                <tr>
                    <th>Unidade</th>
                    <th>Número de Ações de Capacitação</th>
                    <th>Número de Participantes</th>
                </tr>
            </thead>
            <tbody>';
    
        foreach ($dataArray as $linha) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($linha['unidade']) . '</td>';
            $html .= '<td>' . $linha['acoes'] . '</td>';
            $html .= '<td>' . $linha['participantes'] . '</td>';
            $html .= '</tr>';
        }
    
        $html .= '</tbody></table>';
    
        return $html;
    } 
    
    private static function toInt($value) {
        return (int) preg_replace('/\D/', '', $value ?? '');
    }
}
