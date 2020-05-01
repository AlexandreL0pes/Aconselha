<?php

namespace core\sistema;

use core\sistema\Util;

class Requisicao  {

    /**
     * Pega os dados enviados por uma requisição
     * 
     * @param bool $acao
     * @return array|mixed
     */
    public static function get($acao = false) {
        $request = ['acao' => null, "dados" => []];

        foreach ($_POST as $index => $valor) {
            if ($acao && $index == "acao") {
                $request['acao'] = $_POST['acao'];  
            }else{
                $request['dados'][$index] = $valor;
            }
        }

        foreach ($_GET as $index => $valor) {
            if ($acao && $index == 'acao') {
                $request['acao'] = $_GET['acao'];
            } else {
                $request['dados'][$index] = $valor;
            }
        }

        foreach ($_FILES as $index => $valor) {
            $request['dados'][$index] = $valor;
        }

        $json = file_get_contents('php://input');



        $json_decoded = (Util::is_JSON($json) && strlen($json) > 0) ? json_decode($json, true) : [];
        // $json = json_decode($json, true);

        foreach ($json_decoded as $index => $valor) {
            if ($acao && $index == "acao") {
                $request['acao'] = $json_decoded['acao'];  
            }else{
                $request['dados'][$index] = $valor;
            }
        }

        if (!$acao) {
            $request = $request['dados'];
        }

        return $request;
    }
}
