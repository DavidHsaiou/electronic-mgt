<?php

namespace App\Admin\Actions\ExportSheet;

use App\Models\BillType;
use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class StockOutSheet extends RowAction
{
    public $name = '出貨單';

    public function handle(Model $model)
    {
        $notSelectString = '□ ';
        $SelectString = '■ ';

        // $model ...
        $reader = new Xlsx();
        $spreadsheet = $reader->load(resource_path()."\\template\\stockout-template.xlsx");
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $sheet = $spreadsheet->getActiveSheet();
//        $sheet->setCellValue('C2', date_format(date_create($model->order_date_time), 'Ymd'));
        $sheet->setCellValue('C2', date_format(date_create($model->order_date_time), 'Ymd')); // 訂單日期：
        $sheet->setCellValue('C3', date_format(date_create($model->shipping_date_time), 'Ymd')); // 出貨日期：
        $sheet->setCellValue('F2', $model->order_number); // 訂單編號：
        $sheet->setCellValue('F3', $model->ShippingType()->first()->name.' | '.$model->shipping_order_number); // 交易方式：
        $sheet->setCellValue('G2', ($model->bill_type == 1 ? $SelectString: $notSelectString).BillType::find(1)->name); // ■ 二聯式發票 1
        $sheet->setCellValue('G3',  ($model->bill_type == 2 ? $SelectString: $notSelectString).BillType::find(2)->name); // □ 三聯式發票 2
        $sheet->setCellValue('C4', $model->address); // 地址/ 門市：
        // start details
        $rowInit = 6;
        $itemCount = 0;
        $height = 53; // 40
        $allDetails = $model->details()->get();
        $arrayData = [
        ];
        $priceTotal = 0;
        foreach($allDetails as $detail) {
            $currentRow = 6+ $itemCount++;
            $sheet->insertNewRowBefore($currentRow);
            $sheet->getRowDimension(6)->setRowHeight($height);
            $electronic = $detail->useElectronic()->first();
            $detailTotal = $detail->single_price*$detail->count;

            $drawing = new Drawing();
            $imagePath = ('uploads\\'.$electronic->image_path);
            $drawing->setPath($imagePath);
            $drawing->setCoordinates('B'.$currentRow);
            $drawing->setWidthAndHeight(128, $height);
            $drawing->setWorksheet($sheet);

            $dataRow = [
                $itemCount,
                '',
                $electronic->name,
                $electronic->StorageArea()->get()->map(function ($storage) {
                    return $storage->name;
                })->join('+'),
                $electronic->options,
                $electronic->description,
                $detail->single_price,
                $detail->count,
                $detailTotal
            ];

            array_push($arrayData, $dataRow);
            $priceTotal += $detailTotal;
        }
        $sheet
            ->fromArray(
                $arrayData,
                NULL,
                'A'.$rowInit
            );
        // end details
        $rowInit += $itemCount;
        $sheet->setCellValue('I'.$rowInit, $priceTotal); //小計
        $sheet->setCellValue('I'.($rowInit+1), $model->delivery_charge); //運費
        $sheet->setCellValue('I'.($rowInit+2), $model->discount_amount); //折扣
        $sheet->setCellValue('I'.($rowInit+3), $priceTotal + $model->delivery_charge - $model->discount_amount); //總金額

        $resultPath = "stockout-$model->order_number.xlsx";
        $writer->save($resultPath);
        // http://127.0.0.1:8000/stockout-1.xlsx

        return $this->response()->success('Success message.')->redirect(env('APP_URL').'/'.$resultPath);
    }
}
