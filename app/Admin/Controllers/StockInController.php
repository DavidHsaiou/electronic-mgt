<?php

namespace App\Admin\Controllers;

use App\Models\StockInRecord;
use App\Utility\TimeUtility;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;

class StockInController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'StockInRecord';

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
        $grid = new Grid(new StockInRecord());
        $totalPrice = 0;
        $grid->column('id', __('Id'))->expand(function ($model) use(&$totalPrice){
            $details = $model->details()
                ->get()
                ->map(function ($detail) use(&$totalPrice) {
                    $detail->singlePrice = $detail->original_price * $detail->mainRecord()->first()->price_coefficient;
                    $detail->totalPrice = $detail->singlePrice * $detail->count;
                    $totalPrice += $detail->totalPrice;
                    $detail->electronic_name = $detail->useElectronic()->first()->name;
                    return $detail->only(['electronic_name', 'original_price', 'singlePrice', 'count', 'totalPrice']);
                });
            return new Table([__('electronic_name'), __('original_price'), __('singlePrice'), __('Count'), __('totalPrice')], $details->toArray());
        });
        $grid->column('price_coefficient', __('price_coefficient'));
        $grid->column('totalPrice',__('totalPrice'));
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
        $show = new Show(StockInRecord::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('price_coefficient', __('price_coefficient'));
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
        $form = new Form(new StockInRecord());

        $form->decimal('price_coefficient', __('price_coefficient'));

        $form->hasMany('details', __('StockInRecordDetail'), function (Form\NestedForm $form) {
//            $form->text('record_id');
            $form->text('electric_id');
            $form->decimal('original_price', __('original_price'));
            $form->number('count', __('Count'));
        });

        return $form;
    }
}
