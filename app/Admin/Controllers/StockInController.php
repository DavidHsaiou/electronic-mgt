<?php

namespace App\Admin\Controllers;

use App\Models\eletronic;
use App\Models\StockInRecord;
use App\Models\StockInRecordDetail;
use App\Utility\TimeUtility;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;
use App\Admin\Selector\ElectronicSelector;
use Exception;
use Illuminate\Support\Facades\DB;
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

        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableView();
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
            if (count($details) == 0) return 0;
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
        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
            $tools->disableView();
        });

        $form->decimal('price_coefficient', __('price_coefficient'))->required();

        $form->hasMany('details', __('StockInRecordDetail'), function (Form\NestedForm $form) {
            $form->select('electric_id', __('electronic_name'))
                ->options(eletronic::all()->pluck('name', 'id'))->required();
            $form->decimal('original_price', __('original_price'))
                ->required();
            $form->number('count', __('Count'))->rules(['required','gt:0']);
        });

        if ($form->isEditing()) {
            $form->saving(function (Form $form) {
                Log::notice('edit started');
                $newDetails = $form->details;
                // when not enough count popup
                DB::transaction(function () use(&$newDetails) {
                    foreach ($newDetails as $newDetail) {
                        Log::notice($newDetail);
                        $electronic = eletronic::find($newDetail["electric_id"]);
                        $oldDetail = StockInRecordDetail::find($newDetail['id']);
                        $nowCount = $electronic->count;
                        $oldRecordCount = $oldDetail? $oldDetail->count: 0;
                        $newRecordCount = intval($newDetail['count']);
                        // remove data, minus all
                        if ($newDetail['_remove_'] == 1) {
                            if ($oldRecordCount > $nowCount) {
                                throw new Exception("{$electronic->name}現有數量小於移除數量");
                            }
                            if ($oldDetail->used_count > 0) {
                                throw new Exception("{$electronic->name}已經有出庫紀錄，不得移除 出庫數量:$electronic->used_count");
                            }
                            $electronic->decrement('count', $oldRecordCount);
                        } else if ($newDetail['id'] == null) {
                            // newData, just add
                            $electronic->increment('count', $newRecordCount);
                        } else {
                            // edit, compare count and add/minus count
                            $diffCount = $newRecordCount - $oldRecordCount;
                            if ($diffCount > 0) {
                                $electronic->increment('count', $diffCount);
                                if ($oldDetail->status == 1) {
                                    $oldDetail->status = 0;
                                    $oldDetail->save();
                                }
                            } else if ($diffCount < 0) {
                                if (abs($diffCount) > $nowCount) {
                                    throw new Exception("{$electronic->name}現有數量小於移除數量");
                                }
                                if(abs($diffCount) > $oldDetail->count - $oldDetail->used_count) {
                                    throw new Exception("{$electronic->name}該入庫紀錄已經標記出庫，總數量:{$oldDetail->count}、出庫數量:$oldDetail->used_count");
                                }
                                $electronic->decrement('count', abs($diffCount));
                                if ($oldDetail->count - abs($diffCount == $oldDetail->used_count)) {
                                    $oldDetail->status = 1;
                                    $oldDetail->save();
                                }
                            }
                        }
                    }
                });
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
