<?php

use Adianti\Database\TRepository;
use Adianti\Database\TTransaction;

class GrupoMaterialService
{
    public function onSave($data)
    {
        $grupoMaterial = new GrupoMaterial();
        $grupoMaterial->fromArray((array)$data);

        W5iSessao::obterObjetoEdicaoSessao($grupoMaterial, 'id_grupomaterial', null, 'GrupoMaterialForm');
      
        $grupoMaterial->store();

        W5iSessao::removerObjetoEdicaoSessao('GrupoMaterialForm');
    }

    public function onDelete($param)
    {
        $repository = new TRepository('Material');
    }
}