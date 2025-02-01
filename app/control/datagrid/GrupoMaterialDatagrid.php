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
use Adianti\Widget\Wrapper\TQuickForm;
use Adianti\Wrapper\BootstrapDatagridWrapper;
use Adianti\Wrapper\BootstrapFormBuilder;
use Adianti\Wrapper\BootstrapFormWrapper;
use Symfony\Component\VarDumper\Cloner\DumperInterface;

class GrupoMaterialDatagrid extends TPage
{
    private $form_datagrid;
    private $form;
    private $panel;
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

        $colDsGrupo->setTransformer(function ($value, $object)
        {
            return LimitarDescricao::transformer($value, $object);
        });

        //Adiciona as colunas no datadrid
        $this->datagrid->addColumn($colCdGrupo);
        $this->datagrid->addColumn($colNmGrupo);
        $this->datagrid->addColumn($colDsGrupo);

        //Ações do datagrid
        $btnEditar = new TDataGridAction(['GrupoMaterialForm', 'onEdit'], ['id_grupomaterial' => '{id_grupomaterial}', 'register_state' => 'false']);
        $btnExcluir = new TDataGridAction([$this, 'onDelete'], ['id_grupomaterial' => '{id_grupomaterial}', 'nm_grupomaterial' => '{nm_grupomaterial}']);

        $this->datagrid->addAction($btnEditar, 'Editar', 'far:edit blue');
        $this->datagrid->addAction($btnExcluir, 'Deletar', 'far:trash-alt red');

        $this->datagrid->createModel();

        //Formulario de Pesquisas
        $this->form = new TForm('GrupoMaterialForm');
        $this->form->add($this->datagrid);

        //Campos do formulário
        $cdGrupo         = new TEntry('cd_grupomaterial_datagrid');
        $nmGrupoMaterial = new TEntry('nm_grupomaterial_datagrid');
        $dsGrupo         = new TEntry('ds_grupomaterial_datagrid');

        $cdGrupo->exitOnEnter();
        $nmGrupoMaterial->exitOnEnter();
        $dsGrupo->exitOnEnter();
        
        $cdGrupo->setMask('99999');
        $nmGrupoMaterial->setMaxLength(40);
        $dsGrupo->setMaxLength(60);

        $cdGrupo->setSize('100%');
        $nmGrupoMaterial->setSize('100%');
        $dsGrupo->setSize('100%');

        $this->form->setData( TSession::getValue('GrupoMaterialDatagrid_filter_data'));

        $btnSearch = new TButton('search');
        $btnSearch->setImage('fa:search');
        $btnSearch->setAction(new TAction([$this, 'onSearch']));
        $btnSearch->class = 'btn btn-primary';

        $tr = new TElement('tr');
        $this->datagrid->prependRow($tr);

        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', ''));
        $tr->add(TElement::tag('td', $cdGrupo));
        $tr->add(TElement::tag('td', $nmGrupoMaterial));
        $tr->add(TElement::tag('td', $dsGrupo));
        $tr->add( TElement::tag('td', $btnSearch));

        $this->form->addField($cdGrupo);
        $this->form->addField($nmGrupoMaterial);
        $this->form->addField($dsGrupo);
        $this->form->addField($btnSearch);

        //Cria a paginação
        $this->pageNavigation = new TPageNavigation();
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->enableCounters();

        $this->datagrid->add($this->form);

        //Cria o Painel
        $this->panel = new TPanelGroup('Listagem Grupo de Material');
        $this->panel->addFooter($this->pageNavigation);
        $this->panel->add($this->form);
        // $this->panel->add($this->datagrid);

        //Botão de atualizar a pagina
        $btnAtualizar = $this->panel->addHeaderActionLink('Atualizar', new TAction([$this, 'onReload']), 'fa:repeat');
        $btnAtualizar->class = 'btn btn-primary me-1 order-1';

        //Botão para cadastrar um novo registro
        $btnNovo = $this->panel->addHeaderActionLink('Novo', new TAction(['GrupoMaterialForm', 'onShow']), 'fa:plus');
        $btnNovo->class = 'btn btn-success order-2';


        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->panel);

        parent::add($vbox);
    }

    public function onReload($param = null)
    {
        try 
        {
            TTransaction::open('conexao');
            $data = $this->form->getData();

            //Istancia o Repositorio (model)
            $repository = new TRepository('GrupoMaterial');
            $limit = 10;

            //istancia o criterio
            $criterio = new TCriteria;

            if (empty($param['order'])) 
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
            
            if ($grupoMaterial) 
            {
                foreach ($grupoMaterial as $grupos) 
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
            $this->form->setData($data);

            TTransaction::close();
            $this->loaded = true;
        } 
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onDelete($param)
    {
        new TQuestion('Tem certeza que Deseja excluir o Grupo ' . $param['nm_grupomaterial'] . '?', new TAction(
            [$this, 'delete'],
            ['key' => $param['key'], 'name' => $param['nm_grupomaterial']]
        ));
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
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
        $this->onReload();
    }

    public function onSearch($param)
    {
        //butão de limpar o form
        $btnLimpar = $this->panel->addHeaderActionLink('Limpar', new TAction([$this, 'onClear']), 'fa:eraser red');
        $btnLimpar->class = 'btn btn-outline-secondary tbutton_limpar me-1';

        $data = $this->form->getData();
        TSession::delValue('filtros');
        $filtros = [];

        TSession::setValue('filtros', null);

        if (isset($data->cd_grupomaterial_datagrid) && is_numeric($data->cd_grupomaterial_datagrid) && !empty($data->cd_grupomaterial_datagrid)) 
        {
            $filtros[] = new TFilter('cd_grupomaterial', '=', "{$data->cd_grupomaterial_datagrid}");
        }

        if (isset($data->nm_grupomaterial_datagrid) && !empty($data->nm_grupomaterial_datagrid)) 
        {
            $filtros[] = new TFilter('unaccent(nm_grupomaterial)', 'ILIKE', "%{$data->nm_grupomaterial_datagrid}%");
        }

        if (isset($data->ds_grupomaterial_datagrid) && !empty($data->ds_grupomaterial_datagrid)) 
        {
            $filtros[] = new TFilter('unaccent(ds_grupomaterial)', 'ILIKE', "%{$data->ds_grupomaterial_datagrid}%");
        }

        TSession::setValue('filtros', $filtros);
        $this->onReload($param);
    }

    public function onClear()
    {
        $this->form->clear();
        $this->onReload();
    }
}
