<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Advogados_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // Pesquisar advogados com filtros
    public function pesquisar($filtros = []) {
        $this->db->select('numero_registo, nome_completo, regiao, localidade, telefone, email');
        $this->db->from('advogados');
        $this->db->where('status', 'ativo');

        if (!empty($filtros['nome'])) {
            $this->db->like('nome_completo', $filtros['nome']);
        }

        if (!empty($filtros['registo'])) {
            $this->db->like('numero_registo', $filtros['registo']);
        }

        if (!empty($filtros['regiao'])) {
            $this->db->where('regiao', $filtros['regiao']);
        }

        if (!empty($filtros['localidade'])) {
            $this->db->like('localidade', $filtros['localidade']);
        }

        if (!empty($filtros['morada'])) {
            $this->db->like('morada', $filtros['morada']);
        }

        $this->db->order_by('nome_completo', 'ASC');
        $this->db->limit(50); // Limitar resultados para performance

        return $this->db->get()->result();
    }

    // Obter advogados por letra inicial
    public function get_por_letra($letra, $limit = 50, $offset = 0) {
        return $this->db
            ->select('numero_registo, nome_completo, regiao, localidade, telefone, email, data_inscricao')
            ->where('status', 'ativo')
            ->like('nome_completo', $letra, 'after')
            ->order_by('nome_completo', 'ASC')
            ->limit($limit, $offset)
            ->get('advogados')
            ->result();
    }

    // Contar advogados por letra
    public function count_por_letra() {
        $result = [];
        $alfabeto = range('A', 'Z');

        foreach ($alfabeto as $letra) {
            $count = $this->db
                ->where('status', 'ativo')
                ->like('nome_completo', $letra