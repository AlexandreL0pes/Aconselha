<?php


namespace core\sistema;


use DateTime;

class Util
{

    public static function is_JSON($json)
    {
        json_decode($json);
        return (json_last_error() === JSON_ERROR_NONE);
    }
    public static function formataDataBR($data)
    {
        $nova_data = DateTime::createFromFormat("Y-m-d", $data);
        return $nova_data->format('d/m/Y');
    }

    public static function formataDataExtenso($data)
    {
        setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
        date_default_timezone_set('America/Sao_Paulo');
        return strftime('%d de %B de %Y', strtotime($data));
    }

    public static function codigoAlfanumerico()
    {
        $maiuscula = implode('', range('A', 'Z')); // ABCDEFGHIJKLMNOPQRSTUVWXYZ
        $minuscula = implode('', range('a', 'z')); // abcdefghijklmnopqrstuvwxyzy
        $numeros = implode('', range(0, 9)); // 0123456789

        $alfanumerico = $maiuscula . $minuscula . $numeros; // ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789
        $codigo = "";
        $tamanho = 7; // numero de caracteres
        for ($i = 0; $i < $tamanho; $i++) {
            $codigo .= $alfanumerico[rand(0, strlen($alfanumerico) - 1)];
        }

        return $codigo;
    }

    public static function ano($data)
    {
        $dt = explode("-", $data);
        return $dt[0];
    }

    public static function mes($data)
    {
        $dt = explode("-", $data);
        return $dt[1];
    }

    public static function dia($data)
    {
        $dt = explode("-", $data);
        $dt = explode(" ", $dt[2]);
        return $dt[0];
    }

    public static function hora($data)
    {
        $dt = explode("-", $data);
        $dt = explode(" ", $dt[2]);
        $dt = explode(":", $dt[1]);
        return $dt[0];
    }

    public static function min($data)
    {
        $dt = explode("-", $data);
        $dt = explode(" ", $dt[2]);
        $dt = explode(":", $dt[1]);
        return $dt[1];
    }

    public static function seg($data)
    {
        $dt = explode("-", $data);
        $dt = explode(" ", $dt[2]);
        $dt = explode(":", $dt[1]);
        return $dt[2];
    }

    /**
     * Retorna a classificação do coeficiente do estudante
     *
     * @param  mixed $coeficiente
     * @return void
     */
    public static function classificarCoeficiente($coeficiente)
    {

        $classificacao = null;

        // > 8.0 -> Coeficiente Alto
        // >= 6.5 && < 8.0 -> Coeficiente médio
        // < 6.5 -> Coeficiente Baixo
        if ($coeficiente < 6.5) {
            $classificacao =  'baixo';
        } else if ($coeficiente >=  8.5) {
            $classificacao =  'alto';
        } else {
            $classificacao =  'medio';
        }

        return $classificacao;
    }
}
