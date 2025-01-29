<?php

use Adianti\Database\TRecord;

class Material extends TRecord
{
    const TABLENAME = 'material';
    const PRIMARYKEY = 'id_material';
    const IDPOLICY = 'serial';

    private $unidadeMedida;
    private $grupoMaterial;

    public function __construct($id_material = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id_material, $callObjectLoad);
        parent::addAttribute('cd_material');
        parent::addAttribute('nm_material');
        parent::addAttribute('id_unidade_medida');
        parent::addAttribute('id_grupomaterial');
        parent::addAttribute('qtd_estoque');
        parent::addAttribute('vl_medio');
    }

    public function get_unidademedida()
    {
        if(empty($this->unidadeMedida))
        {
            $this->unidadeMedida = new UnidadeDeMedida($this->id_unidademedida);
        }
        return $this->unidadeMedida;
    }

    public function get_grupomaterial()
    {
        if(empty($this->grupoMaterial))
        {
            $this->grupoMaterial = new UnidadeDeMedida($this->id_grupomaterial);
        }
        return $this->grupoMaterial;
    }
}