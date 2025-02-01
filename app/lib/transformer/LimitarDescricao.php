<?php

class LimitarDescricao
{
    public static function transformer($value, $object, $cell = null, $last_row = null)
    {
        if (isset($value))
        {
            if (strlen($value) >= 80)
            {
                return substr($value, 0, 80) . "...";
            }
            else
            {
                return $value;
            }
        }
    }
}