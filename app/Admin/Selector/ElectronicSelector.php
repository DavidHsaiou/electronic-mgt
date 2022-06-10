<?php

namespace App\Admin\Selector;

use App\Models\eletronic;
use Encore\Admin\Grid\Selectable;

class ElectronicSelector extends Selectable
{
    public $model = eletronic::class;

    public function make()
    {
        $this->column('id');
        $this->column('name');

        $this->filter(function ($filter) {
            $filter->like('name');
        });
    }
}
