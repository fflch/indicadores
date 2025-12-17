<?php

namespace Drupal\indicadores\Relatorios;

use Drupal\webform\Entity\Webform;

Class ProjetosBibliotecas {
    public static function prepareData($webform){
        $projetos = [];
    
        $submissions = \Drupal::entityTypeManager()
            ->getStorage('webform_submission')
            ->loadByProperties(['webform_id' => $webform->id()]);
    
        $total_projetos = 0;
    
        foreach ($submissions as $submission) {
            if ($submission->isDraft()) continue;
    
            $data = $submission->getData();
            $unidade = strtoupper($submission->getOwner()->getDisplayName());
    
            $itens = $data['projetos_seg_versao'] ?? [];
    
            foreach ($itens as $coleta) {
    
                $total_projetos++;
    
                $texto = $coleta['nome'] . "\n"
                . $coleta['descricao'] . "\n"
                . $coleta['data_inicio'] . "\n"
                . $coleta['data_finalizacao'] . "\n"
                . $coleta['apoiador'] . "\n"
                . $coleta['recursos_recebidos'] . "\n"
                . $coleta['equipe_envolvida'] . "\n"
                . $coleta['resultados'] . "\n"
                . $coleta['url_projeto'] . "\n";
            
                $linha = [
                    $unidade,
                    nl2br($texto)   // Para quebras de linhas funcionarem no dompdf
                ];

                array_push($projetos, $linha);
            }
        }
    
        array_push($projetos, [
            'TOTAL',
            $total_projetos,
        ]);
    
        return $projetos;
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
    
        <h2>Projetos das Bibliotecas USP</h2>
        <table>
            <thead>
                <tr>
                    <th>Biblioteca</th>
                    <th>Projetos</th>
                </tr>
            </thead>
            <tbody>';
    
        // Linhas da tabela
        for ($i = 0; $i < count($dataArray); $i++) {
            $html .= '<tr>';
            foreach ($dataArray[$i] as $cell) {
                $html .= '<td>' . $cell . '</td>';
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