<?php

use Adianti\Database\TRecord;

class GrupoMaterial extends TRecord
{
    const TABLENAME = 'grupo_material';
    const PRIMARYKEY = 'id_grupomaterial';
    const IDPOLICY = 'serial';

    public function __construct($id_grupomaterial = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id_grupomaterial, $callObjectLoad);
        parent::addAttribute('cd_grupomaterial');
        parent::addAttribute('nm_grupomaterial');
        parent::addAttribute('ds_grupomaterial');
    }
}