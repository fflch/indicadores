<?php

namespace Drupal\indicadores\Controller;
use Drupal\Core\Controller\ControllerBase;

class IndicadoresController extends ControllerBase {
  public function pdf(){
    $build = [
      '#markup' => $this->t('Hello World!'),
    ];
    return $build;
  }
}