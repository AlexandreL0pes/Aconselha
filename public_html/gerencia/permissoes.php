<?php

use core\sistema\Autenticacao;

require_once '../../vendor/autoload.php';
require_once '../../config.php';

if (!isset($_COOKIE['token'])) {
  header("Location: ../login.php");
}

if (!Autenticacao::isGerente($_COOKIE['token'])) {
  header("Location: ../login.php?erro=2");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Permissões</title>
  <link rel="stylesheet" href="../assets/css/index.css" />
  <link rel="stylesheet" href="../assets/css/dropdown.css" />
  <link rel="stylesheet" href="../assets/css/permissoes.css" />
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
            <span>Gerência</span>
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
        <li class="sidenav__list-item anteriores">
          <img src="../assets/images/seta-para-tras.svg" alt="" class="icone" />
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
        <li class="sidenav__list-item-mobile turmas">
          <img src="../assets/images/grupo.svg" alt="" class="icone" />
          <p class="sidenav-item-text">Turmas</p>
        </li>
        <li class="sidenav__list-item-mobile reunioes">
          <img src="../assets/images/reunion.svg" alt="" class="icone" />
          <p class="sidenav-item-text">Conselhos Anteriores</p>
        </li>
        <li class="sidenav__list-item-mobile anteriores">
          <img src="../assets/images/seta-para-tras.svg" alt="" class="icone" />
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
      <!-- <h4 class="">Gerenciamento de Permissões</h4> -->

      <section class="coordenacao" id="coordenacao">
        <div class="head">
          <h4 class="titulo is-5">Coordenadores</h4>
        </div>
        <div class="coordenadores" id="coordenadores">
          <div class="cardbox card-coordenador is-info" data-curso="1">
            <p class="gray-text">Informática para Internet</p>
            <p class=" ">Adriano Braga</p>
          </div>
          <div class="cardbox card-coordenador is-amb" data-curso="2">
            <p class="gray-text">Meio Ambiente</p>
            <p class=" ">Adriano Braga</p>
          </div>
          <div class="cardbox card-coordenador is-agro" data-curso="3">
            <p class="gray-text">Agropecuária</p>
            <p class=" ">Adriano Braga</p>
          </div>
        </div>
      </section>
      <section class="conselho" id="conselho">
        <div class="head">
          <h4 class="titulo is-5">Conselheiros</h4>
        </div>
        <div class="conselheiros" id="conselheiros">
          <div class="cardbox card-conselheiro is-amb" data-turmaconselho="1">
            <p class="gray-text">Meio Ambiente</p>
            <p class="">Adriano Braga</p>
            <p class="gray-text">3° B</p>
          </div>
          <div class="cardbox card-conselheiro is-info" data-turmaconselho="1">
            <p class="subtitulo is-8 gray-text">Informática para Internet</p>
            <p class="subtitulo is-7">Adriano Braga</p>
            <p class="subtitulo is-8 gray-text">1° B</p>
          </div>
          <div class="cardbox card-conselheiro is-agro" data-turmaconselho="1">
            <p class="subtitulo is-8 gray-text">Agropecuária</p>
            <p class="subtitulo is-7">Adriano Braga</p>
            <p class="subtitulo is-8 gray-text">2° B</p>
          </div>
          <div class="cardbox card-conselheiro is-amb" data-turmaconselho="1">
            <p class="subtitulo is-8 gray-text">Meio Ambiente</p>
            <p class="subtitulo is-7">Adriano Braga</p>
            <p class="subtitulo is-8 gray-text">1° B</p>
          </div>
        </div>
      </section>
      <section class="professor" id="professor">
        <div class="head">
          <h4 class="titulo is-5">Professores</h4>
          <button class="button  is-success" id="atualizar-professores">
            <span>Atualizar Professores</span>
            </button>
        </div>
        <div class="professores" id="professores">
          <div class="cardbox card-professor" data-usuario="1">
            <p class="">Adriano Braga</p>
            <p class="gray-text">adriano.braga@gmail.com</p>
          </div>
          <div class="cardbox card-professor" data-usuario="1">
            <p class="">Adriano Braga</p>
            <p class="gray-text">adriano.braga@gmail.com</p>
          </div>
          <div class="cardbox card-professor" data-usuario="1">
            <p class="">Adriano Braga</p>
            <p class="gray-text">adriano.braga@gmail.com</p>
          </div>
          <div class="cardbox card-professor" data-usuario="1">
            <p class="">Adriano Braga</p>
            <p class="gray-text subtitulo is-7">adriano.braga@gmail.com</p>
          </div>
        </div>
      </section>

      <section class="representacao" id="representacao">
        <h4></h4>
        <div class="head">
          <h4 class="titulo is-5">Representantes</h4>
        </div>
        <div class="representantes" id="representantes">
          <div class="cardbox card-representante is-amb" data-turmaconselho="1">
            <p class="subtitulo is-7 gray-text">Meio Ambiente</p>
            <p class="subtitulo is-6">1° B</p>
          </div>
          <div class="cardbox card-representante is-amb" data-turmaconselho="1">
            <p class="gray-text">Meio Ambiente</p>
            <p class="">1° B</p>
          </div>
          <div class="cardbox card-representante is-amb" data-turmaconselho="1">
            <p class="gray-text">Meio Ambiente</p>
            <p class="">1° B</p>
          </div>
          <div class="cardbox card-representante is-amb" data-turmaconselho="1">
            <p class="gray-text">Meio Ambiente</p>
            <p class="">1° B</p>
          </div>
        </div>
      </section>
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
    <div class="modal coordenador" id="modal-coordenador" data-curso="2" data-coordenador="41917">
      <div class="modal-background"></div>
      <div class="modal-card">
        <header class="modal-card-head">
          <p class="modal-card-title">Coordenação de Curso</p>
          <div class="modal-close-btn">
            <i class="fas fa-times sidenav__brand-close"></i>
          </div>
        </header>
        <section class="modal-card-body">
          <div class="info-curso">
            <p class="gray-text">Técnico Integrado ao Ensino Médio</p>
            <p class="" id="coordenacao-curso">Informática para Internet</p>
            <p class="gray-text">Integral</p>
          </div>
          <div class="coordenador">
            <div class="field">
              <label class="label">Coordenador</label>
              <div class="dropdown">
                <div class="dropdown-trigger">
                  <div class="control has-icons-left has-icons-right">
                    <input class="input" type="text" placeholder="Digite o nome do servidor" value="" id="coordenador" />
                    <span class="icon is-small is-left">
                      <i class="fa fa-search" aria-hidden="true"></i>
                    </span>
                  </div>
                  <div class="dropdown-menu" id="coordenador-menu" role="menu"></div>
                </div>
              </div>
            </div>

            <div class="field">
              <label class="label">E-mail</label>
              <div class="dropdown">
                <div class="dropdown-trigger">
                  <div class="control has-icons-left has-icons-right">
                    <input class="input" type="text" placeholder="Digite seu email" value="" id="email-coordenador" />
                    <span class="icon is-small is-left">
                      <i class="fa fa-user" aria-hidden="true"></i>
                    </span>
                  </div>
                </div>
              </div>
              <p class="help">Se possível, insira seu e-mail institucional</p>
            </div>
            <div class="field">
              <label class="label">Senha</label>
              <div class="dropdown">
                <div class="dropdown-trigger">
                  <div class="control has-icons-left has-icons-right">
                    <input class="input" type="password" placeholder="Digite a senha" value="" id="senha-coordenador" />
                    <span class="icon is-small is-left">
                      <i class="fa fa-lock" aria-hidden="true"></i>
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
        <footer class="modal-card-foot">
          <div></div>
          <div></div>
          <button class="button is-success salvar-coordenador" name="" id="salvar-coordenador">
            <span class="icon is-small">
              <i class="fa fa-check" aria-hidden="true"></i>
            </span>
            <span>Salvar</span>
          </button>
        </footer>
      </div>
    </div>

    <div class="modal conselheiro" id="modal-conselheiro">
      <div class="modal-background"></div>
      <div class="modal-card">
        <header class="modal-card-head">
          <p class="modal-card-title">Conselheiro de Turma</p>
          <div class="modal-close-btn">
            <i class="fas fa-times sidenav__brand-close"></i>
          </div>
        </header>
        <section class="modal-card-body">
          <div class="info-turma">
            <p class="" id="conselheiro-turma">3°B</p>
            <p class="gray-text" id="conselheiro-curso">Informática para Internet</p>
            <p class="gray-text">Técnico Integrado ao Ensino Médio</p>
          </div>
          <div class="conselheiro">
            <div class="field">
              <label class="label">Conselheiro</label>
              <div class="dropdown">
                <div class="dropdown-trigger">
                  <div class="control has-icons-left has-icons-right">
                    <input class="input" type="text" placeholder="Digite o nome do servidor" value="" id="conselheiro" />
                    <span class="icon is-small is-left">
                      <i class="fa fa-search" aria-hidden="true"></i>
                    </span>
                  </div>
                  <div class="dropdown-menu" id="conselheiro-menu" role="menu"></div>
                </div>
              </div>
            </div>

            <div class="field">
              <label class="label">E-mail</label>
              <div class="dropdown">
                <div class="dropdown-trigger">
                  <div class="control has-icons-left has-icons-right">
                    <input class="input" type="text" placeholder="Digite seu email" value="" id="email-conselheiro" />
                    <span class="icon is-small is-left">
                      <i class="fa fa-user" aria-hidden="true"></i>
                    </span>
                  </div>
                </div>
              </div>
              <p class="help">Se possível, insira seu e-mail institucional</p>
            </div>
            <div class="field">
              <label class="label">Senha</label>
              <div class="dropdown">
                <div class="dropdown-trigger">
                  <div class="control has-icons-left has-icons-right">
                    <input class="input" type="password" placeholder="Digite a senha" value="" id="senha-conselheiro" />
                    <span class="icon is-small is-left">
                      <i class="fa fa-lock" aria-hidden="true"></i>
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
        <footer class="modal-card-foot">
          <div></div>
          <div></div>
          <button class="button is-success salvar-conselheiro" name="" id="">
            <span class="icon is-small">
              <i class="fa fa-check" aria-hidden="true"></i>
            </span>
            <span>Salvar</span>
          </button>
        </footer>
      </div>
    </div>

    <div class="modal representante" id="modal-representante">
      <div class="modal-background"></div>
      <div class="modal-card">
        <header class="modal-card-head">
          <p class="modal-card-title">3° B - Informática para Internet</p>
          <div class="modal-close-btn">
            <i class="fas fa-times sidenav__brand-close"></i>
          </div>
        </header>
        <section class="modal-card-body">
          <div>
            <div class="field">
              <label class="label">Líder</label>
              <div class="dropdown">
                <div class="dropdown-trigger">
                  <div class="control has-icons-left has-icons-right">
                    <input class="input" type="text" placeholder="Digite o nome do líder" value="" id="representante" />
                    <span class="icon is-small is-left">
                      <i class="fa fa-search" aria-hidden="true"></i>
                    </span>
                  </div>
                  <div class="dropdown-menu" id="representante-menu" role="menu"></div>
                </div>
              </div>
            </div>
            <div class="field">
              <label class="label">Senha</label>
              <div class="dropdown">
                <div class="dropdown-trigger">
                  <div class="control has-icons-left has-icons-right">
                    <input class="input" type="password" placeholder="Digite a senha" value="" id="senha-representante" />
                    <span class="icon is-small is-left">
                      <i class="fa fa-lock" aria-hidden="true"></i>
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <div class="salvar-btn">
              <button class="button is-success salvar-representante is-pulled-right" name="" id="salvar-experiencia">
                <span class="icon is-small">
                  <i class="fa fa-check" aria-hidden="true"></i>
                </span>
                <span>Salvar</span>
              </button>
            </div>
          </div>
          <div>
            <div class="field">
              <label class="label">Vice-Líder</label>
              <div class="dropdown">
                <div class="dropdown-trigger">
                  <div class="control has-icons-left has-icons-right">
                    <input class="input" type="text" placeholder="Digite o nome do vice-líder" value="" id="vice-representante" />
                    <span class="icon is-small is-left">
                      <i class="fa fa-search" aria-hidden="true"></i>
                    </span>
                  </div>
                  <div class="dropdown-menu" id="vice-representante-menu" role="menu"></div>
                </div>
              </div>
            </div>

            <div class="field">
              <label class="label">Senha</label>
              <div class="dropdown">
                <div class="dropdown-trigger">
                  <div class="control has-icons-left has-icons-right">
                    <input class="input" type="password" placeholder="Digite a senha" value="" id="senha-vice-representante" />
                    <span class="icon is-small is-left">
                      <i class="fa fa-lock" aria-hidden="true"></i>
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <div class="salvar-btn">
              <button class="button is-success salvar-vice-representante is-pulled-right" name="" id="salvar-experiencia">
                <span class="icon is-small">
                  <i class="fa fa-check" aria-hidden="true"></i>
                </span>
                <span>Salvar</span>
              </button>
            </div>
          </div>
        </section>
        <footer class="modal-card-foot">
          <div></div>
          <div></div>
        </footer>
      </div>
    </div>

    <div class="modal  professor" id="modal-professor">
      <div class="modal-background"></div>
      <div class="modal-card">
        <header class="modal-card-head">
          <p class="modal-card-title">Professor</p>
          <div class="modal-close-btn">
            <i class="fas fa-times sidenav__brand-close"></i>
          </div>
        </header>
        <section class="modal-card-body">
          <div class="info-professor">
            <p class="titulo is-4">Adriano Braga</p>
            <p class="gray-text">adriano.braga@gmail.com</p>
          </div>
          <div class="professor">
            <div class="field">
              <label class="label">E-mail</label>
              <div class="dropdown">
                <div class="dropdown-trigger">
                  <div class="control has-icons-left has-icons-right">
                    <input class="input" type="text" placeholder="Digite seu email" value="" id="email-professor" />
                    <span class="icon is-small is-left">
                      <i class="fa fa-user" aria-hidden="true"></i>
                    </span>
                  </div>
                </div>
              </div>
              <p class="help">Se possível, insira seu e-mail institucional</p>
            </div>
            <div class="field">
              <label class="label">Senha</label>
              <div class="dropdown">
                <div class="dropdown-trigger">
                  <div class="control has-icons-left has-icons-right">
                    <input class="input" type="password" placeholder="Digite a senha" value="" id="senha-professor" />
                    <span class="icon is-small is-left">
                      <i class="fa fa-lock" aria-hidden="true"></i>
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
        <footer class="modal-card-foot">
          <div></div>
          <div></div>
          <button class="button is-success salvar-professor" name="" id="salvar-professor">
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
<script src="../assets/js/permissoes.js" type="module"></script>

</html>