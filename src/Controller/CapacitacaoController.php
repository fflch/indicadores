<?php

// http://0.0.0.0:8000/indicadores/capacitacao/indicadores_abcd

namespace Drupal\indicadores\Controller;
use Drupal\Core\Controller\ControllerBase;

use Drupal\webform\Entity\Webform;
use Drupal\webform\Entity\WebformSubmission;

use Shuchkin\SimpleXLSXGen;
use Dompdf\Dompdf;
use Drupal\indicadores\Utils\Util;

class CapacitacaoController extends ControllerBase {
    public function form(){
        $str = Util::criaLista('indicadores/capacitacao');

        $build = [
            '#markup' => $this->t($str),
            ];
        return $build;
    }

    public function excel($webform_id){
        $webform = Webform::load($webform_id);
        
        $capacitacoes = $this->createTable($webform);

        $xlsx = SimpleXLSXGen::fromArray( $capacitacoes );

        $xlsx->downloadAs($webform->label().'.xlsx');
    }

    public function pdf($webform_id){
        $webform = Webform::load($webform_id);
        
        $capacitacoes = $this->createTable($webform); //tranformar em string

        // instantiate and use the dompdf class
        $dompdf = new Dompdf();
        $dompdf->loadHtml('hello world');

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream();
    }

    private function createTable($webform){
        $capacitacoes = [
            ['Unidade', 'Tipo de Capacitação', 'Nome', 'Total de Ações', 'Público', 'Carga Horária', 'Carga Horária Total', 'Nº Participantes', 'Total de Participantes', 'Data Início', 'Data Término', 'Ministrante' ],
        ];

        $submissions = \Drupal::entityTypeManager()
            ->getStorage('webform_submission')
            ->loadByProperties(['webform_id' => $webform->id()]);

        foreach($submissions as $submission){
            if ($submission->isDraft()) continue;

            $data = $submission->getData();
            $unidade = strtoupper($submission->getOwner()->getDisplayName());

            $quantidade_capacitacoes = 0;
            $quantidade_participantes = 0;
            $quantidade_horas = 0;

            foreach($data['formulario_coleta_de_dados'] as $coleta){
                $quantidade_capacitacoes++;
                $quantidade_participantes += (int)$coleta['participantes'];
                $quantidade_horas += (int)$coleta['carga_horaria'];
            }
            
            $primeira_iteracao = TRUE;

            foreach($data['formulario_coleta_de_dados'] as $coleta){
                if($primeira_iteracao) {
                    $publico = ($coleta['publico'] == '1') ? 'Comunidade USP' : 'Outras Instituições';

                    $linha = [$unidade, $coleta['tipo'], $coleta['nome'], $quantidade_capacitacoes, $publico, $coleta['carga_horaria'], $quantidade_horas, $coleta['participantes'], $quantidade_participantes, $coleta['data_inicio'], $coleta['data_termino'], $coleta['ministrante']];

                    $primeira_iteracao = FALSE;
                } else {
                    $linha = ['', $coleta['tipo'], $coleta['nome'], '', $publico, $coleta['carga_horaria'],'', $coleta['participantes'], '', $coleta['data_inicio'], $coleta['data_termino'], $coleta['ministrante']];
                }

                array_push($capacitacoes, $linha);
            }
        }
        
        return $capacitacoes;
    }
}