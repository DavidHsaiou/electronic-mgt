<?php

namespace App\Admin\Controllers;

use App\Models\eletronic;
use App\Models\PurchaseOrder;
use App\Models\StockInRecord;
use App\Models\StockInRecordDetail;
use App\Utility\NumberUtility;
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

/**
 * @property $input
 */
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
            $filter->where(function ($query) {
                $query->whereHas('PurchaseOrder', function ($query) {
                    $query->where('id', "$this->input");
                });
            }, __('PurchaseOrder'))->select(PurchaseOrder::where('status', 1)
                ->get()
                ->map(function ($item) {
                    return [
                        'name' => $item->SupplyMerchant()->first()->supply_name.'-'.$item->purchase_time,
                        'id' => $item->id
                    ];
                })->pluck('name', 'id')
            );

            $filter->where(function ($query) {
                $query->whereHas('details', function ($query) {
                    $query->where('electric_id', "$this->input");
                });
            }, __('electronic_name'))->select(eletronic::all()
                ->map(function ($item) {
                    return [
                        'name' => $item->GetSelectName(),
                        'id' => $item->id
                    ];
                })->pluck('name', 'id')
            );
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
                    $electronic = $detail->useElectronic()->first();
                    $detail->electronic_name = $electronic->name;
                    $detail->storageAreas = implode(',',
                        $electronic->StorageArea()->get()
                            ->map(function ($item) {
                                return $item['name'];
                            })->all());
                    return $detail->only([
                        'electronic_name',
                        'storageAreas',
                        'original_price',
                        'singlePrice',
                        'count',
                        'totalPrice']);
                });
            return new Table([
                __('electronic_name'),
                __('store location'),
                __('original_price'),
                __('singlePrice'),
                __('Count'),
                __('totalPrice')],
                $details->toArray());
        });
        $grid->column('PurchaseOrder', __('PurchaseOrder'))->display(function () {
            $order = $this->PurchaseOrder()
                ->first();
            return $order->SupplyMerchant()->first()->supply_name.'-'.$order->purchase_time;
        });
        $grid->column('price_coefficient', __('price_coefficient'))->display(function ($data) {
            return NumberUtility::toDisplyFloat($data);
        });
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

        $form->select('purchase_id', __('PurchaseOrder'))
            ->options(PurchaseOrder::where('status', 1)->get()
                ->map(function (PurchaseOrder $item, $key) {
                    return [
                        'purchase_name' => $item->SupplyMerchant()->first()->supply_name.'-'.$item->purchase_time,
                        'id' => $item->id
                    ];
                })
                ->pluck('purchase_name', 'id'))
            ->required();

        $form->hidden('price_coefficient');
        $form->hasMany('details', __('StockInRecordDetail'), function (Form\NestedForm $form) {
            $form->select('electric_id', __('electronic_name'))
                ->options(eletronic::all()
                    ->map(function ($item) {
                        return [
                            'name' => $item->GetSelectName(),
                            'id' => $item->id
                        ];
                    })
                    ->pluck('name', 'id'))
                ->required();
            $form->decimal('original_price', __('original_price'))
                ->required();
            $form->number('count', __('Count'))->rules(['required','gt:0']);
        });

        $form->textarea('memo', __('memo'));

        if ($form->isEditing()) {
            $form->saving(function (Form $form) {
                Log::notice('edit started');
                $order = PurchaseOrder::where('id', $form->purchase_id)->first();
                $form->price_coefficient = PurchaseOrderController::getPriceCoefficient($order);
                $newDetails = $form->details;
                if (is_null($newDetails) || count($newDetails) == 0) return;
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
                                throw new Exception("{$electronic->name}??????????????????????????????");
                            }
                            if ($oldDetail->used_count > 0) {
                                throw new Exception("{$electronic->name}???????????????????????????????????? ????????????:$electronic->used_count");
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
                                    throw new Exception("{$electronic->name}??????????????????????????????");
                                }
                                if(abs($diffCount) > $oldDetail->count - $oldDetail->used_count) {
                                    throw new Exception("{$electronic->name}?????????????????????????????????????????????:{$oldDetail->count}???????????????:$oldDetail->used_count");
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
            $form->saving(function (Form $form) {
                $order = PurchaseOrder::where('id', $form->purchase_id)->first();
                $form->price_coefficient = PurchaseOrderController::getPriceCoefficient($order);
            });

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
