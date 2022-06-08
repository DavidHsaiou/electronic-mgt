<?php

namespace App\Admin\Controllers;

use App\Models\StorageArea;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class StorageAreaController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '存放區管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new StorageArea());

        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('status', __('Status'))->bool();
        $grid->column('created_at', __('Created at'))->datetime();
        $grid->column('updated_at', __('Updated at'))->datetime();

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
        $show = new Show(StorageArea::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('status', __('Status'));
//        $show->field('created_at', __('Created at'))->datetime();
//        $show->field('updated_at', __('Updated at'))->datetime();

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new StorageArea());

        $form->text('name', __('Name'));
        $form->switch('status', __('Status'));

        return $form;
    }
}
