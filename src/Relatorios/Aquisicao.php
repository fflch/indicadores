<?php

namespace Drupal\indicadores\Relatorios;

use Drupal\webform\Entity\Webform;

Class Aquisicao {
    public static function prepareData($webform){
        $aquisicao = [];

        $submissions = \Drupal::entityTypeManager()
            ->getStorage('webform_submission')
            ->loadByProperties(['webform_id' => $webform->id()]);

        $total_compra            = 0;
        $total_doacao            = 0;
        $total_livros_compra     = 0;
        $total_livros_doacao     = 0;
        $total_periodicos_compra = 0;
        $total_periodicos_doacao = 0;

        foreach($submissions as $submission){
            if ($submission->isDraft()) continue;

            $data = $submission->getData();
            
            $unidade = strtoupper($submission->getOwner()->getDisplayName());

            $total_livros_compra     += self::toInt($data['livros_compra_seg_versao']);
            $total_livros_doacao     += self::toInt($data['livros_doacao_seg_versao']);
            $total_periodicos_compra += self::toInt($data['periodicos_compra_seg_versao']);
            $total_periodicos_doacao += self::toInt($data['periodicos_doacao_seg_versao']);
            
            $total_compra_unidade = self::toInt($data['livros_compra_seg_versao']) + self::toInt($data['periodicos_compra_seg_versao']);
            $total_doacao_unidade = self::toInt($data['livros_doacao_seg_versao']) + self::toInt($data['periodicos_doacao_seg_versao']);

            $linha = [
                $unidade, 
                $data['livros_compra_seg_versao'], 
                $data['livros_doacao_seg_versao'], 
                $data['periodicos_compra_seg_versao'],
                $data['periodicos_doacao_seg_versao'],
                $total_compra_unidade,
                $total_doacao_unidade
            ];

            array_push($aquisicao, $linha);
        }

        $total_compra = $total_livros_compra + $total_periodicos_compra;
        $total_doacao = $total_livros_doacao + $total_periodicos_doacao;

        array_push($aquisicao, [
            'TOTAL',
            $total_livros_compra,
            $total_livros_doacao,
            $total_periodicos_compra,
            $total_periodicos_doacao,
            $total_compra,
            $total_doacao
        ]);
        
        return $aquisicao;
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
                    <th colspan="2">Livros e Outros Tipos de Materiais</th>
                    <th colspan="2">Periódicos - Títulos Correntes</th>
                    <th colspan="2">Total</th>
                </tr>
                <tr>
                    <th>Compra</th>
                    <th>Doação</th>
                    <th>Compra</th>
                    <th>Doação</th>
                    <th>Compra</th>
                    <th>Doação</th>
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