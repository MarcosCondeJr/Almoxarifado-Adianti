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
            $btnEditar->class = 'btn btn-outline-primary me-1 tbutton_editar';

            $btnCancelar = $form->addAction('Cancelar', new TAction([$classe, 'onClose']), 'fa:ban');
            $btnCancelar->class = 'btn btn-danger tbutton_cancelar';
        }
        else
        {
            $btnClose = $form->addHeaderAction('Fechar', new TAction([$classe, 'onClose']), 'fa: fa-times');
            $btnClose->class = 'btn btn-outline-danger order-2';
            
            //Botão de Salvar
            $btnSave = $form->addAction('Salvar', new TAction([$classe, 'onSave']), 'fa:save');
            $btnSave->class = 'btn btn-success tbutton_salvar';

            $btnLimpar = $form->addAction('Limpar', new TAction([$classe, 'onClear']), 'fa:eraser red');
            $btnLimpar->class = 'btn btn-outline-secondary tbutton_limpar';
        }
    }
}