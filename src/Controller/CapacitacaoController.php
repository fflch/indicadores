<?php

namespace Drupal\indicadores\Controller;
use Drupal\Core\Controller\ControllerBase;

use Drupal\webform\Entity\Webform;
use Drupal\webform\Entity\WebformSubmission;

use Shuchkin\SimpleXLSXGen;
use Dompdf\Dompdf;
use Drupal\indicadores\Utils\Util;
use Drupal\indicadores\Relatorios\Capacitacao;

class CapacitacaoController extends ControllerBase {
    public function index(){
        // ADICIONAR O IF QUE FILTRA QUAIS WEBFORMS PEGAR AQUI
        
        $str = Util::createList('indicadores/capacitacao');

        $build = [
            '#markup' => $this->t($str),
            ];
        return $build;
    }

    public function excel($webform_id){
        $webform = Webform::load($webform_id);
        
        $capacitacoes = Capacitacao::prepareData($webform);

        $xlsx = SimpleXLSXGen::fromArray( $capacitacoes );

        $xlsx->downloadAs('capacitações-'.$webform->label().'.xlsx');
    }

    public function pdf($webform_id){
        $webform = Webform::load($webform_id);
        
        $dataArray = Capacitacao::prepareData($webform);
        $html = Capacitacao::createHtmlTable($dataArray);

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream();
    }
}