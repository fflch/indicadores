<?php

namespace Drupal\indicadores\Relatorios;

use Drupal\webform\Entity\Webform;

Class EquipamentosInformatica {
    public static function prepareData($webform){
        $equipamentos = [];

        $submissions = \Drupal::entityTypeManager()
            ->getStorage('webform_submission')
            ->loadByProperties(['webform_id' => $webform->id()]);

        foreach($submissions as $submission){
            if ($submission->isDraft()) continue;

            $data = $submission->getData();
            
            $unidade = strtoupper($submission->getOwner()->getDisplayName());

            $total_microcomputador_usuario     += self::toInt($data['equipamentos_usuarios_microcomputador']);
            $total_microcomputador_funcionario += self::toInt($data['equipamentos_funcionarios_microcomputador']);
            $total_impressora_usuario          += self::toInt($data['equipamentos_usuarios_impressora']);
            $total_impressora_funcionario      += self::toInt($data['equipamentos_funcionarios_impressora']);
            $total_scanner_usuario             += self::toInt($data['equipamentos_usuarios_scanner']);
            $total_scanner_funcionario         += self::toInt($data['equipamentos_funcionarios_scanner']);
            $total_outros_usuario              += self::toInt($data['equipamentos_usuarios_outros']);
            $total_outros_funcionario          += self::toInt($data['equipamentos_funcionarios_outros']);

            $linha = [
                $unidade, 
                $data['equipamentos_usuarios_microcomputador'], 
                $data['equipamentos_funcionarios_microcomputador'], 
                $data['equipamentos_usuarios_impressora'], 
                $data['equipamentos_funcionarios_impressora'], 
                $data['equipamentos_usuarios_scanner'], 
                $data['equipamentos_funcionarios_scanner'], 
                $data['equipamentos_usuarios_outros'], 
                $data['equipamentos_funcionarios_outros']
            ];

            array_push($equipamentos, $linha);
        }

        array_push($equipamentos, [
            'TOTAL',
            $total_microcomputador_usuario,
            $total_microcomputador_funcionario,
            $total_impressora_usuario,
            $total_impressora_funcionario,
            $total_scanner_usuario,
            $total_scanner_funcionario,
            $total_outros_usuario,
            $total_outros_funcionario
        ]);
        
        return $equipamentos;
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
    
        <h2>Equipamentos de Informática nas Bibliotecas da USP</h2>
        <table>
            <thead>
                <tr>
                    <th rowspan="2">Biblioteca</th>
                    <th colspan="2">Microcomputador - Nº de Equipamentos</th>
                    <th colspan="2">Impressora - Nº de Equipamentos</th>
                    <th colspan="2">Scanner - Nº de Equipamentos</th>
                    <th colspan="2">Outros - Nº de Equipamentos</th>
                </tr>
                <tr>
                    <th>Usuários</th>
                    <th>Funcionários</th>
                    <th>Usuários</th>
                    <th>Funcionários</th>
                    <th>Usuários</th>
                    <th>Funcionários</th>
                    <th>Usuários</th>
                    <th>Funcionários</th>
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