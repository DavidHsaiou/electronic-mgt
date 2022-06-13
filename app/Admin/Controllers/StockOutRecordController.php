<?php

namespace App\Admin\Controllers;

use App\Models\billType;
use App\Models\eletronic;
use App\Models\SellChannel;
use App\Models\shippingType;
use App\Models\StockOutRecord;
use App\Models\StockOutRecordDetail;
use App\Models\StockOutType;
use App\Utility\TimeUtility;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockOutRecordController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'StockOutRecord';
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
        $grid = new Grid(new StockOutRecord());
        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
        });

        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableView();
        });

        $grid->column('id', __('Id'))->expand(function ($model){
            $details = $model->Details()
                ->get()
                ->map(function ($detail){
                    $detail->singlePrice = $detail->single_price;
                    $detail->totalPrice = $detail->single_price * $detail->count;
                    $detail->electronic_name = $detail->useElectronic()->first()->name;
                    return $detail->only([
                        'electronic_name',
                        'single_price',
                        'count',
                        'totalPrice']);
                });
            return new Table([
                __('electronic_name'),
                __('single_price'),
                __('count'),
                __('total_price')],
                $details->toArray());
        });
        $grid->column('order_number', __('Order number'));
        $grid->column('ShippingType.name', __('Shipping type'));
        $grid->column('BillType.name', __('Bill type'));
        $grid->column('StockOutType.name', __('Stock out type'));
        $grid->column('SellChannelType.name', __('Sell channel type'));
        $grid->column('order_date_time', __('Order date time'));
        $grid->column('shipping_date_time', __('Shipping date time'));
        $grid->column('address', __('Address'));
        $grid->column('real_amount', __('Real amount'));
        $grid->column('buyer_amount', __('Buyer amount'));
        $grid->column('delivery_charge', __('Delivery charge'));
        $grid->column('discount_amount', __('Discount amount'));
        $grid->column('memo', __('Memo'));
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
        $show = new Show(StockOutRecord::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('order_number', __('Order number'));
        $show->field('shipping_type', __('Shipping type'));
        $show->field('bill_type', __('Bill type'));
        $show->field('stock_out_type', __('Stock out type'));
        $show->field('sell_channel_type', __('Sell channel type'));
        $show->field('order_date_time', __('Order date time'));
        $show->field('shipping_date_time', __('Shipping date time'));
        $show->field('address', __('Address'));
        $show->field('real_amount', __('Real amount'));
        $show->field('buyer_amount', __('Buyer amount'));
        $show->field('delivery_charge', __('Delivery charge'));
        $show->field('discount_amount', __('Discount amount'));
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
        $form = new Form(new StockOutRecord());


        $form->column(1/2, function ($form) {
            $form->text('order_number', __('Order number'))->required();
            $form->select('bill_type', __('Bill type'))
                ->options(billType::where('status', 1)->get()->pluck('name', 'id'))
                ->required();
            $form->select('stock_out_type', __('Stock out type'))
                ->options(StockOutType::where('status', 1)->get()->pluck('name', 'id'))
                ->required();
            $form->select('sell_channel_type', __('Sell channel type'))
                ->options(SellChannel::where('status', 1)->get()->pluck('name', 'id'))
                ->required();
            $form->select('shipping_type', __('Shipping type'))
                ->options(shippingType::where('status', 1)->get()->pluck('name', 'id'))
                ->required();
            $form->text('address', __('Address'));

        });

        $form->column(6 , function ($form) {
            $form->datetime('order_date_time', __('Order date time'))
                ->default(date('Y-m-d H:i:s'))
                ->required();
            $form->datetime('shipping_date_time', __('Shipping date time'))
                ->default(date('Y-m-d H:i:s'))
                ->required();
            $form->decimal('real_amount', __('Real amount'))
                ->rules(['required', 'gte:0']);
            $form->decimal('buyer_amount', __('Buyer amount'))
                ->rules(['required', 'gte:0']);
            $form->decimal('delivery_charge', __('Delivery charge'))
                ->rules(['required', 'gte:0']);
            $form->decimal('discount_amount', __('Discount amount'))
                ->rules(['required', 'gte:0']);
        });

        $form->column(13 , function ($form) {
            $form->hasMany('Details', __('StockOutRecordDetail'), function (Form\NestedForm $form) {
                $form->select('electric_id', __('electronic_name'))
                    ->options(eletronic::all()->pluck('name', 'id'))->required();
                $form->decimal('single_price', __('single_price'))
                    ->required();
                $form->number('count', __('Count'))->rules(['required','gt:0']);
            });
            $form->textarea('memo', __('Memo'));
        });

        if ($form->isEditing()) {
            $form->saving(function (Form $form) {
                $newDetails = $form->Details;
                // when not enough count popup
                DB::transaction(function () use(&$newDetails) {
                    foreach ($newDetails as $newDetail) {
                        $electronic = eletronic::find($newDetail["electric_id"]);
                        $oldDetail = StockOutRecordDetail::find($newDetail['id']);
                        $nowCount = $electronic->count;
                        $oldRecordCount = $oldDetail? $oldDetail->count: 0;
                        $newRecordCount = $newDetail['count'];
                        // remove data, add all
                        if ($newDetail['_remove_'] == 1) {
                            $electronic->increment('count', $oldRecordCount);
                        } else if ($newDetail['id'] == null) {
                            // newData, check count and minus
                            if ($newRecordCount > $nowCount) {
                                throw new Exception("{$electronic->name}現有數量小於使用數量");
                            }
                            $electronic->decrement('count', $newRecordCount);
                        } else {
                            // edit, compare count and add/minus count
                            $diffCount = $newRecordCount - $oldRecordCount;
                            if ($diffCount > 0) {
                                if ($diffCount > $nowCount) {
                                    throw new Exception("{$electronic->name}現有數量小於使用數量");
                                }
                                $electronic->decrement('count', $diffCount);
                            } else if ($diffCount < 0) {
                                $electronic->increment('count', abs($diffCount));
                            }
                        }
                    }
                });
            });
        }

        if ($form->isCreating()) {
            $form->saving(function (Form $form) {
                $newDetails = $form->Details;
                DB::transaction(function () use (&$newDetails) {
                    foreach ($newDetails as $newDetail) {
                        $electronic = eletronic::find($newDetail["electric_id"]);
                        $nowCount = $electronic->count;
                        $useCount = $newDetail['count'];
                        if ($useCount > $nowCount) {
                            throw new Exception("{$electronic->name}現有數量小於使用數量");
                        }
                        $electronic->decrement('count', $useCount);
                    }
                });
            });
        }

        return $form;
    }
}
