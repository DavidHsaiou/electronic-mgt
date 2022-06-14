<?php

namespace App\Admin\Controllers;

use App\Models\PurchaseOrder;
use App\Models\StockInRecord;
use App\Models\SupplyManagement;
use App\Utility\NumberUtility;
use App\Utility\TimeUtility;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Log;

/**
 * @property $original_amount
 */
class PurchaseOrderController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'PurchaseOrder';
    function __construct() {
        $this->title = __($this->title);
    }

    /**
     * @param PurchaseOrderController $model
     * @return float|int
     */
    public static function getPriceCoefficient(PurchaseOrder $model)
    {
        return ($model->delivery_charge + $model->tariff + $model->miscellaneous_branch + $model->charge) / $model->original_amount;
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new PurchaseOrder());

        $grid->column('id', __('Id'));
        $grid->column('SupplyMerchant.supply_name', __('SupplyMerchant'));
        $grid->column('purchase_time', __('Purchase time'));
        $grid->column('original_amount', __('Original amount'));
        $grid->column('original_delivery_charge', __('Original delivery charge'));
        $grid->column('delivery_charge', __('taiwan delivery charge'));
        $grid->column('tariff', __('taiwan tariff'));
        $grid->column('miscellaneous_branch', __('taiwan miscellaneous branch'));
        $grid->column('charge', __('taiwan charge'));
        $grid->column('price_coefficient', __('price_coefficient').__('price_coefficient_math'))->display(function () {
            $model = $this;
            if ($model->original_amount == 0) return '-';
            return NumberUtility::toDisplyFloat(PurchaseOrderController::getPriceCoefficient($model));
        });
        $grid->column('status', __('Status'))->bool();
        $grid->column('memo', __('Memo'));
        $grid->column('created_at', __('Created at'))->display(function($create) {
            return TimeUtility::toDisplyTime($create);
        });
        $grid->column('updated_at', __('Updated at'))->display(function($create) {
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
        $show = new Show(PurchaseOrder::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('supply_id', __('Supply id'));
        $show->field('purchase_time', __('Purchase time'));
        $show->field('original_amount', __('Original amount'));
        $show->field('original_delivery_charge', __('Original delivery charge'));
        $show->field('delivery_charge', __('Delivery charge'));
        $show->field('tariff', __('Tariff'));
        $show->field('miscellaneous_branch', __('Miscellaneous branch'));
        $show->field('charge', __('Charge'));
        $show->field('status', __('Status'));
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
        $form = new Form(new PurchaseOrder());

        $form->select('supply_id', __('SupplyMerchant'))
            ->options(SupplyManagement::where('status', 1)
                ->pluck('supply_name', 'id'))
            ->required();
        $form->datetime('purchase_time', __('Purchase time'))
            ->default(date('Y-m-d H:i:s'))
            ->required();
        $form->decimal('original_amount', __('Original amount'))
            ->required();
        $form->decimal('original_delivery_charge', __('Original delivery charge'))
            ->required();
        $form->decimal('delivery_charge', __('taiwan delivery charge'))
            ->required();
        $form->decimal('tariff', __('taiwan tariff'))
            ->required();
        $form->decimal('miscellaneous_branch', __('taiwan miscellaneous branch'))
            ->required();
        $form->decimal('charge', __('taiwan charge'))
            ->required();
        $form->switch('status', __('Status'))
            ->required();
        $form->textarea('memo', __('Memo'));

        if ($form->isEditing()) {
            $form->saved(function ($form) {
                $order = $form->model();
                $stockInRecords = $order->StockInRecords()->get();
                if (count($stockInRecords) == 0) return;
                foreach ($stockInRecords as $stockInRecord) {
                    $stockInRecord->price_coefficient = PurchaseOrderController::getPriceCoefficient($order);
                    $stockInRecord->save();
                }
            });
        }

        return $form;
    }
}
