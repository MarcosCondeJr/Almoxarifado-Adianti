<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Database\TCriteria;
use Adianti\Database\TRepository;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridAction;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Datagrid\TPageNavigation;
use Adianti\Widget\Dialog\TAlert;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Dialog\TQuestion;
use Adianti\Widget\Util\TXMLBreadCrumb;
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

        $colCdGrupo->setAction(new TAction([$this, 'onReload']), ['order' => 'cd_grupomaterial']);
        $colNmGrupo->setAction(new TAction([$this, 'onReload']), ['order' => 'nm_grupomaterial']);
        $colDsGrupo->setAction(new TAction([$this, 'onReload']), ['order' => 'ds_grupomaterial']);

        //Adiciona as colunas no datadrid
        $this->datagrid->addColumn($colCdGrupo);
        $this->datagrid->addColumn($colNmGrupo);
        $this->datagrid->addColumn($colDsGrupo);

        //Ações do datagrid
        $btnEditar = new TDataGridAction(['GrupoMaterialForm', 'onEdit'], ['id_grupomaterial'=>'{id_grupomaterial}', 'register_state' => 'false']);
        $btnExcluir = new TDataGridAction([$this, 'onDelete'], ['id_grupomaterial'=>'{id_grupomaterial}', 'nm_grupomaterial' => '{nm_grupomaterial}']);

        $this->datagrid->addAction($btnEditar, 'Editar', 'far:edit blue');
        $this->datagrid->addAction($btnExcluir ,'Deletar', 'far:trash-alt red');

        $this->datagrid->createModel();

        //Cria a paginação
        $this->pageNavigation = new TPageNavigation();
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->enableCounters();

        //Cria o Painel
        $panel = new TPanelGroup('Listagem Grupo de Material');
        $panel->add($this->datagrid);
        $panel->addFooter($this->pageNavigation);

        //Botão para cadastrar um novo registro
        $btnNovo = $panel->addHeaderActionLink('Novo', new TAction(['GrupoMaterialForm', 'onShow']), 'fa:plus');
        $btnNovo->class = 'btn btn-primary';

        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($panel);

        parent::add($vbox);
    }

    public function onReload($param = null)
    {
        try
        {
            TTransaction::open('conexao');

            //Istancia o Repositorio (model)
            $repository = new TRepository('GrupoMaterial');
            $limit = 10;

            //istancia o criterio
            $criterio = new TCriteria;

            if(empty($param['order']))
            {
                $param['order'] = 'id_grupomaterial';
            }

            $criterio->setProperties($param);
            $criterio->setProperties('limit', $limit);

            if(TSession::getValue('filtros'))
            {
                foreach(TSession::getValue('filtros') as $item)
                {
                    $criterio->add($item);
                }
            }

            $grupoMaterial = $repository->load($criterio);
            $this->datagrid->clear();

            if($grupoMaterial)
            {
                foreach($grupoMaterial as $grupos)
                {
                    $this->datagrid->addItem($grupos);
                }
            }

            $criterio->resetProperties();
            TSession::delValue('filtros');
            $count = $repository->count($criterio);

            $this->pageNavigation->setCount($count);
            $this->pageNavigation->setProperties($param);
            $this->pageNavigation->setLimit($limit);

            TTransaction::close();
            $this->loaded = true;
        }
        catch(Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onDelete($param)
    {
        new TQuestion('Tem certeza que Deseja excluir o Grupo ' . $param['nm_grupomaterial'] . '?', new TAction([$this, 'delete'], 
        ['key' => $param['key'], 'name' => $param['nm_grupomaterial']]));
        $this->onReload();
    }

    public function delete($param)
    {
        try
        {
            TTransaction::open('conexao');

            $key = $param['key'];
            $name = $param['name'];

            $service = new GrupoMaterialService();
            $service->onDelete($key, $name);

            TTransaction::close();
        }
        catch(Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
        $this->onReload();
    }
}