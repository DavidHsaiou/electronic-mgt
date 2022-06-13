<?php

namespace App\Admin\Controllers;

use App\Models\shippingType;
use App\Models\StockOutRecord;
use App\Utility\TimeUtility;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
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

        $grid->column('id', __('Id'));
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

        $form->text('order_number', __('Order number'));
        $form->select('shipping_type', __('Shipping type'))
            ->options(shippingType::where('status', 1)->get()->pluck('name', 'id'));
        $form->number('bill_type', __('Bill type'));
        $form->number('stock_out_type', __('Stock out type'));
        $form->number('sell_channel_type', __('Sell channel type'));
        $form->datetime('order_date_time', __('Order date time'))->default(date('Y-m-d H:i:s'));
        $form->datetime('shipping_date_time', __('Shipping date time'))->default(date('Y-m-d H:i:s'));
        $form->text('address', __('Address'));
        $form->decimal('real_amount', __('Real amount'));
        $form->decimal('buyer_amount', __('Buyer amount'));
        $form->decimal('delivery_charge', __('Delivery charge'));
        $form->decimal('discount_amount', __('Discount amount'));
        $form->textarea('memo', __('Memo'));

        return $form;
    }
}
