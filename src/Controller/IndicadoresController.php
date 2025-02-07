<?php

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
      ['Unidade', 'Tipo de Capacitação', 'Nome', 'Total de Ações' ],
    ];

    if ($webform) {
      // Load all submissions for the webform.
      $submissions = \Drupal::entityTypeManager()
        ->getStorage('webform_submission')
        ->loadByProperties(['webform_id' => $webform_id]);
    
      foreach ($submissions as $submission) {
        if ($submission->isDraft()) continue;

        $data = $submission->getData();
        $unidade = $submission->getOwner()->getDisplayName();
        $quantidade = 0;
        $quantidade_participantes = 0;
        $primeira_iteracao = TRUE;

        //FAZER UM FOREACH PARA CALCULAR TODOS OS DADOS AQUI, PRIMEIRO

        foreach ($data['formulario_coleta_de_dados'] as $coleta) {
          if($primeira_iteracao) {
            $linha = [$unidade, $coleta['tipo'], $coleta['nome'], ''];
            $primeira_iteracao = FALSE;
          }
          else {
            $linha = [ '', $coleta['tipo'], $coleta['nome']];
          }
          array_push($capacitacoes, $linha);
          $quantidade++;
          $quantidade_participantes += $coleta['participantes'];
        }

        \Drupal::logger('custom_module')->notice(print_r($data, TRUE));
      }
    } else {
      \Drupal::logger('custom_module')->error('Webform not found.');
    }

    $xlsx = SimpleXLSXGen::fromArray( $capacitacoes )
                  ->mergeCells('A20:B20'); // TENTAR FAZER ISSO FUNCIONAR PARA A UNIDADE 
    $xlsx->downloadAs('capacitacoes-'.date('Y-m-d-H-i').'.xlsx');

  }

}