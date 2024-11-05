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
            'idProfessor' => $professorUsuarioId,
            'userId' => $professorUsuarioId
        ]);    
    }

     // Método para retornar os alunos de uma turma específica
     public function alunos($request, $response, $args) {
        $turmaId = $args['id'];


        try {
            $alunos = $this->aluno->findAllByTurma($turmaId);

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
            $dadosJogoCobrinha = $this->aluno->findDadosJogo($alunoId, 'ponto_cobrinha');
    
            if ($dadosJogoCobrinha) {
                 // Calcular métricas Jogo da Cobrinha
                $totalPontos = array_sum(array_column($dadosJogoCobrinha, 'pontos'));
                $totalTempo = array_sum(array_column($dadosJogoCobrinha, 'tempo_total'));
                $totalMacasPares = array_sum(array_column($dadosJogoCobrinha, 'macas_pares'));
                $totalMacasImpares = array_sum(array_column($dadosJogoCobrinha, 'macas_impares'));
                $totalColisoes = array_sum(array_column($dadosJogoCobrinha, 'colisoes'));
                $totalJogos = count($dadosJogoCobrinha);

                // Métricas de desempenho
                $precisao = $totalMacasPares + $totalMacasImpares > 0 ? ($totalMacasPares / ($totalMacasPares + $totalMacasImpares)) * 100 : 0;
                $pontosPorSegundo = $totalTempo > 0 ? $totalPontos / $totalTempo : 0;
                $taxaColisoes = $totalJogos > 0 ? $totalColisoes / $totalJogos : 0;
                $mediaPontosPorJogo = $totalJogos > 0 ? $totalPontos / $totalJogos : 0;

                // Avaliação de desempenho
                $mensagemDesempenho = "";
                
                if ($precisao >= 90) {
                    $mensagemDesempenho .= "Excelente na diferenciação de números pares e ímpares. ";
                } elseif ($precisao >= 70) {
                    $mensagemDesempenho .= "Muito bom na diferenciação de números pares e ímpares. ";
                } elseif ($precisao >= 50) {
                    $mensagemDesempenho .= "Bom na diferenciação de números, mas pode melhorar. ";
                } else {
                    $mensagemDesempenho .= "Dificuldade em diferenciar números pares de ímpares. ";
                }                

                // Avaliação do controle (taxa de colisões)
                if ($taxaColisoes < 0.05) {
                    $mensagemDesempenho .= "Excelente controle da cobrinha! ";
                } elseif ($taxaColisoes < 0.1) {
                    $mensagemDesempenho .= "Muito bom controle da cobrinha. ";
                } elseif ($taxaColisoes < 0.2) {
                    $mensagemDesempenho .= "Bom controle da cobrinha, mas pode melhorar. ";
                } else {
                    $mensagemDesempenho .= "Precisa melhorar o controle da cobrinha. ";
                }                

                $mensagemDesempenho .= " ";

                // Avaliação da agilidade (pontos por segundo)
                if ($pontosPorSegundo >= 0.15) {
                    $mensagemDesempenho .= "Excelente agilidade! ";
                } elseif ($pontosPorSegundo >= 0.1) {
                    $mensagemDesempenho .= "Muito boa agilidade. ";
                } elseif ($pontosPorSegundo >= 0.05) {
                    $mensagemDesempenho .= "Boa agilidade. ";
                } else {
                    $mensagemDesempenho .= "Precisa melhorar a agilidade. ";
                }                              
                
                $resultadoCobrinha = [
                    'mensagemDesempenho' => $mensagemDesempenho,
                    'precisao' => $precisao,
                    'taxaColisoes' => $taxaColisoes,
                    'pontosPorSegundo' => $pontosPorSegundo,
                    'mediaPontosPorJogo' => $mediaPontosPorJogo,
                ];
                
            } else {
                $resultadoCobrinha = [
                    'mensagemDesempenho' => "Aluno não jogou esse jogo ainda.",
                    'precisao' => 0,
                    'taxaColisoes' => 0,
                    'pontosPorSegundo' => 0,
                    'mediaPontosPorJogo' => 0,
                ];
            }

            //ADIVINHE O NÚMERO
            // Buscar dados do aluno para o jogo "Adivinhe o Número"
            $dadosJogoAdivinhe = $this->aluno->findDadosJogo($alunoId, 'pontos_adivinhe');

            if ($dadosJogoAdivinhe) {
                // Calcular métricas para o Jogo "Adivinhe o Número"
                $totalPontos = array_sum(array_column($dadosJogoAdivinhe, 'pontos'));
                $totalTempo = array_sum(array_column($dadosJogoAdivinhe, 'tempo_total'));
                $totalJogos = count($dadosJogoAdivinhe);
                $totalTentativas = $totalJogos + $totalPontos; 

                // Métricas de desempenho
                $precisao = $totalTentativas > 0 ? ($totalPontos / $totalTentativas) * 100 : 0; // Precisão em %
                $pontosPorSegundo = $totalTempo > 0 ? $totalPontos / $totalTempo : 0;

                // Média de pontos por jogo
                $mediaPontosPorJogo = $totalJogos > 0 ? $totalPontos / $totalJogos : 0;

                // Avaliação de desempenho
                $mensagemDesempenho = "";

                // Avaliação da precisão
                if ($precisao >= 90) {
                    $mensagemDesempenho .= "Excelente precisão nas respostas! ";
                } elseif ($precisao >= 70) {
                    $mensagemDesempenho .= "Muito boa precisão nas respostas. ";
                } elseif ($precisao >= 50) {
                    $mensagemDesempenho .= "Bom desempenho, mas pode melhorar a precisão. ";
                } else {
                    $mensagemDesempenho .= "Dificuldade em escolher as respostas corretas. ";
                }

                // Avaliação da agilidade (pontos por segundo)
                if ($pontosPorSegundo >= 0.15) {
                    $mensagemDesempenho .= "Excelente agilidade! ";
                } elseif ($pontosPorSegundo >= 0.1) {
                    $mensagemDesempenho .= "Muito boa agilidade. ";
                } elseif ($pontosPorSegundo >= 0.05) {
                    $mensagemDesempenho .= "Boa agilidade. ";
                } else {
                    $mensagemDesempenho .= "Precisa melhorar a agilidade. ";
                }

                // Resultados para o Jogo "Adivinhe o Número"
                $resultadoAdivinhe = [
                    'mensagemDesempenhoAdivinhe' => $mensagemDesempenho,
                    'precisaoAdivinhe' => $precisao,
                    'pontosPorSegundoAdivinhe' => $pontosPorSegundo,
                    'mediaPontosPorJogoAdivinhe' => $mediaPontosPorJogo,
                ];

            } else {
                $resultadoAdivinhe = [
                    'mensagemDesempenhoAdivinhe' => "Aluno não jogou esse jogo ainda.",
                    'precisaoAdivinhe' => 0,
                    'pontosPorSegundoAdivinhe' => 0,
                    'mediaPontosPorJogoAdivinhe' => 0,
                ];
            }

            //JOGO DA MEMÓRIA
            $dadosJogoMemoria = $this->aluno->findDadosJogo($alunoId, 'pontos_memoria');

            if ($dadosJogoMemoria) {
                $totalTempo = array_sum(array_column($dadosJogoMemoria, 'tempo_total'));
                $totalJogos = count($dadosJogoMemoria);
                $tempoMedioPorJogo = $totalTempo / $totalJogos;
    
                // Avaliação de desempenho
                $mensagemDesempenho = "";
    
                if ($tempoMedioPorJogo < 30) {
                    $mensagemDesempenho = "Excelente!";
                } elseif ($tempoMedioPorJogo < 60) {
                    $mensagemDesempenho = "Bom!";
                } elseif ($tempoMedioPorJogo < 90) {
                    $mensagemDesempenho = "Médio.";
                } else {
                    $mensagemDesempenho = "Abaixo do esperado.";
                }
    
                $resultadoMemoria = [
                    'tempoMedioPorJogo' => number_format($tempoMedioPorJogo, 2) . ' segundos',
                    'mensagemDesempenho' => $mensagemDesempenho,
                ];
            } else {
                $resultadoMemoria = [
                    'mensagemDesempenho' => "Aluno não jogou esse jogo ainda.",
                    'tempoMedioPorJogo' => 0
                ];
            }

            //TIRO AO ALVO

            $dadosJogoTiro = $this->aluno->findDadosJogo($alunoId, 'pontos_tiroaoalvo');

            if ($dadosJogoTiro) {
                // Calcular métricas para o Jogo "Adivinhe o Número"
                $totalPontos = array_sum(array_column($dadosJogoTiro, 'pontos'));
                $totalTempo = array_sum(array_column($dadosJogoTiro, 'tempo_total'));
                $totalJogos = count($dadosJogoTiro);
                $totalTentativas = $totalJogos + $totalPontos; 

                // Métricas de desempenho
                $precisao = $totalTentativas > 0 ? ($totalPontos / $totalTentativas) * 100 : 0; // Precisão em %
                $pontosPorSegundo = $totalTempo > 0 ? $totalPontos / $totalTempo : 0;

                // Média de pontos por jogo
                $mediaPontosPorJogo = $totalJogos > 0 ? $totalPontos / $totalJogos : 0;

                // Avaliação de desempenho
                $mensagemDesempenho = "";

                // Avaliação da precisão
                if ($precisao >= 90) {
                    $mensagemDesempenho .= "Excelente precisão nas respostas! ";
                } elseif ($precisao >= 70) {
                    $mensagemDesempenho .= "Muito boa precisão nas respostas. ";
                } elseif ($precisao >= 50) {
                    $mensagemDesempenho .= "Bom desempenho, mas pode melhorar a precisão. ";
                } else {
                    $mensagemDesempenho .= "Dificuldade em escolher as respostas corretas. ";
                }

                // Avaliação da agilidade (pontos por segundo)
                if ($pontosPorSegundo >= 0.15) {
                    $mensagemDesempenho .= "Excelente agilidade! ";
                } elseif ($pontosPorSegundo >= 0.1) {
                    $mensagemDesempenho .= "Muito boa agilidade. ";
                } elseif ($pontosPorSegundo >= 0.05) {
                    $mensagemDesempenho .= "Boa agilidade. ";
                } else {
                    $mensagemDesempenho .= "Precisa melhorar a agilidade. ";
                }

                // Resultados para o Jogo "Tiro ao Alvo"
                $resultadoTiro = [
                    'mensagemDesempenhoTiro' => $mensagemDesempenho,
                    'precisaoTiro' => $precisao,
                    'pontosPorSegundoTiro' => $pontosPorSegundo,
                    'mediaPontosPorJogoTiro' => $mediaPontosPorJogo,
                ];

            } else {
                $resultadoTiro = [
                    'mensagemDesempenhoTiro' => "Aluno não jogou esse jogo ainda.",
                    'precisaoTiro' => 0,
                    'pontosPorSegundoTiro' => 0,
                    'mediaPontosPorJogoTiro' => 0,
                ];
            }

            //PEGA-NÚMERO
            $dadosJogoPega = $this->aluno->findDadosJogo($alunoId, 'pontos_peganumero');

            if ($dadosJogoPega) {
                // Calcular métricas para o Jogo 
                $totalPontos = array_sum(array_column($dadosJogoPega, 'pontos'));
                $totalTempo = array_sum(array_column($dadosJogoPega, 'tempo_total'));
                $totalJogos = count($dadosJogoTiro);
                $totalTentativas = $totalJogos + $totalPontos; 

                // Métricas de desempenho
                $precisao = $totalTentativas > 0 ? ($totalPontos / $totalTentativas) * 100 : 0; // Precisão em %

                // Média de pontos por jogo
                $mediaPontosPorJogo = $totalJogos > 0 ? $totalPontos / $totalJogos : 0;

                // Avaliação de desempenho
                $mensagemDesempenho = "";

                // Avaliação da precisão
                if ($precisao >= 90) {
                    $mensagemDesempenho .= "Excelente em diferenciar números de letras! ";
                } elseif ($precisao >= 70) {
                    $mensagemDesempenho .= "Muito bom em diferenciar números de letras. ";
                } elseif ($precisao >= 50) {
                    $mensagemDesempenho .= "Bom desempenho, mas pode melhorar a precisão. ";
                } else {
                    $mensagemDesempenho .= "Dificuldade em diferenciar números de letras. ";
                }

                // Resultados para o Jogo "Tiro ao Alvo"
                $resultadoPega = [
                    'mensagemDesempenho' => $mensagemDesempenho,
                    'precisao' => $precisao,
                    'mediaPontosPorJogo' => $mediaPontosPorJogo,
                ];

            } else {
                $resultadoPega = [
                    'mensagemDesempenho' => "Aluno não jogou esse jogo ainda.",
                    'precisao' => 0,
                    'mediaPontosPorJogo' => 0,
                ];
            }

            //SALVE A TERRA
            $dadosSalveATerra = $this->aluno->findDadosJogo($alunoId, 'pontos_salveaterra');

            if ($dadosSalveATerra) {
                // Calculando o total de pontos, vidas perdidas e tempo total
                $totalPontos = array_sum(array_column($dadosSalveATerra, 'pontos'));
                $totalTempo = array_sum(array_column($dadosSalveATerra, 'tempo_total'));
                $vidasPerdidasErradas = array_sum(array_column($dadosSalveATerra, 'vidas_perdidas_erradas'));
                $vidasPerdidasCanvas = array_sum(array_column($dadosSalveATerra, 'vidas_perdidas_canvas'));
    
                // Calcular a precisão (acertos / tentativas)
                $precisao = $totalPontos / ($totalPontos + 5) * 100;
    
                // Calcular os pontos por segundo
                $pontosPorSegundo = $totalTempo > 0 ? ($totalPontos / $totalTempo) : 0;
    
                // Criando a mensagem de desempenho
                $mensagemDesempenho = "";
    
                if ($precisao >= 90) {
                    $mensagemDesempenho = "Excelente em cálculos de adição e subtração!";
                } elseif ($precisao >= 70) {
                    $mensagemDesempenho = "Bom em cálculos de adição e subtração!";
                } elseif ($precisao >= 50) {
                    $mensagemDesempenho = "Médio em em cálculos de adição e subtração.";
                } else {
                    $mensagemDesempenho = "Abaixo do esperado em cálculos de adição e subtração.";
                }

                // Avaliar agilidade com base em pontos por segundo e vidas perdidas
                if ($pontosPorSegundo > 0.1 && $vidasPerdidasCanvas < 2) {
                    $mensagemDesempenho .= " Ótima agilidade!";
                } elseif ($pontosPorSegundo > 0.05 && $vidasPerdidasCanvas < 3) {
                    $mensagemDesempenho .= " Boa agilidade!";
                } else {
                    $mensagemDesempenho .= " Agilidade a melhorar.";
                }
                    
                // Resultado
                $resultadoSalve = [
                    'precisao' => number_format($precisao, 2) . '%',
                    'pontosPorSegundo' => number_format($pontosPorSegundo, 2) . ' pontos/segundo',
                    'mensagemDesempenho' => $mensagemDesempenho,
                ];
            } else {
                $resultadoSalve = [
                    'mensagemDesempenho' => "Aluno não jogou esse jogo ainda.",
                    'precisao' => 0,
                    'pontosPorSegundo' => 0,
                ];
            }    


            // Juntar resultados
            $resultado = [
                'mensagemDesempenho' => $resultadoCobrinha['mensagemDesempenho'],
                'precisao' => number_format($resultadoCobrinha['precisao'], 2) . '%', // Exibido como percentual
                'taxaColisoes' => number_format($resultadoCobrinha['taxaColisoes'], 2), // Mantém duas casas decimais
                'pontosPorSegundo' => number_format($resultadoCobrinha['pontosPorSegundo'], 2) . ' pts/s', // Pontos por segundo
                'mediaPontosPorJogo' => number_format($resultadoCobrinha['mediaPontosPorJogo'], 2) . ' pts', // Média de pontos

                'mensagemDesempenhoAdivinhe' => $resultadoAdivinhe['mensagemDesempenhoAdivinhe'],
                'precisaoAdivinhe' => number_format($resultadoAdivinhe['precisaoAdivinhe'], 2) . '%',
                'pontosPorSegundoAdivinhe' => number_format($resultadoAdivinhe['pontosPorSegundoAdivinhe'], 2) . ' pts/s',
                'mediaPontosPorJogoAdivinhe' => number_format($resultadoAdivinhe['mediaPontosPorJogoAdivinhe'], 2) . ' pts',

                'mensagemDesempenhoMemoria' => $resultadoMemoria['mensagemDesempenho'],
                'tempoMedioPorJogoMemoria' => $resultadoMemoria['tempoMedioPorJogo'],

                'mensagemDesempenhoTiro' => $resultadoTiro['mensagemDesempenhoTiro'],
                'precisaoTiro' => number_format($resultadoTiro['precisaoTiro'], 2) . '%',
                'pontosPorSegundoTiro' => number_format($resultadoTiro['pontosPorSegundoTiro'], 2) . ' pts/s',
                'mediaPontosPorJogoTiro' => number_format($resultadoTiro['mediaPontosPorJogoTiro'], 2) . ' pts',

                'mensagemDesempenhoPega' => $resultadoPega['mensagemDesempenho'],
                'precisaoPega' => number_format($resultadoPega['precisao'], 2) . '%',
                'mediaPontosPorJogoPega' => number_format($resultadoPega['mediaPontosPorJogo'], 2) . ' pts',

                'mensagemDesempenhoSalve' => $resultadoSalve['mensagemDesempenho'],
                'precisaoSalve' => $resultadoSalve['precisao'],
                'pontosPorSegundoSalve' => $resultadoSalve['pontosPorSegundo']
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