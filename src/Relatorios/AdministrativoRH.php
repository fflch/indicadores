<?php

namespace Drupal\indicadores\Relatorios;

use Drupal\webform\Entity\Webform;

Class AdministrativoRH {
    public static function prepareData($webform){
        $recursos_funcionarios = [];

        $submissions = \Drupal::entityTypeManager()
            ->getStorage('webform_submission')
            ->loadByProperties(['webform_id' => $webform->id()]);

        $total_funcionarios   = 0;

        $keys_nivel_superior = [
            'funcionarios_superior',
            'funcionarios_superior_especializacao',
            'funcionarios_superior_mestrado',
            'funcionarios_superior_doutorado',
        ];

        foreach($submissions as $submission){
            if ($submission->isDraft()) continue;

            $data = $submission->getData();
            
            $unidade = strtoupper($submission->getOwner()->getDisplayName());

            $total_nivel_superior = 0;
            foreach($keys_nivel_superior as $key){
                $total_nivel_superior += self::toInt($data[$key]);
            }

            $total_funcionarios = $total_nivel_superior + self::toInt($data['funcionarios_tecnico']) + self::toInt($data['funcionarios_basico']);

            $linha = [
                $unidade, 
                $data['funcionarios_superior'],
                $data['funcionarios_superior_especializacao'],
                $data['funcionarios_superior_mestrado'],
                $data['funcionarios_superior_doutorado'],
                $total_nivel_superior,
                $data['funcionarios_tecnico'],
                $data['funcionarios_basico'],
                $total_funcionarios
            ];

            array_push($recursos_funcionarios, $linha);
        }
        
        return $recursos_funcionarios;
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
    
        <h2>Aquisição de Livros/Outros Tipos de Materiais e Títulos Correntes de Periódicos</h2>
        <table>
            <thead>
                <tr>
                    <th rowspan="2">Biblioteca</th>
                    <th colspan="5">Funcionário de Nível Superior</th>
                    <th rowspan="2">Funcionário de Nível Técnico</th>
                    <th rowspan="2">Funcionário de Nível Básico</th>
                    <th rowspan="2">Total Funcionários</th>
                </tr>
                <tr>
                    <th>Graduação</th>
                    <th>Especialização</th>
                    <th>Mestrado</th>
                    <th>Doutorado</th>
                    <th>Total</th>
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