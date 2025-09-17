<?php

namespace Drupal\indicadores\Controller;
use Drupal\Core\Controller\ControllerBase;

class IndexController extends ControllerBase {

    public function index(){
        $str = '
            <ul>
                <li><a href="/indicadores/capacitacao">Relatórios de Capacitações</a></li>  
                <li><a href="/indicadores/XXXXX">Acervo das Bibliotecas da USP</a></li>
                <li><a href="/indicadores/acervo_fasciculos_backlog">Acervo de Fascículos de Periódicos e Backlog de Catalogação</a></li>
                <li><a href="/indicadores/acessibilidade_geral">Acessibilidade Nas Bibliotecas da USP - Geral e Física</a></li>
                <li><a href="/indicadores/acessibilidade_arquitetonica">Acessibilidade Nas Bibliotecas da USP - Arquitetônica e Física</a></li>
                <li><a href="/indicadores/acessibilidade_tecnologica">Acessibilidade Nas Bibliotecas da USP - Conteúdo e Tecnologia</a></li>
                <li><a href="/indicadores/aquisicao">Aquisição de Livros e de Outros Tipos de Materiais e Títulos Correntes e Periódicos</a></li>
                <li><a href="/indicadores/assistencia_normalizacao">Assistência ao Usuário e Normalização Técnica</a></li>
                <li><a href="/indicadores/atendimento_comutacao">Atendimento de Comutação Bibliográfica pelas Bibliotecas da USP</a></li>
                <li><a href="/indicadores/atendimento_comutacao_solicitante">Atendimento de Comutação Bibliográfica pelas Bibliotecas da USP - Biblioteca Solicitante</a></li>
                <li><a href="/indicadores/XXXXX">Bases de Dados mantidas pelas Bibliotecas da USP</a></li>
                <li><a href="/indicadores/XXXXX">Capacitação de Usuários pelas Bibliotecas da USP - Completa</a></li>
                <li><a href="/indicadores/XXXXX">Capacitação de Usuários pelas Bibliotecas da USP - Nº de Participantes por Tipo de Capacitação</a></li>
                <li><a href="/indicadores/XXXXX">Capacitação de Usuários pelas Bibliotecas da USP - Resumo</a></li>
                <li><a href="/indicadores/XXXXX">Circulação do Acervo das Bibliotecas da USP</a></li>
                <li><a href="/indicadores/XXXXX">Consultas ao Acervo e Empréstimo entre Bibliotecas como Biblioteca Solicitante</a></li>
                <li><a href="/indicadores/estrutura_organizacional">Dados Administrativos - Estrutura Organizacional</a></li>
                <li><a href="/indicadores/XXXXX">Dados Administrativos - Horário de Funcionamento das Bibliotecas</a></li>
                <li><a href="/indicadores/XXXXX">Dados Administrativos - Informações Complementares</a></li>
                <li><a href="/indicadores/XXXXX">Dados Administrativos - Número de Assentos para Usuários e Área Física</a></li>
                <li><a href="/indicadores/XXXXX">Dados Administrativos - Recursos Humanos das Bibliotecas da USP</a></li>
                <li><a href="/indicadores/XXXXX">Dissertações e Teses Defendidas na USP e Cadastradas no DEDALUS</a></li>
                <li><a href="/indicadores/XXXXX">Equipamentos de Informática nas Bibliotecas da USP</a></li>
                <li><a href="/indicadores/XXXXX">Eventos Promovidos pelas Bibliotecas e com Participação das Bibliotecas</a></li>
                <li><a href="/indicadores/XXXXX">Frequência às Bibliotecas da USP, distribuída por Unidade</a></li>
                <li><a href="/indicadores/XXXXX">Participação das Equipes das Bibliotecas em Ações de Capacitação</a></li>
                <li><a href="/indicadores/XXXXX">Projetos das Bibliotecas USP</a></li>
                <li><a href="/indicadores/XXXXX">Publicações Editadas pelas Bibliotecas</a></li>
                <li><a href="/indicadores/XXXXX">Publicações Oficiais Editadas pelas Unidades com a Participação das Bibliotecas</a></li>
                <li><a href="/indicadores/internet_redes">Serviços pela Internet e Rede sem Fio nas Bibliotecas da USP</a></li>
            </ul>';

        return [ '#markup' => $this->t($str)];
    }
}