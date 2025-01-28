<?php

class GrupoMaterialService
{
    public function onSave($data)
    {
        if(empty(trim($data->nm_grupomaterial)))
        {
            throw new Exception('O campo Nome é obrigatório');
        }

        $grupoMaterial = new GrupoMaterial();
        $grupoMaterial->fromArray((array)$data);
        W5iSessao::obterObjetoEdicaoSessao($grupoMaterial, 'id_grupomaterial', null, __CLASS__);
        $grupoMaterial->store();
        W5iSessao::removerObjetoEdicaoSessao(__CLASS__);
    }
}