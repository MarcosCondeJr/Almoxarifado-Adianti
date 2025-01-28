<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Control\TWindow;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\TText;
use Adianti\Wrapper\BootstrapFormBuilder;

class GrupoMaterialForm extends TPage
{
    private $form;

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

        $row1 = $this->form->addFields([new TLabel('Código (*)', 'red'), $cdGrupo],
                                        [new TLabel('Nome (*)', 'red'), $nmGrupo]);
        $row2 = $this->form->addFields([new TLabel('Descrição'), $dsGrupo]);

        $row1->layout = ['col-sm-4', 'col-sm-8'];
        $row2->layout = ['col-sm-12'];

        //Butão de Fechar a pagina
        $btnClose = $this->form->addHeaderAction('Fechar', new TAction([$this, 'onCLose']), 'fa: fa-times');
        $btnClose->class = 'btn btn-outline-danger';

        //Botão de Salvar
        $btnSave = $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save');
        $btnSave->class = 'btn btn-success';

        parent::add($this->form);
    }

    public function onSave()
    {

    }

    public function onCLose()
    {
        TScript::create("Template.closeRightPanel()");
    }
}
