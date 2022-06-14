<?php

namespace App\Admin\Controllers;

use App\Models\ElectronicType;
use App\Models\eletronic;
use App\Models\StockInRecordDetail;
use App\Models\StorageArea;
use App\Models\WorkState;
use App\Utility\NumberUtility;
use App\Utility\TimeUtility;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Grid\Filter;
use Encore\Admin\Show;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * @property $input
 */
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

        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableView();
        });

        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->column(1/2, function (Filter $filter) {
                $filter->like('name', __('Name'));
                $filter->like('description', __('description'));
                $filter->where(function ($query) {
                    $query->whereHas('WorkState', function ($query) {
                        $query->where('id', "$this->input");
                    });
                }, __('flowTag'))->select(WorkState::where('status', 1)->get()->pluck('name', 'id'));
                $filter->where(function ($query) {
                    $query->whereHas('ElectronicType', function ($query) {
                        $query->where('id', "$this->input");
                    });
                }, __('ElectronicType'))->select(ElectronicType::where('status', 1)->get()->pluck('TypeName', 'id'));
            });
        });

        $grid->model()
            ->select('eletronics.*')
            ->join('electronic_types as et', 'et.id', '=', 'eletronics.electronic_type')
            ->orderBy('et.sort');

        $grid->column('id', __('Id'));
        $grid->column('id_name', __('id_name'))->sortable();
        $grid->column('name', __('Name'));
        $grid->column('options', __('options'));
        $grid->column('count', __('Count'));
        $grid->column('ElectronicType.TypeName', __('ElectronicType'));
        $grid->WorkState(__('flowTag'))
            ->display(function ($workState) {
                $returnData = array_map(function ($workState) {
                    return $workState['name'];
                }, $workState);
                return $returnData;
            })->label();
        $grid->StorageArea(__('store location'))
            ->display(function ($storages){
            $returnData = array_map(function ($storage) {
                return $storage['name'];
            }, $storages);
            return $returnData;
        })->label();
        $grid->column('description', __('description'));
        $grid->column('image_path', __('upload_image'))->image();
        $grid->column('essential_name', __('essential_name'));
        $grid->column('pricing', __('pricing'));
        $grid->column('profit', __('profit'))->display(function (){
            $price = $this['pricing'];
            if ($price == 0) {
                return "-";
            }

            $electronic_id = $this['id'];

            $totalCount = 0;
            $totalPrice = 0;
            $remainRecords = StockInRecordDetail::where('electric_id', $electronic_id)
                ->where('status', 0)->get();
            if (count($remainRecords) == 0) { return '-'; }
            foreach ($remainRecords as $remainRecord) {
                $price_coefficient = $remainRecord->mainRecord()->first()->price_coefficient;
                $remainCount = $remainRecord->count - $remainRecord->used_count;
                $totalCount += $remainCount;
                $totalPrice += $remainCount * $remainRecord->original_price * $price_coefficient;
            }
            return NumberUtility::toDisplyFloat(($price / ($totalPrice / $totalCount) * 100)).'%';
        });
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

        $form->tools(function (Form\Tools $tools) {
            $tools->disableView();
        });

        $form->text('name', __('Name'))
            ->required();
        $form->text('id_name', __('id_name'))
            ->required();
        $form->text('options', __('options'));
        $form->textarea('description', __('description'))
            ->required();
        $form->select('electronic_type', __('ElectronicType'))
            ->options(ElectronicType::where('status', 1)
                ->orderBy('sort')
                ->pluck('TypeName', 'id'))
            ->required();
        $form->multipleSelect('StorageArea', __('store location'))
            ->options(DB::table('storage_areas', 'sa')
                ->leftJoin('electronic_storage_areas as e_sa', 'e_sa.storage_id','=', 'sa.id')
                ->where('sa.status', '=', '1')
                ->groupBy('sa.id', 'e_sa.storage_id', 'sa.name')
                ->select(DB::raw("IF(e_sa.storage_id is null, concat(sa.name, '(空的)'), sa.name) as name"), 'id')
                ->get()
                ->pluck('name', 'id'))
            ->required();
        $form->multipleSelect('WorkState', __('flowTag'))
            ->options(WorkState::where('status', 1)
            ->pluck('name', 'id'))
            ->required();
        $form->text('essential_name', __('essential_name'));
        $form->image('image_path', __('upload_image'));
        $form->number('pricing', __('pricing'));
        $form->text('tax_rule', __('tax_rule'));
        $form->text('bill_name', __('bill_name'));
        $form->textarea('memo', __('memo'));

        return $form;
    }
}
