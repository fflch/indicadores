<?php

namespace Drupal\indicadores\Controller;
use Drupal\Core\Controller\ControllerBase;

use Drupal\webform\Entity\WebformSubmission;

class IndicadoresController extends ControllerBase {
  public function pdf(){
    $build = [
      '#markup' => $this->t('Hello World!'),
    ];
    return $build;
  }

  public function capacitacao($submission_id){

    $submission = \Drupal\webform\Entity\WebformSubmission::load($submission_id);
    $data = $submission->getData();

    $linhas = '';
    foreach($data['formulario_coleta_de_dados'] as $linha){
      $linhas .= "    
      <tr>
        <td>{$linha['tipo']}</td>
        <td>{$linha['nome']}</td>
        <td></td>
        <td></td>
        <td></td>
      </tr>";
    }

    $table = "
    <table>
      <thead>
        <tr>
          <th>Tipo</th>
          <th>Nome</th>
          <th></th>
          <th></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        {$linhas}
      </tbody>
      </table>
    ";
    $build = [
      '#markup' => $this->t($table),
    ];
    return $build;
  }

}