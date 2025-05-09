<?php

namespace Drupal\indicadores\Controller;
use Drupal\Core\Controller\ControllerBase;

use Drupal\webform\Entity\Webform;
use Drupal\webform\Entity\WebformSubmission;

use Shuchkin\SimpleXLSXGen;
use Dompdf\Dompdf;
use Drupal\indicadores\Utils\Util;

class RelatorioController extends ControllerBase {
    public function form(){
        $str = Util::criaLista('indicadores/relatorios');

        $build = [
            '#markup' => $this->t($str),
            ];
        return $build;
    }

    public function pdf(){
        // instantiate and use the dompdf class
        $dompdf = new Dompdf();
        $dompdf->loadHtml('hello world');

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream();
    }

    public function excel($webform_id){
        $webform = Webform::load($webform_id);

        $xlsx = SimpleXLSXGen::fromArray( [1, 2, 3] );

        $xlsx->downloadAs($webform->label().'.xlsx');
    }

    private function createTable(){
        $unidade = strtoupper($submission->getOwner()->getDisplayName());

    }
}