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

                switch ($coleta['tipo']) {
                    case '1':  $tipo = 'Apresentação em Teatro'; break;
                    case '2':  $tipo = 'Comunicação'; break;
                    case '3':  $tipo = 'Concerto'; break;
                    case '4':  $tipo = 'Conferência'; break;
                    case '5':  $tipo = 'Congresso'; break;
                    case '6':  $tipo = 'Convenção'; break;
                    case '7':  $tipo = 'Exposição'; break;
                    case '8':  $tipo = 'Grupo de estudos'; break;
                    case '9':  $tipo = 'Mesa Redonda'; break;
                    case '10': $tipo = 'Mostra'; break;
                    case '11': $tipo = 'Palestra'; break;
                    case '12': $tipo = 'Seminário'; break;
                    case '13': $tipo = 'Simpósio'; break;
                    case '14': $tipo = 'Workshop'; break;
                    case '15': $tipo = 'Coletiva de Autores'; break;
                    case '16': $tipo = 'Lançamento de Livros'; break;
                    case '17': $tipo = 'Lançamento de Produtos/Serviços'; break;
                    case '18': $tipo = 'Outros'; break;
                    default:   $tipo = 'Não informado'; break;
                }

                $nivel_participacao = ($coleta['nivel_participacao'] == '1') ? 'Evento Totalmente realizado pela Biblioteca' : 'Participação na Comissão Organizadora';
    
                $texto = $coleta['nome'] . "\n"
                . $coleta['periodo'] . "\n"
                . $coleta['apoiador'] . "\n"
                . $tipo . "\n"
                . $nivel_participacao . "\n";
            
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