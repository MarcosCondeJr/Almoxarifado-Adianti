<?php

use Adianti\Control\TPage;
use Adianti\Control\TWindow;
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

        parent::add($this->form);
    }
}
