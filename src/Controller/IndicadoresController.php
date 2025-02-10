<?php

// http://0.0.0.0:8000/indicadores/capacitacao/indicadores_abcd

namespace Drupal\indicadores\Controller;
use Drupal\Core\Controller\ControllerBase;

use Drupal\webform\Entity\Webform;
use Drupal\webform\Entity\WebformSubmission;

use Shuchkin\SimpleXLSXGen;

class IndicadoresController extends ControllerBase {
  public function pdf(){
    $build = [
      '#markup' => $this->t('Hello World!'),
    ];
    return $build;
  }

  public function capacitacao($webform_id){
    $webform = Webform::load($webform_id);

    $capacitacoes = [
        ['Unidade', 'Tipo de Capacitação', 'Nome', 'Total de Ações', 'Público', 'Carga Horária', 'Nº Participantes', 'Total de Participantes', 'Data Início', 'Data Término', 'Ministrante' ],
    ];

    if($webform){
      $submissions = \Drupal::entityTypeManager()
        ->getStorage('webform_submission')
        ->loadByProperties(['webform_id' => $webform_id]);
    
      foreach($submissions as $submission){
        if ($submission->isDraft()) continue;

        $data = $submission->getData();
        $unidade = strtoupper($submission->getOwner()->getDisplayName());

        $quantidade_capacitacoes = 0;
        $quantidade_participantes = 0;

        foreach($data['formulario_coleta_de_dados'] as $coleta){
            $quantidade_capacitacoes++;
            $quantidade_participantes += $coleta['participantes'];
        }

        $primeira_iteracao = TRUE;

        foreach($data['formulario_coleta_de_dados'] as $coleta){
          if($primeira_iteracao) {
            $publico = ($coleta['publico'] == '1') ? 'Comunidade USP' : 'Outras Instituições';

            $linha = [$unidade, $coleta['tipo'], $coleta['nome'], $quantidade_capacitacoes, $publico, $coleta['carga_horaria'], $coleta['participantes'], $quantidade_participantes, $coleta['data_inicio'], $coleta['data_termino'], $coleta['ministrante']];

            $primeira_iteracao = FALSE;
          }
          else {
            $linha = ['', $coleta['tipo'], $coleta['nome'], '', $publico, $coleta['carga_horaria'], $coleta['participantes'], '', $coleta['data_inicio'], $coleta['data_termino'], $coleta['ministrante']];
          }

          array_push($capacitacoes, $linha);
        }

        \Drupal::logger('custom_module')->notice(print_r($data, TRUE));
      }
    } else {
      \Drupal::logger('custom_module')->error('Webform not found.');
    }

    $xlsx = SimpleXLSXGen::fromArray( $capacitacoes );

    $xlsx->downloadAs('capacitacoes-'.date('Y-m-d-H-i').'.xlsx');

  }

}