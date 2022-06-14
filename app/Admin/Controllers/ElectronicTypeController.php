<?php

namespace App\Admin\Controllers;

use App\Models\ElectronicType;
use App\Utility\TimeUtility;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ElectronicTypeController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'ElectronicType';
    function __construct() {
        $this->title = __($this->title);
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ElectronicType());

        $grid->model()->orderBy('sort');

        $grid->column('id', __('Id'));
        $grid->column('TypeName', __('Name'));
        $grid->column('status', __('Status'))->bool();
        $grid->column('sort', __('Sort'));
        $grid->column('created_at', __('Created at'))->display(function ($create){
            return TimeUtility::toDisplyTime($create);
        });
        $grid->column('updated_at', __('Updated at'))->display(function ($create){
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
        $show = new Show(ElectronicType::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('TypeName', __('Name'));
        $show->field('status', __('Status'));
        $show->field('sort', __('Sort'));
        $show->field('created_at', __('Created at'))->display(function ($create){
            return TimeUtility::toDisplyTime($create);
        });
        $show->field('updated_at', __('Updated at'))->display(function ($create){
            return TimeUtility::toDisplyTime($create);
        });

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new ElectronicType());

        $form->text('TypeName', __('Name'));
        $form->switch('status', __('Status'));
        $form->number('sort', __('Sort'));

        return $form;
    }
}
