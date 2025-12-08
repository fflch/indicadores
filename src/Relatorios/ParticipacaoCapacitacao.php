<?php

namespace Drupal\indicadores\Relatorios;

use Drupal\webform\Entity\Webform;

Class ParticipacaoCapacitacao {
    public static function prepareData($webform){
        $numeros_capacitacao = [];
    
        $submissions = \Drupal::entityTypeManager()
            ->getStorage('webform_submission')
            ->loadByProperties(['webform_id' => $webform->id()]);
    
        // ---------- TOTAL GERAL DE FUNCIONÁRIOS (por grupo e categoria) ----------
        $grupos = ['superior', 'tecnico', 'basico'];
        $categorias = ['eventos', 'especializacao', 'mestrado', 'doutorado', 'cursos'];
    
        $totais_globais = [];
        foreach ($grupos as $g) {
            foreach ($categorias as $cat) {
                $totais_globais[$g][$cat] = 0;
            }
        }
    
        $total_geral_capacitacao = 0;
    
        foreach($submissions as $submission){
            if ($submission->isDraft()) continue;
    
            $data = $submission->getData();
            
            $unidade = strtoupper($submission->getOwner()->getDisplayName());
    
            // Totais por unidade
            $totais_unidade = ['superior' => 0, 'tecnico' => 0, 'basico' => 0];
    
            foreach ($grupos as $g) {
                foreach ($categorias as $cat) {
    
                    $key  = "{$g}_{$cat}";
                    $valor = self::toInt($data[$key] ?? 0);
    
                    // soma no total da unidade
                    $totais_unidade[$g] += $valor;
    
                    // soma no total global
                    $totais_globais[$g][$cat] += $valor;
                }
            }
    
            $total_superior_unidade = $totais_unidade['superior'];
            $total_tecnico_unidade  = $totais_unidade['tecnico'];
            $total_basico_unidade   = $totais_unidade['basico'];
    
            $total_geral_unidade = array_sum($totais_unidade);
    
            $total_geral_capacitacao += $total_geral_unidade;
    
            // Monta linha
            $linha = [
                $unidade,
    
                // categorias — essas você pode automatizar depois se quiser
                $data['superior_eventos'], 
                $data['tecnico_eventos'],
                $data['basico_eventos'],
                
                $data['superior_especializacao'], 
                $data['tecnico_especializacao'], 
                $data['basico_especializacao'],
                
                $data['superior_mestrado'], 
                $data['tecnico_mestrado'], 
                $data['basico_mestrado'],
                
                $data['superior_doutorado'], 
                $data['tecnico_doutorado'], 
                $data['basico_doutorado'],
    
                // cursos
                $data['superior_cursos'], 
                $data['tecnico_cursos'], 
                $data['basico_cursos'],
    
                // totais por unidade
                $total_superior_unidade,
                $total_tecnico_unidade,
                $total_basico_unidade,
                $total_geral_unidade,
            ];
    
            $numeros_capacitacao[] = $linha;
        }

        // ---------- LINHA FINAL DOS TOTAIS GERAIS ----------
        $linha_totais = ['TOTAL'];

        foreach ($grupos as $g) {
            foreach ($categorias as $cat) {
                $linha_totais[] = $totais_globais[$g][$cat];
            }
        }

        foreach ($grupos as $g) {
            $linha_totais[] = array_sum($totais_globais[$g]);
        }

        $linha_totais[] = $total_geral_capacitacao;

        // adiciona linha final no array principal
        array_push($numeros_capacitacao, $linha_totais);

        return $numeros_capacitacao;
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
    
        <h2>Participação das Equipes das Bibliotecas em Ações de Capacitação</h2>
        <table>
            <thead>
                <tr>
                    <th rowspan="2">Biblioteca</th>
                    <th colspan="3">Eventos</th>
                    <th colspan="3">Pós-Graduação - Especialização</th>
                    <th colspan="3">Pós-Graduação - Mestrado</th>            
                    <th colspan="3">Pós-Graduação - Doutorado</th>              
                    <th colspan="3">Cursos</th>                          
                    <th colspan="3">Total</th>
                    <th rowspan="2">Total</th>                    
                </tr>
                <tr>
                    <th>Sup.</th>
                    <th>Téc.</th>
                    <th>Bás.</th>
                    <th>Sup.</th>
                    <th>Téc.</th>
                    <th>Bás.</th>
                    <th>Sup.</th>
                    <th>Téc.</th>
                    <th>Bás.</th>
                    <th>Sup.</th>
                    <th>Téc.</th>
                    <th>Bás.</th>           
                    <th>Sup.</th>
                    <th>Téc.</th>
                    <th>Bás.</th>
                    <th>Sup.</th>
                    <th>Téc.</th>
                    <th>Bás.</th>                                                                     
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