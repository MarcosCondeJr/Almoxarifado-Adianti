<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Database\TRepository;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridAction;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Datagrid\TPageNavigation;
use Adianti\Widget\Dialog\TAlert;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Dialog\TQuestion;
use Adianti\Widget\Form\TButton;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Util\TXMLBreadCrumb;
use Adianti\Wrapper\BootstrapDatagridWrapper;

class GrupoMaterialDatagrid extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;

    // use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);

        //Colunas do datagrid
        $colCdGrupo = new TDataGridColumn('cd_grupomaterial', 'Código', 'center', '20%');
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

        //Formulario de Pesquisas
        $this->form = new TForm('GrupoMaterialSearch');
        $this->form->onsubmit = 'return false';

        $this->form->add($this->datagrid);
        $this->form->style = 'overflow-x:auto';

        //Campos do formulário
        $cdGrupo         = new TEntry('cd_grupomaterial');
        $nmGrupoMaterial = new TEntry('nm_grupomaterial');
        $dsGrupo         = new TEntry('ds_grupomaterial');

        $cdGrupo->setMaxLength(5);
        $nmGrupoMaterial->setMaxLength(40);
        $dsGrupo->setMaxLength(60);

        $cdGrupo->setSize('100%');
        $nmGrupoMaterial->setSize('100%');
        $dsGrupo->setSize('100%');

        $cdGrupo->tabindex = -1;
        $nmGrupoMaterial->tabindex = -1;
        $dsGrupo->tabindex = -1;

        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data'));

        $cdGrupo->setExitAction(new TAction([$this, 'onSearch'], ['static' => 1]));
        $nmGrupoMaterial->setExitAction(new TAction([$this, 'onSearch'], ['static' => 1]));
        $dsGrupo->setExitAction(new TAction([$this, 'onSearch'], ['static' => 1]));        

        $tr = new TElement('tr');
        $this->datagrid->prependRow($tr);

        $tr->add( TElement::tag('td', ''));
        $tr->add( TElement::tag('td', ''));
        $tr->add( TElement::tag('td', $cdGrupo));
        $tr->add( TElement::tag('td', $nmGrupoMaterial));
        $tr->add( TElement::tag('td', $dsGrupo));

        $this->form->addField($cdGrupo);
        $this->form->addField($nmGrupoMaterial);
        $this->form->addField($dsGrupo);

        //Cria a paginação
        $this->pageNavigation = new TPageNavigation();
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->enableCounters();

        //Cria o Painel
        $panel = new TPanelGroup('Listagem Grupo de Material');
        $panel->add($this->datagrid);
        $panel->addFooter($this->pageNavigation);

        //Botão de atualizar a pagina
        $btnAtualizar = $panel->addHeaderActionLink('Atualizar', new TAction([$this, 'onReload']), 'fa:repeat');
        $btnAtualizar->class = 'btn btn-primary me-1';
        
        //Botão para cadastrar um novo registro
        $btnNovo = $panel->addHeaderActionLink('Novo', new TAction(['GrupoMaterialForm', 'onShow']), 'fa:plus');
        $btnNovo->class = 'btn btn-success';

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

    public function onSearch($param)
    {
        $data = $this->form->getData();
        TSession::delValue('filtros');

        $filtros = [];

        TSession::setValue('filtros', null);

        if(isset($data->cd_grupomaterial) && is_numeric($data->cd_grupomaterial) && !empty($data->cd_grupomaterial))
        {
            $filtros[] = new TFilter('cd_material', '=', "{$data->cd_grupomaterial}");
        }

        if(isset($data->nm_grupomaterial) && !empty($data->nm_grupomaterial))
        {
            $filtros[] = new TFilter('unaccent(nm_material)', 'ILIKE', "{$data->nm_grupomaterial}");
        }

        if(isset($data->ds_grupomaterial) && !empty($data->ds_grupomaterial))
        {
            $filtros[] = new TFilter('unaccent(ds_material)', 'ILIKE' , "{$data->ds_grupomaterial}");
        }

        TSession::setValue('filtros', $filtros);
        $this->onReload($param);
    }
}