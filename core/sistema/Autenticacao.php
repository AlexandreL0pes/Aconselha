<?php


namespace core\sistema;

use core\model\Usuario;


class Autenticacao {
    const COOKIE_USUARIO = "usuario";
    const COOKIE_ACESSO = "acesso";

    const GERENTE = 1;
    const COORDENADOR = 2;
    const PROFESSOR = 3;
    const REPRESENTANTE = 4;
    
}