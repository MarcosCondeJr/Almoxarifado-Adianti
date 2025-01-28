<?php

use Adianti\Control\TPage;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Wrapper\BootstrapDatagridWrapper;

class GrupoMaterialDatagrid extends TPage
{
    private $datagrid;
    private $pageNavigation;

    public function __construct()
    {
        parent::__construct();
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);

        $colCdGrupo = new TDataGridColumn('cd_grupomaterial', 'Código', 'center', '30%');
        $colNmGrupo = new TDataGridColumn('nm_grupomaterial', 'Nome', 'left', '50%');
        $colDsGrupo = new TDataGridColumn('ds_grupomaterial', 'Descrição', 'left', '30%');

        parent::add($this->datagrid);
    }
}