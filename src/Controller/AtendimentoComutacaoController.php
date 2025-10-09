<?php

namespace Drupal\indicadores\Controller;
use Drupal\Core\Controller\ControllerBase;

use Drupal\webform\Entity\Webform;
use Drupal\webform\Entity\WebformSubmission;

use Shuchkin\SimpleXLSXGen;
use Dompdf\Dompdf;
use Drupal\indicadores\Utils\Util;
use Drupal\indicadores\Relatorios\AtendimentoComutacao;

class AtendimentoComutacaoController extends ControllerBase {
    public function index(){
        $str = Util::createList('indicadores/atendimento_comutacao');

        $build = [
            '#markup' => $this->t($str),
            ];
        return $build;
    }

    public function excel($webform_id){
        $webform = Webform::load($webform_id);

        $xlsx = SimpleXLSXGen::fromArray(AtendimentoComutacao::prepareData($webform));

        $xlsx->downloadAs('atendimento-comutacao-'.$webform->label().'.xlsx');
    }

    public function pdf($webform_id){
        $webform = Webform::load($webform_id);
        
        $dataArray = AtendimentoComutacao::prepareData($webform);
        $html = AtendimentoComutacao::createHtmlTable($dataArray);

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream();
    }
}