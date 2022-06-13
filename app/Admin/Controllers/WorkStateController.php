<?php

namespace App\Admin\Controllers;

use App\Models\WorkState;
use App\Utility\TimeUtility;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class WorkStateController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'WorkState';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new WorkState());

        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('status', __('Status'))->bool();
        $grid->column('created_at', __('Created at'))->display(function ($time){
            return TimeUtility::toDisplyTime($time);
        });
        $grid->column('updated_at', __('Updated at'))->display(function ($time){
            return TimeUtility::toDisplyTime($time);
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
        $show = new Show(WorkState::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('status', __('Status'));
        $show->field('created_at', __('Created at'))->display(function ($time){
            return TimeUtility::toDisplyTime($time);
        });
        $show->field('updated_at', __('Updated at'))->display(function ($time){
            return TimeUtility::toDisplyTime($time);
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
        $form = new Form(new WorkState());

        $form->text('name', __('Name'));
        $form->switch('status', __('Status'));

        return $form;
    }
}
