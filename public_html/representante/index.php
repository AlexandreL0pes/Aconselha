<?php

require_once '../../vendor/autoload.php';
require_once '../../config.php';

use core\controller\Reunioes;
use core\sistema\Autenticacao;

$r = new Reunioes();

// Verifica a existência da Token
if (!isset($_COOKIE['token'])) {
  header("Location: ../login.php");
}

// Verifica se a reunião está em andamento
if (isset($_COOKIE['token']) && !$r->turma_em_reuniao($_COOKIE['token'])) {
  header("Location: ../login.php?erro=1");
}

// Verifica se o usuário tem permissão para acessar a página
if (!Autenticacao::isRepresentante($_COOKIE['token']) && !Autenticacao::isViceRepresentante($_COOKIE['token'])) {
  header("Location: ../login.php?erro=2");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Ensino-Aprendizagem</title>
  <link rel="stylesheet" href="../assets/css/index.css" />
  <link rel="stylesheet" href="../assets/css/dropdown.css" />
  <link rel="stylesheet" href="../assets/css/ensino.css" />
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


    <!-- Sidebar -->
    <aside class="sidenav">
      <ul class="sidenav__list">
        <li class="sidenav__list-item first-item">
          <img src="../assets/images/logo_sadc.svg" alt="" class="logo-icon" />
        </li>
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
      <div class="page-head">
        <h4 class="titulo is-5">Avaliação de Ensino-Aprendizagem</h4>
        <div class="card-info turma-info">
          <span><span name="turma" id="nome"></span> -
            <span name="curso" id="curso"></span></span>
          <!-- <span class="subtitulo is-8 gray-text"
              >Prof. Adriano Honorato Braga</span
            > -->
          <span class="subtitulo is-8 gray-text" id="codigo"></span>
        </div>
        <div class="actions">
          <button class="button is-success is-right" id="btnEnsino">
            <span class="icon is-small">
              <i class="fa fa-plus" aria-hidden="true"></i>
            </span>
            <span>Dificuldade de Aprendizado</span>
          </button>
          <button class="button is-success is-right" id="btnExperiencia">
            <span class="icon is-small">
              <i class="fa fa-plus" aria-hidden="true"></i>
            </span>
            <span>Experiências</span>
          </button>
        </div>
      </div>
      <div class="descricao">
        <div>
          <h4 class="subtitulo is-7">Avaliações em andamento</h4>
          <p class="resultadoSelecionado">
            <span class="" id="qtdAvaliacoes">3</span>
          </p>
        </div>
        <div class="pesquisa">
          <div class="field has-addons tipos">
            <p class="control">
              <button class="button is-small" id="filtrarEnsino">
                <span>Ensino</span>
              </button>
            </p>
            <p class="control">
              <button class="button is-small align-center" id="removerFiltro">
                <span>Ambas</span>
              </button>
            </p>
            <p class="control">
              <button class="button is-small align-center" id="filtrarExperiencia">
                <span>Experiências</span>
              </button>
            </p>
          </div>
        </div>
      </div>


      <div class="avaliacoes"></div>
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
    <div class="modal" id="avaliacao-aprendizado">
      <div class="modal-background"></div>
      <div class="modal-card">
        <header class="modal-card-head">
          <p class="modal-card-title">Avaliação de Ensino-Aprendizagem</p>
          <div class="modal-close-btn">
            <i class="fas fa-times sidenav__brand-close"></i>
          </div>
        </header>
        <section class="modal-card-body">
          <div class="formEncaminhamento">
            <div class="field">
              <label class="label">Disciplina</label>
              <div class="dropdown">
                <div class="dropdown-trigger">
                  <div class="control has-icons-left has-icons-right">
                    <input class="input" type="text" placeholder="Digite o nome do aluno" value="" id="ensino-disciplina" />
                    <span class="icon is-small is-left">
                      <i class="fa fa-search" aria-hidden="true"></i>
                    </span>
                  </div>
                  <div class="dropdown-menu text-capitalize" id="ensino-disciplina-menu" role="menu"></div>
                </div>
              </div>
            </div>
            <div class="field">
              <label class="label">Estudantes</label>
              <div class="dropdown">
                <div class="dropdown-trigger">
                  <div class="control has-icons-left has-icons-right">
                    <input class="input" type="text" placeholder="Digite o nome do professor" value="" id="ensino-estudantes" />
                    <span class="icon is-small is-left">
                      <i class="fa fa-search" aria-hidden="true"></i>
                    </span>
                  </div>
                  <div class="dropdown-menu text-capitalize" id="ensino-estudantes-menu" role="menu"></div>
                </div>
              </div>
            </div>

            <div class="chips estudantes" id="ensino-estudantes-selecionados"></div>

            <div class="field">
              <label class="label">Observação</label>
              <div class="control">
                <textarea id="ensino-descricao" class="textarea" placeholder="Descreva a motivação do encaminhamento"></textarea>
              </div>
            </div>
          </div>
        </section>
        <footer class="modal-card-foot">
          <button class="button is-danger excluir-ensino" name="" id="excluir-ensino">
            <span class="icon is-small">
              <i class="fa fa-times" aria-hidden="true"></i>
            </span>
            <span>Excluir</span>
          </button>
          <div></div>

          <button class="button is-success salvar-ensino" name="" id="salvar-ensino">
            <span class="icon is-small">
              <i class="fa fa-check" aria-hidden="true"></i>
            </span>
            <span>Salvar</span>
          </button>
        </footer>
      </div>
    </div>

    <div class="modal" id="avaliacao-experiencia">
      <div class="modal-background"></div>
      <div class="modal-card">
        <header class="modal-card-head">
          <p class="modal-card-title">Experiências</p>
          <div class="modal-close-btn">
            <i class="fas fa-times sidenav__brand-close"></i>
          </div>
        </header>
        <section class="modal-card-body">
          <div class="columns">
            <div class="column is-two-thirds">
              <div class="field">
                <label class="label">Título</label>
                <p class="control">
                  <input id="experiencia-titulo" class="input" type="text" placeholder="Breve descricão da experiência" />
                </p>
              </div>
            </div>
            <div class="column">
              <div class="field">
                <label class="label">Categoria</label>
                <div class="control">
                  <div class="select">
                    <select id="experiencia-categoria"> </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="field">
            <label class="label">Disciplinas</label>
            <div class="dropdown">
              <div class="dropdown-trigger">
                <div class="control has-icons-left has-icons-right">
                  <input class="input" type="text" placeholder="Digite o nome do professor" value="" id="experiencia-disciplinas" />
                  <span class="icon is-small is-left">
                    <i class="fa fa-search" aria-hidden="true"></i>
                  </span>
                </div>
                <div class="dropdown-menu text-capitalize" id="experiencia-disciplinas-menu" role="menu"></div>
              </div>
            </div>
          </div>

          <div class="chips disciplinas" id="experiencia-disciplinas-selecionadas"></div>

          <div class="field">
            <label class="label">Descrição</label>
            <div class="control">
              <textarea id="experiencia-descricao" class="textarea" placeholder="Detalhe aqui sua sugestão"></textarea>
            </div>
          </div>
        </section>
        <footer class="modal-card-foot">
          <button class="button is-danger excluir-experiencia" name="" id="excluir-experiencia">
            <span class="icon is-small">
              <i class="fa fa-times" aria-hidden="true"></i>
            </span>
            <span>Excluir</span>
          </button>
          <div></div>
          <button class="button is-success salvar-experiencia" name="" id="salvar-experiencia">
            <span class="icon is-small">
              <i class="fa fa-check" aria-hidden="true"></i>
            </span>
            <span>Salvar</span>
          </button>
        </footer>
      </div>
    </div>
  </div>
  <div class="toasts" id="toasts"></div>
</body>
<script src="https://cdn.rawgit.com/mattmezza/bulmahead/master/dist/bulmahead.bundle.js"></script>
<script src="../assets/js/index.js" type="module"></script>
<script src="../assets/js/ensino.js" type="module"></script>

</html>