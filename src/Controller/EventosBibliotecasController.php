<?php

namespace Drupal\indicadores\Controller;
use Drupal\Core\Controller\ControllerBase;

use Drupal\webform\Entity\Webform;
use Drupal\webform\Entity\WebformSubmission;

use Shuchkin\SimpleXLSXGen;
use Dompdf\Dompdf;
use Drupal\indicadores\Utils\Util;
use Drupal\indicadores\Relatorios\EventosBibliotecas;

class EventosBibliotecasController extends ControllerBase {
    public function index(){
        $webforms = Webform::loadMultiple();
        $agencia_forms = [];

        foreach ($webforms as $webform) {
            if (str_starts_with($webform->id(), 'indicadores_abcd')) {
                $agencia_forms[] = $webform;
            }
        }

        $str = Util::createList('indicadores/eventos_bibliotecas');

        $build = [
            '#markup' => $this->t($str),
            ];
        return $build;
    }

    public function excel($webform_id){
        $webform = Webform::load($webform_id);
        
        $capacitacoes = EventosBibliotecas::prepareData($webform);

        $xlsx = SimpleXLSXGen::fromArray( $capacitacoes );

        $xlsx->downloadAs('capacitações-'.$webform->label().'.xlsx');
    }

    public function pdf($webform_id){
        $webform = Webform::load($webform_id);
        
        $dataArray = EventosBibliotecas::prepareData($webform);
        $html = EventosBibliotecas::createHtmlTable($dataArray);

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream();
    }
}