<?php

use core\sistema\Autenticacao;

require_once '../../vendor/autoload.php';
require_once '../../config.php';

if (!isset($_COOKIE['token'])) {
  header("Location: ../login.html");
}

if (!Autenticacao::isProfessor($_COOKIE['token']) || !Autenticacao::isConselheiro($_COOKIE['token'])) {
  header("Location: ../login.html?erro=2");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Avaliação Diagnóstica</title>
  <link rel="stylesheet" href="../assets/css/index.css" />
  <link rel="stylesheet" href="../assets/css/dropdown.css" />
  <link rel="stylesheet" href="../assets/css/diagnostica.css" />
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
              <a href="#" class="dropdown-item">
                Professor
              </a>
              <hr class="dropdown-divider" />
              <a href="#" class="dropdown-item">
                Coordenador
              </a>
              <hr class="dropdown-divider" />
              <a href="#" class="dropdown-item">
                Aluno
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
        <h4 class="titulo is-5">Avaliação Diagnóstica</h4>
        <div class="card-info turma-info">
          <span><span name="turma" id="nome"></span> -
            <span name="curso" id="curso"></span></span>
          <!-- <span class="subtitulo is-8 gray-text"
              >Prof. Adriano Honorato Braga</span
            > -->
          <span class="subtitulo is-8 gray-text" id="codigo">20141.03INF10I.3B</span>
          <a class="button is-small" href="./diagnostica.html">
            <span>Informações</span>
          </a>
        </div>
      </div>
      <div class="avaliacoes">
        <div>
          <h4 class="subtitulo is-7">Avaliações em andamento</h4>
          <p class="resultadoSelecionado">
            Restam
            <span class="" id="qtdAvaliacoes">0</span>
            avaliações pendentes
          </p>
        </div>

        <a class="button is-success" href="./dashboard.html">
          <span class="icon is-small">
            <i class="fas fa-check"></i>
          </span>
          <span>Finalizar</span>
        </a>
      </div>
      <div class="alunos" id="alunos">
      </div>
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
    <div class="modal" id="avaliacao-diagnostica">
      <div class="modal-background"></div>
      <div class="modal-card">
        <header class="modal-card-head">
          <p class="modal-card-title">Avaliação Diagnóstica</p>
          <div class="modal-close-btn">
            <i class="fas fa-times sidenav__brand-close"></i>
          </div>
        </header>
        <section class="modal-card-body">
          <div class="avaliacao-aluno">
            <div class="info">
              <p class="" name="nome">Alexandre Ferreira Lopes</p>
              <p class="gray-text subtitulo is-7" name="curso">
                Informática para Internet
              </p>
              <p class="gray-text subtitulo is-7" name="matricula">
                2017103202030090
              </p>
            </div>
            <div class="perfil-aluno">
              <p>Selecione as categorias que se adequam ao aluno:</p>
              <div class="perfis" id="opcoes-perfis"></div>
            </div>
          </div>
        </section>
        <footer class="modal-card-foot">
          <div></div>
          <button class="button is-success save-avaliation" name="" id="salvar-diagnostica">
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
<script src="../assets/js/index.js" type="module"></script>
<script src="../assets/js/diagnostica.js" type="module"></script>

</html>