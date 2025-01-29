<?php

use Adianti\Database\TRecord;

class UnidadeDeMedida extends TRecord
{
    const TABLENAME = 'unidade_de_medida';
    const PRIMARYKEY = 'id_unidademedida';
    const IDPOLICY = 'serial';

    public function __construct($id_unidademedida = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id_unidademedida, $callObjectLoad);
        parent::addAttribute('nm_unidademedida');
    }
}