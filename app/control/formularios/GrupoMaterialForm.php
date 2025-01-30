<?php

use Adianti\Control\TPage;
use Adianti\Database\TTransaction;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Dialog\TToast;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\TText;
use Adianti\Wrapper\BootstrapFormBuilder;

class GrupoMaterialForm extends TPage
{
    private $form;
    private $service;

    public function __construct()
    {
        parent::__construct();
        parent::setTargetContainer('adianti_right_panel');

        $this->form = new BootstrapFormBuilder('GrupoMaterialForm');
        $this->form->setFormTitle('Grupo de Materiais');

        $cdGrupo = new TEntry('cd_grupomaterial');
        $nmGrupo   = new TEntry('nm_grupomaterial');
        $dsGrupo   = new TText('ds_grupomaterial');

        $cdGrupo->setSize('100%');
        $nmGrupo->setSize('100%');
        $dsGrupo->setSize('100%');

        $cdGrupo->setEditable(false);

        $row1 = $this->form->addFields(
            [new TLabel('Código (*)', 'red'), $cdGrupo],
            [new TLabel('Nome (*)', 'red'), $nmGrupo]
        );

        $row2 = $this->form->addFields([new TLabel('Descrição'), $dsGrupo]);

        $row1->layout = ['col-sm-2', 'col-sm-10'];
        $row2->layout = ['col-sm-12'];

        $nmGrupo->addValidation('Nome', new TRequiredValidator);

        AlmoxarifadoUtils::tiposDeBotao('onShow', $this->form, 'GrupoMaterialForm');

        parent::add($this->form);
    }

    public function onSave($param)
    {
        try {
            TTransaction::open('conexao');
            $data = $this->form->getData();
            $this->form->validate();

            $this->service = new GrupoMaterialService();
            $this->service->onSave($data);
            
            TApplication::loadPage('GrupoMaterialDatagrid', 'onReload');
            TToast::show('success', 'Cadastrado com Sucesso ', 'top right', 'far:check-circle');
            TTransaction::close();
        } 
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData($data);
            TTransaction::rollback();
        }
    }

    public function onEdit($param = null)
    {
        try 
        {
            // dump($param);
            TTransaction::open('conexao');
            $key = $param['key'];

            AlmoxarifadoUtils::tiposDeBotao('onEdit', $this->form, 'GrupoMaterialForm');
            self::habilitarCampos(['enable' => 0]);

            if (!empty($key)) 
            {
                
                $grupoMaterial = new GrupoMaterial($key);
                W5iSessao::incluirObjetoEdicaoSessao($grupoMaterial, $key, 'id_grupomaterial',__CLASS__);

                if ($grupoMaterial) 
                {
                    $this->form->setData($grupoMaterial);
                }
            }
            TTransaction::close();
        } 
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onCLose()
    {
        TScript::create("Template.closeRightPanel()");
        W5iSessao::removerObjetoEdicaoSessao(__CLASS__);
    }


    public function onShow()
    {
        TTransaction::open('conexao');

        $item = AlmoxarifadoUtils::gerarCodigo('GrupoMaterial', 'cd_grupomaterial');
        TForm::sendData('GrupoMaterialForm', $item, false, false);

        W5iSessao::removerObjetoEdicaoSessao(__CLASS__);
        TTransaction::close();
    }

    public function habilitarCampos($param)
    {
        if ($param['enable'] == 1) 
        {
            $data = $this->form->getData();
            TEntry::enableField('GrupoMaterialForm', 'nm_grupomaterial');
            TText::enableField('GrupoMaterialForm', 'ds_grupomaterial');

            //Desabilita butão
            TScript::create("document.querySelector('.tbutton_editar').style.display = 'none';");
            TForm::sendData('GrupoMaterialForm', $data);
        } 
        else 
        {
            //Desabilita o botão de salvar
            TScript::create("document.querySelector('.btn-success').style.display = 'none';");
            TScript::create("document.querySelector('.btn-outline-secondary').style.display = 'none';");

            TEntry::disableField('GrupoMaterialForm', 'nm_grupomaterial');
            TText::disableField('GrupoMaterialForm', 'ds_grupomaterial');
        }
    }
}
