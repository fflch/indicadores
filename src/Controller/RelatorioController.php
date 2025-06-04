<?php

namespace Drupal\indicadores\Controller;
use Drupal\Core\Controller\ControllerBase;

use Drupal\webform\Entity\Webform;
use Drupal\webform\Entity\WebformSubmission;

use Shuchkin\SimpleXLSXGen;
use Dompdf\Dompdf;
use Drupal\indicadores\Utils\Util;

class RelatorioController extends ControllerBase {
    public function index(){
        $str = Util::createList('indicadores/relatorios');

        $build = [
            '#markup' => $this->t($str),
            ];
        return $build;
    }

    public function excel($webform_id){
        $webform = Webform::load($webform_id);

        $xlsx = SimpleXLSXGen::fromArray( [1, 2, 3] );

        $xlsx->downloadAs($webform->label().'.xlsx');
    }

    public function pdf(){
        // instantiate and use the dompdf class
        $dompdf = new Dompdf();
        $dompdf->loadHtml('Em construção');

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream();
    }

    private function createTable(){
        $unidade = strtoupper($submission->getOwner()->getDisplayName());

    }
}