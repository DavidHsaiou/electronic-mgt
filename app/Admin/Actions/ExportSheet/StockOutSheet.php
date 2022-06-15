<?php

namespace App\Admin\Actions\ExportSheet;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class StockOutSheet extends RowAction
{
    public $name = '出貨單';

    public function handle(Model $model)
    {
        // $model ...
        $reader = new Xlsx();
        $spreadsheet = $reader->load(resource_path()."\\template\\stockout-template.xlsx");
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $resultPath = "stockout-$model->order_number.xlsx";
        $writer->save($resultPath);
        // http://127.0.0.1:8000/stockout-1.xlsx

        return $this->response()->success('Success message.')->redirect(env('APP_URL').'/'.$resultPath);
    }
}
