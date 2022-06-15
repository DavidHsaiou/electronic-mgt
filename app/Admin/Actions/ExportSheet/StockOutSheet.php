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
        $notSelectString = '□';
        $SelectString = '■';

        // $model ...
        $reader = new Xlsx();
        $spreadsheet = $reader->load(resource_path()."\\template\\stockout-template.xlsx");
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $sheet = $spreadsheet->getActiveSheet();
//        $sheet->setCellValue('C2', date_format(date_create($model->order_date_time), 'Ymd'));
        $sheet->setCellValue('C2', date_format(date_create($model->order_date_time), 'Ymd')); // 訂單日期：
        $sheet->setCellValue('C3', date_format(date_create($model->shipping_date_time), 'Ymd')); // 出貨日期：
        $sheet->setCellValue('F2', ''); // 訂單編號：
        $sheet->setCellValue('F3', ''); // 交易方式：
        $sheet->setCellValue('G2', ''); // ■ 二聯式發票
        $sheet->setCellValue('G3', ''); // □ 三聯式發票
        $sheet->setCellValue('C4', ''); // 地址/ 門市：
        // start details
        $rowInit = 6;
        $itemCount = 0;
        $height = 53; // 40
        $allDetails = $model->details()->get();
        $arrayData = [
        ];
//            $sheet->insertNewRowBefore('6');
//        $arrayData = [
//            [' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' '],
//        ];
        $sheet
            ->fromArray(
                $arrayData,
                NULL,
                'A'.$rowInit
            );
        // end details
        $rowInit += $itemCount;
        $sheet->setCellValue('I'.$rowInit, ''); //小計
        $sheet->setCellValue('I'.($rowInit+1), ''); //運費
        $sheet->setCellValue('I'.($rowInit+2), ''); //折扣
        $sheet->setCellValue('I'.($rowInit+3), ''); //總金額

        $resultPath = "stockout-$model->order_number.xlsx";
        $writer->save($resultPath);
        // http://127.0.0.1:8000/stockout-1.xlsx

        return $this->response()->success('Success message.')->redirect(env('APP_URL').'/'.$resultPath);
    }
}
