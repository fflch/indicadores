<?php

namespace Drupal\indicadores\Relatorios;

use Drupal\webform\Entity\Webform;

Class EventosBibliotecas {
    public static function prepareData($webform){
        $eventos = [];
    
        $submissions = \Drupal::entityTypeManager()
            ->getStorage('webform_submission')
            ->loadByProperties(['webform_id' => $webform->id()]);
    
        $total_eventos = 0;
    
        foreach ($submissions as $submission) {
            if ($submission->isDraft()) continue;
    
            $data = $submission->getData();
            $unidade = strtoupper($submission->getOwner()->getDisplayName());
    
            $itens = $data['eventos_seg_versao'] ?? [];
    
            foreach ($itens as $coleta) {
    
                $total_eventos++;

                // Adicionar um switch aqui para o tipo e para o nível de participação
    
                $texto = $coleta['nome'] . "\n"
                . $coleta['periodo'] . "\n"
                . $coleta['apoiador'] . "\n"
                . $coleta['tipo'] . "\n"
                . $coleta['nivel_participacao'] . "\n";
            
                $linha = [
                    $unidade,
                    nl2br($texto)   // Para quebras de linhas funcionarem no dompdf
                ];

                array_push($eventos, $linha);
            }
        }
    
        array_push($eventos, [
            'TOTAL',
            $total_eventos,
        ]);
    
        return $eventos;
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
    
        <h2>Eventos promovidos pelas Bibliotecas e/ou com Participação das Bibliotecas</h2>
        <table>
            <thead>
                <tr>
                    <th>Biblioteca</th>
                    <th>Eventos</th>
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