<?php

namespace Drupal\indicadores\Controller;
use Drupal\Core\Controller\ControllerBase;

class IndexController extends ControllerBase {

    public function index(){
        $str = '
            <ul>
                <li><a href="/indicadores/capacitacao">Capacitações</a></li>
                <li><a href="/indicadores/estrutura_organizacional">Dados Administrativos - Estrutura Organizacional</a></li>
                <li><a href="/indicadores/XXX">Dados Administrativos - Informações Complementares</a></li>
                <li><a href="/indicadores/XXX">Dados Administrativos - Horário de funcionamento das bibliotecas</a></li>
                <li><a href="/indicadores/XXX">Dados Administrativos - Números de assentos para usuários e área física</a></li>
            </ul>';

        return [ '#markup' => $this->t($str)];
    }
}