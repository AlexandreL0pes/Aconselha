<?php

namespace {

    define("ROOT", __DIR__ . '/');

    // Porta para o caminho
    $port = ($_SERVER['SERVER_PORT'] != 80 && $_SERVER['SERVER_PORT'] != 443) ? ":" . $_SERVER['SERVER_PORT'] : "";

    // Trata Caminho
    $dir = explode($_SERVER['DOCUMENT_ROOT'], str_replace("\\", "/", __DIR__));
    $base = '//' . str_replace("//", "/", $_SERVER['SERVER_NAME'] . $port . '/' . $dir[1] . "/");

    define("BASE", $base);

    define("URL", "//" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);

    $path_cookie = ROOT;
    // Verificação para a criação dos Cookies
    if (strpos($path_cookie, '\\') !== false) {
        // O path no Windows vem com a \
        $path_cookie = explode("\\", ROOT);
        $path_cookie = $path_cookie[count($path_cookie) - 1];
        $path_cookie = "/" . $path_cookie . "public_html/";
    } else {
        // O path do linux vem com a /
        $path_cookie = explode("/", ROOT);
        $path_cookie = $path_cookie[count($path_cookie) - 2];
        $path_cookie = "/" . $path_cookie . "/";
    }

    define("PATH_COOKIE", $path_cookie);

    // Define a codificação
    mb_internal_encoding("UTF-8");
}

