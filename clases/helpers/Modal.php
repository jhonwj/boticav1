<?php
class Modal
{
    public static function render ($type, $args)
    {
        $view = __DIR__ . '/../../views/partials/' . $type . '.php';
        include_once($view);
    }
}
?>
