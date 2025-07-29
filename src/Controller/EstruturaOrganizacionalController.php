<?php

namespace Drupal\indicadores\Controller;
use Drupal\Core\Controller\ControllerBase;

use Drupal\webform\Entity\Webform;
use Drupal\webform\Entity\WebformSubmission;

use Shuchkin\SimpleXLSXGen;
use Dompdf\Dompdf;
use Drupal\indicadores\Utils\Util;
use Drupal\indicadores\Relatorios\EstruturaOrganizacional;

class EstruturaOrganizacionalController extends ControllerBase {
    public function index(){
        // ADICIONAR O IF QUE FILTRA QUAIS WEBFORMS PEGAR AQUI

        $str = Util::createList('indicadores/estrutura_organizacional');

        $build = [
            '#markup' => $this->t($str),
            ];
        return $build;
    }

    public function excel($webform_id){
        $webform = Webform::load($webform_id);

        $xlsx = SimpleXLSXGen::fromArray(EstruturaOrganizacional::prepareData($webform));

        $xlsx->downloadAs('estrutura_organizacional-'.$webform->label().'.xlsx');
    }

    public function pdf($webform_id){
        $webform = Webform::load($webform_id);
        
        $dataArray = EstruturaOrganizacional::prepareData($webform);
        $html = EstruturaOrganizacional::createHtmlTable($dataArray);

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream();
    }
}