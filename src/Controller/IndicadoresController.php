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
    
    $unidade = $submission->getOwner()->getDisplayName();

    $linhas = '';
    foreach($data['formulario_coleta_de_dados'] as $linha){
      $linhas .= "    
      <tr>
        <td>{$unidade}</td>
        <td>{$linha['tipo']}</td>
        <td>{$linha['nome']}</td>
        <td>{$linha['publico']}</td>
        <td>{$linha['carga_horaria']}</td>
        <td>{$linha['participantes']}</td>
        <td>{$linha['data_inicio']}</td>
        <td>{$linha['data_termino']}</td>
        <td>{$linha['ministrante']}</td>
      </tr>";
    }

    $table = "
    <table>
      <thead>
        <tr>
          <th>Unidade</th>
          <th>Tipo de Capacitação</th>
          <th>Nome</th>
          <th>Público</th>
          <th>Carga Horária</th>
          <th>Nº de Participantes/Alunos Matriculados</th>
          <th>Data de Início</th>
          <th>Data de Término</th>
          <th>Ministrante</th>
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