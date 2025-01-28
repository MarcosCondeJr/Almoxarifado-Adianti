<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridAction;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Datagrid\TPageNavigation;
use Adianti\Wrapper\BootstrapDatagridWrapper;

class GrupoMaterialDatagrid extends TPage
{
    private $datagrid;
    private $pageNavigation;

    public function __construct()
    {
        parent::__construct();
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);

        //Colunas do datagrid
        $colCdGrupo = new TDataGridColumn('cd_grupomaterial', 'Código', 'center', '30%');
        $colNmGrupo = new TDataGridColumn('nm_grupomaterial', 'Nome', 'left', '50%');
        $colDsGrupo = new TDataGridColumn('ds_grupomaterial', 'Descrição', 'left', '30%');

        //Adiciona as colunas no datadrid
        $this->datagrid->addColumn($colCdGrupo);
        $this->datagrid->addColumn($colNmGrupo);
        $this->datagrid->addColumn($colDsGrupo);

        //Ações do datagrid
        $btnEditar = new TDataGridAction(['GrupoMaterialForm', 'onEdit'], ['id'=>'{id}', 'register_state' => 'false']);
        $btnExcluir = new TDataGridAction([$this, 'onDelete'], ['id'=>'{id}']);

        $this->datagrid->addAction($btnEditar, 'Edit', 'far:edit blue');
        $this->datagrid->addAction($btnExcluir ,'Delete', 'far:trash-alt red');

        $this->datagrid->createModel();

        $this->pageNavigation = new TPageNavigation();
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->enableCounters();

        $panel = new TPanelGroup('Listagem Grupo de Material');
        $panel->add($this->datagrid);
        $panel->addFooter($this->pageNavigation);

        $btnNovo = $panel->addHeaderActionLink('Novo', new TAction(['GrupoMaterialForm', 'onShow']), 'fa:plus');
        $btnNovo->class = 'btn btn-primary';

        parent::add($panel);
    }

    public function onReload()
    {
    
    }

    public function onDelete()
    {

    }
}