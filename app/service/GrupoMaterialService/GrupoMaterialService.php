<?php

class GrupoMaterialService
{
    public function onSave($data)
    {
        $grupoMaterial = new GrupoMaterial();
        $grupoMaterial->fromArray((array)$data);

        $grupoMaterial->store();
    }
}