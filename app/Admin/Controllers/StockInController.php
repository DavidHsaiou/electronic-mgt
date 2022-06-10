<?php

namespace App\Admin\Controllers;

use App\Models\eletronic;
use App\Models\StockInRecord;
use App\Utility\TimeUtility;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;
use App\Admin\Selector\ElectronicSelector;
use Illuminate\Support\Facades\Log;

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
        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
        });

        $grid->model()->orderBy('id', 'desc');

        $grid->column('id', __('Id'))->expand(function ($model){
            $details = $model->details()
                ->get()
                ->map(function ($detail){
                    $detail->singlePrice = $detail->original_price * $detail->mainRecord()->first()->price_coefficient;
                    $detail->totalPrice = $detail->singlePrice * $detail->count;
                    $detail->electronic_name = $detail->useElectronic()->first()->name;
                    return $detail->only([
                        'electronic_name',
                        'original_price',
                        'singlePrice',
                        'count',
                        'totalPrice']);
                });
            return new Table([
                __('electronic_name'),
                __('original_price'),
                __('singlePrice'),
                __('Count'),
                __('totalPrice')],
                $details->toArray());
        });
        $grid->column('price_coefficient', __('price_coefficient'));
        $grid->column('details',__('totalPrice'))->display(function ($details) use(&$price_coefficient) {
            $eachPrice = 0;
            $price_coefficient = 0;
            $main = StockInRecord::find($details[0]['record_id']);
            foreach($details as $detail) {
                $eachPrice += $detail['original_price'] * $main->price_coefficient * $detail['count'];
            }
            return $eachPrice;
        });
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
        $form->decimal('price_coefficient', __('price_coefficient'))->required();

        $form->hasMany('details', __('StockInRecordDetail'), function (Form\NestedForm $form) {
            $form->select('electric_id', __('electronic_name'))
                ->options(eletronic::all()->pluck('name', 'id'))->required();
            $form->decimal('original_price', __('original_price'))->required();
            $form->number('count', __('Count'))->rules(['required','gt:0'], [
                'gt' => [
                    'numeric' =>
                        ':attribute 必須大於 :value.'
                ]
            ]);
        });

        if ($form->isEditing()) {
            $form->saving(function (Form $form) {
                Log::notice('edit started');
//                $oldDetails = $form->model()->details()->get();
//                $newDetails = array_map(function ($detail) {
//                    return $detail;
//                },$form->details);
//                Log::notice('datas', [$newDetails]);
//                // when not enough count popup
//                // old record and not in new record, minus all
//                foreach ($oldDetails as $oldDetail) {
//
//                }
//                // new record and not exist in old record, just add
////                foreach ($)
//                // old and new record exist, compare count and add/minus count
            });
        }

        if ($form->isCreating()) {
            $form->saved(function (Form $form) {
                $details = $form->model()->details()->get();
                foreach ($details as $detail) {
                    $electronic = $detail->useElectronic();
                    $electronic->increment('count', $detail->count);
                }
            });
        }

        return $form;
    }
}
