<?php

use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Database\TRepository;
use Adianti\Database\TTransaction;

class GrupoMaterialService
{
    public function onSave($data)
    {
        $grupoMaterial = new GrupoMaterial();
        $grupoMaterial->fromArray((array)$data);

        // $codigo = GrupoMaterial::where('cd_grupomaterial', '-', $data->cd_grupomaterial)->last();

        var_dump($data->id_grupomaterial);
        
        // if($grupoMaterial->cd_grupomate)

        W5iSessao::obterObjetoEdicaoSessao($grupoMaterial, 'id_grupomaterial', null, 'GrupoMaterialForm');

        // $grupoMaterial->store();

        W5iSessao::removerObjetoEdicaoSessao('GrupoMaterialForm');
    }

    public function onDelete($key, $name)
    {
        //Pega uma dependencia se existir
        $dependencia = Material::where('id_grupomaterial', '=', $key)->last();

        if(!empty($dependencia))
        {
            throw new Exception("Não é possivelo deletar o grupo " . $name . " pois ele está vinculado a um Material!");
        }

        $grupoMaterial = new GrupoMaterial($key);
        $grupoMaterial->delete();
    }
}