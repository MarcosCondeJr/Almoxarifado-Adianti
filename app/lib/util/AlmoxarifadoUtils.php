<?php

use Adianti\Control\TAction;
use Adianti\Registry\TSession;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Form\TForm;

class AlmoxarifadoUtils 
{
    public static function gerarCodigo($classe, $coluna)
    {
        $item = new stdClass();
        $result = $classe::orderBy($coluna, 'desc')->last();

        if(!empty($result))
        {
            $item->$coluna = $result->$coluna + 1;
        }
        else
        {
            $item->$coluna = 1;
        }
        return $item;
    }

    public static function tiposDeBotao($param, $form, $classe)
    {
        if($param == 'onEdit')
        {
            $btnEditar = $form->addHeaderAction('Editar', new TAction([$classe, 'habilitarCampos'], ['enable' => 1]), 'fas:far fa-edit');
            $btnEditar->addStyleClass('tbutton_editar');
            $btnEditar->class = 'btn btn-outline-primary';

            // Adiciona flex para a exibição
            TScript::create("document.querySelector('.tbutton_editar').style.display = 'flex';");
            TScript::create("document.querySelector('.tbutton_editar').style.justifyContent = 'flex-start';");
            TScript::create("document.querySelector('.tbutton_editar').style.marginRight = '10px';");  // Adicionando margem para o espaço
        }
        else
        {
            $btnClose = $form->addHeaderAction('Fechar', new TAction([$classe, 'onClose']), 'fa: fa-times');
            $btnClose->class = 'btn btn-outline-danger';
            TScript::create("document.querySelector('.btn-outline-danger').style.marginLeft = 'auto';");  // Empurra o botão para a direita
            
            //Botão de Salvar
            $btnSave = $form->addAction('Salvar', new TAction([$classe, 'onSave']), 'fa:save');
            $btnSave->class = 'btn btn-success';
        }
    }
}