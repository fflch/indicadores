<?php

namespace Drupal\indicadores\Controller;
use Drupal\Core\Controller\ControllerBase;

class IndexController extends ControllerBase {

    public function index(){
        $str = '
            <ul>
                <li><a href="/indicadores/relatorios">Relatórios Gerais</a></li>
                <li><a href="/indicadores/capacitacao">Relatórios de Capacitações</a></li>
            </ul>';

        return [ '#markup' => $this->t($str)];
    }
}