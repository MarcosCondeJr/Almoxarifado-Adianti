<?php

use Adianti\Database\TRecord;

class GrupoMaterial extends TRecord
{
    const Tablename = 'grupomaterial';
    const PrimaryKey = 'cd_grupomaterial';
    const IDPOLICY = 'serial';

    public function __construct($cd_grupomaterial = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($cd_grupomaterial, $callObjectLoad);
        parent::addAttribute('nm_grupomaterial');
    }
}