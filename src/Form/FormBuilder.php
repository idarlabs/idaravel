<?php

namespace Idaravel\Form;

use Illuminate\View\Component;

class FormBuilder extends Component {

    public $action;
    public $method;
    public $form;

    public function __construct($action, array $form, $method = 'POST') {
        $this->action = $action;
        $this->form = $form;
        $this->method = strtoupper($method);
    }

    public function render() {
        return view()->file(__DIR__ . '/form.blade.php');
    }
}