<?php

namespace Drupal\indicadores\Controller;
use Drupal\Core\Controller\ControllerBase;

use Drupal\webform\Entity\Webform;
use Drupal\webform\Entity\WebformSubmission;

use Shuchkin\SimpleXLSXGen;
use Dompdf\Dompdf;
use Drupal\indicadores\Utils\Util;
use Drupal\indicadores\Relatorios\CapacitacaoParticipantes;

class CapacitacaoParticipantesController extends ControllerBase {
    public function index(){
        $webforms = Webform::loadMultiple();
        $agencia_forms = [];

        foreach ($webforms as $webform) {
            if (str_starts_with($webform->id(), 'indicadores_abcd')) {
                $agencia_forms[] = $webform;
            }
        }

        $str = Util::createList('indicadores/capacitacao_participantes');

        $build = [
            '#markup' => $this->t($str),
            ];
        return $build;
    }

    public function excel($webform_id){
        $webform = Webform::load($webform_id);
        
        $capacitacoes = CapacitacaoParticipantes::prepareData($webform);

        $xlsx = SimpleXLSXGen::fromArray( $capacitacoes );

        $xlsx->downloadAs('capacitações-'.$webform->label().'.xlsx');
    }

    public function pdf($webform_id){
        $webform = Webform::load($webform_id);
        
        $dataArray = CapacitacaoParticipantes::prepareData($webform);
        $html = CapacitacaoParticipantes::createHtmlTable($dataArray);

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream();
    }
}