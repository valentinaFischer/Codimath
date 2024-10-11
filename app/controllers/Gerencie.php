<?php
namespace app\controllers;

use Exception;
use app\classes\Flash;
use app\classes\Validate;
use app\database\models\Aluno;
use app\database\models\Turma;
use app\database\models\Usuario;

class Gerencie extends Base
{
    private $user;
    private $validate;

    public function __construct()
    {
        $this->user = new Usuario;
        $this->validate = new Validate;
        $this->turma = new Turma;
        $this->aluno = new Aluno;
    }

    public function index($request, $response) {
        $message = Flash::get('message');

        $professorUsuarioId = $_SESSION['user_logged_data']['id'];
        $turmas = $this->turma->findByProfessor($professorUsuarioId);
        
       

        return $this->getTwig()->render($response, $this->setView('site/gerencie'), [
            'title' => 'Gerencie as Turmas',
            'turmas' => $turmas,
            'idProfessor' => $professorUsuarioId
        ]);    
    }

     // Método para retornar os alunos de uma turma específica
     public function alunos($request, $response, $args) {
        $turmaId = $args['id'];

        error_log('ID da turma: ' . $turmaId);

        try {
            $alunos = $this->aluno->findAllByTurma($turmaId);

            error_log('Dados dos alunos: ' . print_r($alunos, true));

            if (!is_array($alunos)) {
                error_log('Erro: $alunos não é um array.');
                $response->getBody()->write(json_encode(['error' => 'Dados inválidos']));
                return $response->withHeader('Content-Type', 'application/json')
                                ->withStatus(500);
            }

            if (empty($alunos)) {
                $response->getBody()->write(json_encode([])); // Retorna um JSON vazio com status 204
                return $response->withHeader('Content-Type', 'application/json')
                                ->withStatus(204);
            }

            $response->getBody()->write(json_encode($alunos));
            return $response->withHeader('Content-Type', 'application/json');

        } catch (Exception $e) {
            error_log('Erro: ' . $e->getMessage());
            $response->getBody()->write(json_encode(['error' => 'Erro ao buscar alunos.']));
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(500);
        }
    }

    public function desempenho($request, $response, $args) {
        $alunoId = $args['id'];
    
        try {
            // Buscar dados do aluno
            $dadosJogo = $this->aluno->findDadosJogo($alunoId);
    
            if (!$dadosJogo) {
                $response->getBody()->write(json_encode(['error' => 'Dados não encontrados']));
                return $response->withHeader('Content-Type', 'application/json')
                                ->withStatus(404);
            }
    
            // Calcular métricas
            $totalPontos = array_sum(array_column($dadosJogo, 'pontos'));
            $totalTempo = array_sum(array_column($dadosJogo, 'tempo_total'));
            $totalMacasPares = array_sum(array_column($dadosJogo, 'macas_pares'));
            $totalMacasImpares = array_sum(array_column($dadosJogo, 'macas_impares'));
            $totalColisoes = array_sum(array_column($dadosJogo, 'colisoes'));
            $totalJogos = count($dadosJogo);
    
            $precisao = ($totalMacasPares / ($totalMacasPares + $totalMacasImpares)) * 100;
            $pontosPorSegundo = $totalPontos / $totalTempo;
            $taxaColisoes = $totalColisoes / $totalJogos;
    
            $resultado = [
                'precisao' => number_format($precisao, 2) . '%',
                'pontos_por_segundo' => number_format($pontosPorSegundo, 2),
                'taxa_colisoes' => number_format($taxaColisoes, 2),
            ];
    
            $response->getBody()->write(json_encode($resultado));
            return $response->withHeader('Content-Type', 'application/json');
    
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Erro ao calcular desempenho.']));
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(500);
        }
    }
}