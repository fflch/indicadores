<?php

namespace Drupal\indicadores\Relatorios;

use Drupal\webform\Entity\Webform;

class CapacitacaoParticipantes {

    public static function prepareData($webform) {
        $capacitacoes = [];

        $submissions = \Drupal::entityTypeManager()
            ->getStorage('webform_submission')
            ->loadByProperties(['webform_id' => $webform->id()]);

        foreach ($submissions as $submission) {
            if ($submission->isDraft()) continue;

            $data = $submission->getData();
            $unidade = strtoupper($submission->getOwner()->getDisplayName());

            if (!isset($capacitacoes[$unidade])) {
                $capacitacoes[$unidade] = [
                    'unidade'    => $unidade,
                    'disciplina' => 0,
                    'palestra'   => 0,
                    'visita'     => 0,
                    'curso'      => 0,
                    'aulas'      => 0,
                    'outros'     => 0,
                    'total'      => 0,
                ];
            }

            foreach ($data['formulario_coleta_de_dados'] as $coleta) {
                $tipo = $coleta['tipo'] ?? null;
            
                if (is_array($tipo)) {
                    $tipo = reset($tipo); // garante que seja escalar
                }
            
                $participantes = self::toInt($coleta['participantes']);
            
                switch ($tipo) {
                    case '1': $capacitacoes[$unidade]['disciplina'] += $participantes; break;
                    case '2': $capacitacoes[$unidade]['palestra']   += $participantes; break;
                    case '3': $capacitacoes[$unidade]['visita']     += $participantes; break;
                    case '4': $capacitacoes[$unidade]['curso']      += $participantes; break;
                    case '5': $capacitacoes[$unidade]['aulas']      += $participantes; break;
                    case '6': $capacitacoes[$unidade]['outros']     += $participantes; break;
                }
            
                $capacitacoes[$unidade]['total'] += $participantes;
            }
        }

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
    
        <h2>Capacitação de Usuários pelas Bibliotecas da USP - Nº de Participantes por tipo de capacitação</h2>
        <table>
            <thead>
                <tr>
                    <th>Unidade</th>
                    <th>Disciplina</th>
                    <th>Palestra</th>
                    <th>Visita Orientada</th>
                    <th>Curso/Treinamento</th>
                    <th>Aulas a Convite da Unidade</th>
                    <th>Outros</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>';
    
        foreach ($dataArray as $linha) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($linha['unidade']) . '</td>';
            $html .= '<td>' . $linha['disciplina'] . '</td>';
            $html .= '<td>' . $linha['palestra'] . '</td>';
            $html .= '<td>' . $linha['visita'] . '</td>';
            $html .= '<td>' . $linha['curso'] . '</td>';
            $html .= '<td>' . $linha['aulas'] . '</td>';
            $html .= '<td>' . $linha['outros'] . '</td>';
            $html .= '<td><strong>' . $linha['total'] . '</strong></td>';
            $html .= '</tr>';
        }
    
        $html .= '</tbody></table>';
    
        return $html;
    } 
    
    private static function toInt($value) {
        return (int) preg_replace('/\D/', '', $value ?? '');
    }
}
