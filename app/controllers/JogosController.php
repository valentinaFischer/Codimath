<?php 
namespace app\controllers;

use app\classes\Flash;
use app\database\models\Jogo;
use app\database\models\Aluno;
use app\database\models\Turma;
use app\database\models\Professor;
use app\database\models\PontoCobrinha;
use app\database\models\PontoTiroAoAlvo;
use app\database\models\PontoSalveATerra;
use app\database\models\PontosPegaNumero;

class JogosController extends Base {
    private $user;
    private $validate;

    public function __construct()
    {
        $this->turma = new Turma;
        $this->aluno = new Aluno;
        $this->jogo = new Jogo;
        $this->professor = new Professor;
        $this->pontoCobrinha = new PontoCobrinha;
        $this->pontoSalveATerra = new PontoSalveATerra;
        $this->pontoPegaNumero = new PontosPegaNumero;
        $this->pontoTiro = new PontoTiroAoAlvo;
    }

    public function index($request, $response, $args) {
        $messages = Flash::getAll();

        $idJogo = $args['idJogo'];
        $idAluno = $args['idAluno'];

        $jogo = $this->jogo->findBy('id', $idJogo);
        $aluno = $this->aluno->findBy('id', $idAluno);

        $turmaId = $aluno->turma_id;
        $turma = $this->turma->findBy('id', $turmaId);

        $professorId = $turma->professor_usuario_id;
        $professor = $this->professor->findBy('usuario_id', $professorId);

        return $this->getTwig()->render($response, $this->setView('site/jogo'), [
            'title' => 'Botar nome do jogo',
            'messages' => $messages,
            'aluno' => $aluno,
            'jogo' => $jogo,
            'professor' => $professor,
            'turma' => $turma
        ]);    
    }

    public function salvarPontuacao($request, $response, $args) {

        $pontos = filter_input(INPUT_POST, 'pontuacaoFinal', FILTER_SANITIZE_NUMBER_INT);
        $tempo = filter_input(INPUT_POST, 'tempoTotal', FILTER_SANITIZE_NUMBER_INT);

        $idAluno = $args['aluno_id'];
        $idProfessor = $args['professor_usuario_id'];
        $idJogo = $args['jogo_id'];

        if ($idJogo == 1) {
            $macasPares = filter_input(INPUT_POST, 'macasPares', FILTER_SANITIZE_NUMBER_INT);
            $macasImpares = filter_input(INPUT_POST, 'macasImpares', FILTER_SANITIZE_NUMBER_INT);
            $colisoes = filter_input(INPUT_POST, 'colisoes', FILTER_SANITIZE_NUMBER_INT);
        }

        $aluno = $this->aluno->findBy('id', $idAluno);
        $professor = $this->professor->findBy('usuario_id', $idProfessor);
        $jogo = $this->jogo->findBy('id', $idJogo);

        if ($idJogo == 1) {
            $pontoSalvo = $this->pontoCobrinha->create([
                'id_aluno' => $idAluno,
                'id_jogo' => $idJogo,
                'pontos' => $pontos,
                'tempo_total' => $tempo,
                'macas_pares' => $macasPares,
                'macas_impares' => $macasImpares,
                'colisoes' => $colisoes
            ]);

        } elseif ($idJogo == 4) {
            $vidasPerdidasErradas = filter_input(INPUT_POST, 'vidas_perdidas_erradas', FILTER_SANITIZE_NUMBER_INT);
            $vidasPerdidasCanvas = filter_input(INPUT_POST, 'vidas_perdidas_canvas', FILTER_SANITIZE_NUMBER_INT);

            $pontoSalvo = $this->pontoSalveATerra->create([
                'id_aluno' => $idAluno,
                'id_jogo' => $idJogo,
                'pontos' => $pontos,
                'tempo_total' => $tempo,
                'vidas_perdidas_erradas' => $vidasPerdidasErradas,
                'vidas_perdidas_canvas' => $vidasPerdidasCanvas
            ]);
        } elseif ($idJogo == 5) {
            $pontoSalvo = $this->pontoPegaNumero->create([
                'id_aluno' => $idAluno,
                'id_jogo' => $idJogo,
                'pontos' => $pontos,
                'tempo_total' => $tempo
            ]);
        } elseif ($idJogo == 6) {
            $pontoSalvo = $this->pontoTiro->create([
                'id_aluno' => $idAluno,
                'id_jogo' => $idJogo,
                'pontos' => $pontos,
                'tempo_total' => $tempo
            ]);
        }

        if ($pontoSalvo) {
            Flash::set('message', 'Pontos salvos!');
        } else {
            Flash::set('message', 'Ocorreu um erro');
        }

        $messages = Flash::getAll();
        return $this->getTwig()->render($response, $this->setView('site/teste'), [
            'title' => 'Salvando pontuação',
            'messages' => $messages,
            'aluno' => $aluno,
            'jogo' => $jogo,
            'professor' => $professor
        ]);  
    }
}