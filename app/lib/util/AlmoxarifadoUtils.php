<?php

use Adianti\Widget\Form\TForm;

class AlmoxarifadoUtils 
{
    public static function gerarCodigo($classe, $coluna)
    {
        $item = new stdClass();
        $result = $classe::orderBy($coluna, 'desc')->last();

        if(!empty($result))
        {
            $item->$coluna = $classe->$coluna + 1;
        }
        else
        {
            $item->$coluna = 1;
        }
        return $item;
    }
}