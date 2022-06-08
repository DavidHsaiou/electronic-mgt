<?php

namespace App\Admin\Controllers;

use App\Models\eletronic;
use App\Models\StorageArea;
use App\Utility\TimeUtility;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ElectronicController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'electronic';

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
        $grid = new Grid(new eletronic());

        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('count', __('Count'));
        $grid->StorageArea()->display(function ($storages){
            $returnData = array_map(function ($storage) {
                return $storage['name'];
            }, $storages);
            return $returnData;
        })->label();
        $grid->column('created_at', __('Created at'))->display(function ($create){
            return TimeUtility::toDisplyTime($create);
        });
        $grid->column('updated_at', __('Updated at'))->display(function ($update){
            return TimeUtility::toDisplyTime($update);
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
        $show = new Show(eletronic::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('count', __('Count'));
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
        $form = new Form(new eletronic());

        $form->text('name', __('Name'));
        $form
            ->multipleSelect('StorageArea', __('store location'))
            ->options(StorageArea::where('status', 1)->pluck('name', 'id'));

        return $form;
    }
}
