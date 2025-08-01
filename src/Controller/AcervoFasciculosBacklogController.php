<?php

namespace Drupal\indicadores\Controller;
use Drupal\Core\Controller\ControllerBase;

use Drupal\webform\Entity\Webform;
use Drupal\webform\Entity\WebformSubmission;

use Shuchkin\SimpleXLSXGen;
use Dompdf\Dompdf;
use Drupal\indicadores\Utils\Util;
use Drupal\indicadores\Relatorios\AcervoFasciculosBacklog;

class AcervoFasciculosBacklogController extends ControllerBase {
    public function index(){
        $str = Util::createList('indicadores/acervo_fasciculos_backlog');

        $build = [
            '#markup' => $this->t($str),
            ];
        return $build;
    }

    public function excel($webform_id){
        $webform = Webform::load($webform_id);

        $xlsx = SimpleXLSXGen::fromArray(AcervoFasciculosBacklog::prepareData($webform));

        $xlsx->downloadAs('acervo_fasciculos_backlog-'.$webform->label().'.xlsx');
    }

    public function pdf($webform_id){
        $webform = Webform::load($webform_id);
        
        $dataArray = AcervoFasciculosBacklog::prepareData($webform);
        $html = AcervoFasciculosBacklog::createHtmlTable($dataArray);

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream();
    }
}