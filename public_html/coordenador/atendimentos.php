<?php

use core\sistema\Autenticacao;

require_once '../../vendor/autoload.php';
require_once '../../config.php';

if (!isset($_COOKIE['token'])) {
  header("Location: ../login.php");
}

if (!Autenticacao::isCoordenador($_COOKIE['token'])) {
  header("Location: ../login.php?erro=2");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Atendimentos</title>
  <link rel="stylesheet" href="../assets/css/index.css" />
  <link rel="stylesheet" href="../assets/css/dropdown.css" />
  <link rel="stylesheet" href="../assets/css/atendimento.css" />
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
            <span>Coordenador</span>
            <span class="icon is-small">
              <i class="fas fa-angle-down" aria-hidden="true"></i>
            </span>
          </button>
        </div>
        <div class="dropdown-menu" id="dropdown-menu6" role="menu">
          <div class="dropdown-content">
            <div class="dropdown-item">
              <a href="../professor/index.php" class="dropdown-item">
                Professor
              </a>
              <hr class="dropdown-divider" />
              <a href="../gerencia/index.php" class="dropdown-item">
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
        <li class="sidenav__list-item estudantes">
          <img src="../assets/images/student.svg" alt="" class="icone" />
        </li>
        <li class="sidenav__list-item turmas">
          <img src="../assets/images/grupo.svg" alt="" class="icone" />
        </li>
        <li class="sidenav__list-item reunioes">
          <img class="icone" src="../assets/images/reunion.svg" alt="" />
        </li>
        <li class="sidenav__list-item config">
          <img class="icone" src="../assets/images/config.svg" alt="" />
        </li>
      </ul>
    </aside>
    <!-- Sidebar -->

    <!-- Sidebar Mobile -->
    <aside class="sidenav-mobile">
      <div class="sidenav__close-icon-mobile">
        <i class="fas fa-times sidenav__brand-close"></i>
      </div>
      <ul class="sidenav__list-mobile">
        <li class="sidenav__list-item-mobile estudantes">
          <img src="../assets/images/student.svg" alt="" class="icone" />
          <p class="sidenav-item-text">Alunos</p>
        </li>
        <li class="sidenav__list-item-mobile turmas">
          <img src="../assets/images/grupo.svg" alt="" class="icone" />
          <p class="sidenav-item-text">Turmas</p>
        </li>
        <li class="sidenav__list-item-mobile reunioes">
          <img src="../assets/images/reunion.svg" alt="" class="icone" />
          <p class="sidenav-item-text">Conselhos Anteriores</p>
        </li>
        <li class="sidenav__list-item-mobile config">
          <img src="../assets/images/config.svg" alt="" class="icone" />
          <p class="sidenav-item-text">Configurações</p>
        </li>
      </ul>
    </aside>
    <!-- Sidebar Mobile -->

    <main class="main">
      <div class="page-head">
        <h4 class="subtitulo is-5">Encaminhamentos Psicopedagógicos</h4>
        <div class="turma-info">
          <span>
            <span name="turma" id="nome"></span> -
            <span name="curso" id="curso"></span>
          </span>
          <!-- <span class="gray-text" >
              Prof. Adriano Honorato Braga
            </span> -->
          <span class="gray-text" id="codigo"></span>
          <a class="button" href="./dashboard.php">
            Informações
          </a>
        </div>
      </div>

      <div class="descricao">
        <div>
          <h4 class="subtitulo is-7">Encaminhamentos Salvos</h4>
          <p class="resultadoSelecionado">
            <span class="" id="qtdEncaminhamentos"></span>
          </p>
        </div>
        <button class="button is-success is-right" id="add-encaminhamento">
          <span class="icon is-small">
            <i class="fa fa-check" aria-hidden="true"></i>
          </span>
          <span>Novo Encaminhamento</span>
        </button>
      </div>
      <div class="encaminhamentos"></div>
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

    <div class="modal" id="encaminhamento">
      <div class="modal-background"></div>
      <div class="modal-card">
        <header class="modal-card-head">
          <p class="modal-card-title">Encaminhamento</p>
          <div class="modal-close-btn">
            <i class="fas fa-times sidenav__brand-close"></i>
          </div>
        </header>
        <section class="modal-card-body">
          <div class="columns">
            <div class="column">
              <div class="field">
                <label class="label">Estudante</label>
                <div class="dropdown">
                  <div class="dropdown-trigger">
                    <div class="control has-icons-left has-icons-right">
                      <input class="input" type="text" placeholder="Digite o nome do aluno" value="" id="aluno" />
                      <span class="icon is-small is-left">
                        <i class="fa fa-search" aria-hidden="true"></i>
                      </span>
                    </div>
                    <div class="dropdown-menu" id="aluno-menu" role="menu"></div>
                  </div>
                </div>

                <!-- <p class="help is-success">This username is available</p> -->
              </div>
            </div>
            <div class="column is-three-fifths">
              <div class="field">
                <label class="label">Professores</label>
                <div class="dropdown">
                  <div class="dropdown-trigger">
                    <div class="control has-icons-left has-icons-right">
                      <input class="input" type="text" placeholder="Digite o nome do professor" value="" id="professores" />
                      <span class="icon is-small is-left">
                        <i class="fa fa-search" aria-hidden="true"></i>
                      </span>
                    </div>

                    <div class="dropdown-menu" id="professores-menu" role="menu"></div>
                  </div>
                </div>

                <!-- <p class="help is-success">This username is available</p> -->

                <div class="professores-selecionados chips"></div>
              </div>
            </div>
          </div>

          <div class="field">
            <label class="label">Queixa</label>
            <div class="control">
              <textarea class="textarea" placeholder="Descreva a motivação do encaminhamento" id="queixa"></textarea>
            </div>
          </div>
          <div class="field">
            <label class="label">Intervenção</label>
            <div class="control">
              <div class="select">
                <select id="intervencao"> </select>
              </div>
            </div>
          </div>
        </section>
        <footer class="modal-card-foot">
          <button class="button is-danger is-outlined excluir-encaminhamento" name="">
            <span class="icon is-small">
              <i class="fa fa-times" aria-hidden="true"></i>
            </span>
            <span>Excluir</span>
          </button>
          <button class="button is-success salvar-encaminhamento" name="">
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
<script src="../assets/js/atendimentos.js" type="module"></script>

</html>