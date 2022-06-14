<?php

namespace App\Admin\Controllers;

use App\Models\SupplyManagement;
use App\Utility\TimeUtility;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class SupplyManagementController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'SupplyManagement';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new SupplyManagement());

        $grid->column('id', __('Id'));
        $grid->column('supply_name', __('Supply name'));
        $grid->column('status', __('Status'));
        $grid->column('memo', __('Memo'));
        $grid->column('created_at', __('Created at'))->display(function($create) {
            return TimeUtility::toDisplyTime($create);
        });
        $grid->column('updated_at', __('Updated at'))->display(function($create) {
            return TimeUtility::toDisplyTime($create);
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(SupplyManagement::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('supply_name', __('Supply name'));
        $show->field('status', __('Status'));
        $show->field('memo', __('Memo'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new SupplyManagement());

        $form->text('supply_name', __('Supply name'));
        $form->switch('status', __('Status'));
        $form->textarea('memo', __('Memo'));

        return $form;
    }
}
