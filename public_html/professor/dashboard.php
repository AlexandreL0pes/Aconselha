<?php

use core\sistema\Autenticacao;

require_once '../../vendor/autoload.php';
require_once '../../config.php';

if (!isset($_COOKIE['token'])) {
  header("Location: ../login.php");
}

if (!Autenticacao::isProfessor($_COOKIE['token']) && !Autenticacao::isConselheiro($_COOKIE['token'])) {
  header("Location: ../login.php?erro=2");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Reunião</title>
  <link rel="stylesheet" href="../assets/css/index.css" />
  <link rel="stylesheet" href="../assets/css/dashboard.css" />
  <link rel="stylesheet" href="../assets/css/modals.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" />

  <link rel="stylesheet" href="../assets/css/bulma.min.css" />
</head>

<body>
  <div class="grid-container">
    <!-- Hamburg Icon -->
    <div class="menu-icon">
      <i class="fas fa-bars header__menu"></i>
    </div>
    <!-- Hamburg Icon -->

    <!-- Header -->
    <header class="header">
      <div class="dropdown is-right is-small no-border" id="dropdown-user">
        <div class="dropdown-trigger">
          <button class="button" aria-haspopup="true" aria-controls="dropdown-menu6">
            <span>Professor</span>
            <span class="icon is-small">
              <i class="fas fa-angle-down" aria-hidden="true"></i>
            </span>
          </button>
        </div>
        <div class="dropdown-menu" id="dropdown-menu6" role="menu">
          <div class="dropdown-content">
            <div class="dropdown-item">
              <a href="../coordenador/index.php" class="dropdown-item">
                Coordenador
              </a>
            </div>
          </div>
        </div>
      </div>
    </header>
    <!-- Header -->

  <!-- Sidebar -->
  <aside class="sidenav">
      <ul class="sidenav__list">
        <li class="sidenav__list-item first-item">
          <img src="../assets/images/logo_sadc.svg" alt="" class="logo-icon" />
        </li>
        <li class="sidenav__list-item turmas">
          <img src="../assets/images/grupo.svg" alt="" class="icone" />
        </li>
        <li class="sidenav__list-item reunioes">
          <img class="icone" src="../assets/images/reunion.svg" alt="" />
        </li>
        <!-- <li class="sidenav__list-item config">
          <img class="icone" src="../assets/images/config.svg" alt="" />
        </li> -->
      </ul>
      <div class="item-sair">
        <img src="../assets/images/logout.svg" alt="" class="sair-icone" />
      </div>
    </aside>
    <!-- Sidebar -->

    <!-- Sidebar Mobile -->
    <aside class="sidenav-mobile">
      <div class="sidenav__close-icon-mobile">
        <i class="fas fa-times sidenav__brand-close"></i>
      </div>
      <ul class="sidenav__list-mobile">
        <li class="sidenav__list-item-mobile turmas">
          <img src="../assets/images/grupo.svg" alt="" class="icone" />
          <p class="sidenav-item-text">Turmas</p>
        </li>
        <li class="sidenav__list-item-mobile reunioes">
          <img src="../assets/images/reunion.svg" alt="" class="icone" />
          <p class="sidenav-item-text">Conselhos Anteriores</p>
        </li>
        <!-- <li class="sidenav__list-item-mobile config">
          <img src="../assets/images/config.svg" alt="" class="icone" />
          <p class="sidenav-item-text">Configurações</p>
        </li> -->
      </ul>
      <div class="item-sair">
        <img src="../assets/images/logout.svg" alt="" class="sair-icone" />
        <p class="text">
          Sair
        </p>
      </div>
    </aside>
    <!-- Sidebar Mobile -->

    <main class="main">
      <div class="content" id="tabs-with-content">
        <div class="tabs">
          <ul>
            <li class="is-active"><a>Reunião</a></li>
            <li><a>Turma</a></li>
          </ul>
        </div>
        <div>
          <section class="tab-content">
            <div class="reuniao">
              <div class="ensino">
                <div class="titulo-avaliacoes">
                  Dificuldades de Aprendizado
                  <a class="mostrar-tudo">Mostrar mais
                    <i class="fas fa-angle-down" aria-hidden="true"></i></a>
                </div>
                <div class="avaliacoes" id="aprendizados"></div>
              </div>
              <div class="experiencia">
                <div class="titulo-avaliacoes">
                  Experiências
                  <a class="mostrar-tudo">Mostrar mais
                    <i class="fas fa-angle-down" aria-hidden="true"></i></a>
                </div>

                <div id="experiencias" class="avaliacoes"></div>
              </div>
              <div class="diagnostica">
                <div class="titulo-avaliacoes">
                  Avaliações Diagnósticas
                  <a class="mostrar-tudo">Mostrar mais
                    <i class="fas fa-angle-down" aria-hidden="true"></i></a>
                </div>

                <div id="diagnosticas" class="avaliacoes"></div>
              </div>
            </div>
          </section>

          <!-- INFORMAÇÕES TURMA -->
          <section class="tab-content main-dash">
            <div class="main-dash">
              <section class="overview">
                <div class="chart">
                  <h1 class="chart-title">Coeficiente Geral</h1>
                  <div class="canvas-chart">
                    <canvas id="coef-geral"></canvas>
                  </div>
                  <div class="legenda">
                    <div class="alto">
                      <span class="cor"></span>
                      <span class="text">Alto</span>
                    </div>
                    <div class="medio">
                      <span class="cor"></span>
                      <span class="text">Médio</span>
                    </div>
                    <div class="baixo">
                      <span class="cor"></span>
                      <span class="text">Baixo</span>
                    </div>
                  </div>
                </div>

                <div class="estatistica-turma" id="estatistica-turma">
                  <div class="card-info coef-geral">
                    <p class="descricao">Coeficiente Geral</p>
                    <p class="resultado">0,0</p>
                  </div>
                  <div class="card-info experiencia">
                    <p class="descricao">Experiências</p>
                    <p class="resultado">0</p>
                  </div>
                  <div class="card-info aprendizado">
                    <p class="descricao">Ensino-Aprendizado</p>
                    <p class="resultado">0</p>
                  </div>
                  <div class="card-info medidas">
                    <p class="descricao">Medidas Disciplinares</p>
                    <p class="resultado">0</p>
                  </div>
                </div>

                <div class="avaliacoes-diagnostica">
                  <h1>Principais Avaliações</h1>
                  <div class="avaliacoes" id="avaliacoes">
                  </div>
                </div>
              </section>

              <section class="medidas">
                <div class="titulo-medidas">
                  <h1>Medidas Disciplinares</h1>
                  <a class="mostrar-tudo">Mostrar mais
                    <i class="fas fa-angle-down" aria-hidden="true"></i></a>
                </div>
                <div class="lista-medidas">
                  <!-- <div class="mostrar-mais">
                <span>+5</span>
              </div> -->
                </div>
              </section>

              <section class="alunos">
                <h1 class="principal">Estudantes</h1>
                <div class="pesquisa">
                  <div class="field has-addons tipos">
                    <p class="control">
                      <button class="button is-small" id="filtrarAlto">
                        <span>Alto</span>
                      </button>
                    </p>
                    <p class="control">
                      <button class="button is-small align-center" id="filtrarMedio">
                        <span>Médio</span>
                      </button>
                    </p>
                    <p class="control">
                      <button class="button is-small align-center" id="filtrarBaixo">
                        <span>Baixo</span>
                      </button>
                    </p>
                    <p class="control">
                      <button class="button is-small align-center" id="removerFiltro">
                        <span>Todos</span>
                      </button>
                    </p>
                  </div>
                </div>
                <div class="overview-alunos"></div>
                <div class="lista-estudantes" id="lista-estudantes">
                  <!-- <div class="cardbox card-turma alto" data-aluno="2017103202030090">
              <p class="subtitulo is-6">Alexandre Lopes</p>
              <p class="subtitulo is-8 gray-text">2017103202030090</p>
              <p class="subtitulo is-7">9,0</p>
            </div> -->
                </div>
              </section>
            </div>


          </section>
          <!-- INFORMAÇÕES TURMA -->
        </div>
      </div>
      <aside class="side">
        <div class="turma-info">
          <span>
            <span name="turma" id="nome"></span> -
            <span name="curso" id="curso"></span>
          </span>
          <!-- <span class="gray-text" >
              Prof. Adriano Honorato Braga
            </span> -->
          <span class="gray-text" id="codigo"></span>
        </div>
        <div class="reuniao-info">
          <div class="ensino">
            <p class="info-titulo">Ensino-Aprendizado</p>
            <div class="resultados">
              <div class="aprendizado">
                <p class="resultados-titulo">Aprendizado</p>
                <div class="resultado-quantidade">
                  <span class="color-aprendizado"></span>
                  <span class="quantidade"></span>
                </div>
              </div>
              <div class="experiencia">
                <p class="resultados-titulo">Experiências</p>
                <div class="resultado-quantidade">
                  <span class="color-experiencia"></span>
                  <span class="quantidade"></span>
                </div>
              </div>
            </div>
          </div>
          <div class="diagnostica">
            <p class="info-titulo">Diagnóstica</p>
            <div class="resultados">
              <div class="positivos">
                <p class="resultados-titulo">Positivos</p>
                <div class="resultado-quantidade">
                  <span class="color-positivo"></span>
                  <span class="quantidade"></span>
                </div>
              </div>
              <div class="negativos">
                <p class="resultados-titulo">Negativos</p>
                <div class="resultado-quantidade">
                  <span class="color-negativo"></span>
                  <span class="quantidade"></span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="actions">
          <a class="button is-small" href="./diagnostica.php">
            <span>Avaliação Diagnóstica</span>
          </a>
        </div>
      </aside>
    </main>

    <footer class="footer">
      <div class="footer_nepeti">
        <img class="logo_nepeti" src="../assets/images/logo_nepeti.png" alt="" />
        <p class="text-nepeti">
          &copy; 2020 - Núcleo de Estudos e Pesquisa em Tecnologia da
          Informação
        </p>
      </div>
    </footer>

    <div class="modal visualizar" id="visualizar-ensino">
      <div class="modal-background"></div>
      <div class="modal-card modal-avaliacao ensino">
        <header class="modal-card-head">
          <p class="modal-card-title"></p>
          <div class="modal-close-btn">
            <i class="fas fa-times sidenav__brand-close"></i>
          </div>
        </header>
        <section class="modal-card-body">
          <div class="modal-ensino">
            <div class="info">
              <p class="observacao"></p>
            </div>
            <div class="info-alunos">
              <h5 class="titulo-estudante">Estudantes</h5>
              <div class="estudantes">
                <div class="chips"></div>
              </div>
            </div>
          </div>
        </section>
        <footer class="modal-card-foot"></footer>
      </div>
    </div>
    <div class="modal visualizar" id="visualizar-experiencia">
      <div class="modal-background"></div>
      <div class="modal-card modal-avaliacao experiencia">
        <header class="modal-card-head">
          <p class="modal-card-title"></p>
          <div class="modal-close-btn">
            <i class="fas fa-times sidenav__brand-close"></i>
          </div>
        </header>
        <section class="modal-card-body">
          <div class="modal-experiencia">
            <div class="info">
              <div class="chip-categoria positivo"></div>
              <p class="observacao"></p>
            </div>
            <div class="info-disciplinas">
              <h5 class="titulo-disciplina">Disciplinas</h5>
              <div class="disciplinas">
                <div class="chips"></div>
              </div>
            </div>
          </div>
        </section>
        <footer class="modal-card-foot"></footer>
      </div>
    </div>
    <div class="modal visualizar" id="visualizar-diagnostica">
      <div class="modal-background"></div>
      <div class="modal-card modal-avaliacao diagnostica positiva">
        <header class="modal-card-head">
          <div class="modal-card-title"></div>
          <div class="modal-close-btn">
            <i class="fas fa-times sidenav__brand-close"></i>
          </div>
        </header>
        <section class="modal-card-body">
          <div class="modal-diagnostica">
            <div class="info-professores">
              <h5 class="titulo-disciplina">Professores</h5>
              <div class="professores">
                <div class="chips"></div>
              </div>
            </div>
            <div class="info-perfil">
              <h5 class="titulo-perfil">Perfis</h5>
              <div class="perfis">
                <div class="chips"></div>
              </div>
            </div>
          </div>
        </section>
        <footer class="modal-card-foot"></footer>
      </div>
    </div>

    <div class="modal visualizar" id="visualizar-medida">
      <div class="modal-background"></div>
      <div class="modal-card">
        <header class="modal-card-head">
          <div class="modal-card-title">Medida Disciplinar</div>
          <div class="modal-close-btn">
            <i class="fas fa-times sidenav__brand-close"></i>
          </div>
        </header>
        <section class="modal-card-body">
          <div class="modal-medida">
            <div class="info-m">
              <div class="info-aluno">
                <p class="nome"></p>
                <p class="matricula"></p>
                <p class="data"></p>
              </div>
            </div>
            <div class="info-medida">
              <div class="tipo-medida">
                <p>Ocorrência Leve</p>
              </div>
              <p class="observacao">
                Lorem ipsum, dolor sit amet consectetur adipisicing elit.
                Error quo quasi doloribus placeat quaerat, laboriosam beatae
                libero optio hic! Quaerat quidem eum soluta distinctio
                laudantium quo aperiam reprehenderit vitae laborum!
              </p>
            </div>
          </div>
        </section>
        <footer class="modal-card-foot"></footer>
      </div>
    </div>
  </div>
  <div class="toasts" id="toasts"></div>
</body>
<script src="../assets/js/index.js" type="module"></script>
<script src="../assets/js/dashboard_reuniao.js" type="module"></script>
<script src="../assets/js/dashboard_turma.js" type="module"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.3/dist/Chart.min.js"></script>
<!-- <script src="../assets/js/atendimentos.js" type="module"></script> -->

</html>