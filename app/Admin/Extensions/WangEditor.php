<?php

namespace App\Admin\Extensions;

use Encore\Admin\Form\Field;

class WangEditor extends Field
{
    protected $view = 'admin.wang-editor';

    protected static $css = [
        '/vendor/wangEditor/release/wangEditor.min.css',
    ];

    protected static $js = [
        '/vendor/wangEditor/release/wangEditor.min.js',
    ];

    public function render()
    {
        $name = $this->formatName($this->column);
// v4
        $this->script = <<<EOT

var E = window.wangEditor
var editor = new E('#{$this->id}');
editor.customConfig.zIndex = 0
editor.customConfig.uploadImgShowBase64 = true
editor.customConfig.onchange = function (html) {
    $('input[name=\'$name\']').val(html);
}
editor.create()

EOT;

        //v5
//        $this->script = <<<EOT
//
//var E = window.wangEditor
//
//const editorConfig = {}
//editorConfig.placeholder = '請輸入內容'
//editorConfig.onChange = (editor) => {
//    $('input[name =\'$name\']').val(html)
//}
//
//// 工具栏配置
//const toolbarConfig = {}
//
//// 创建编辑器
//const editor = E.createEditor({
//  selector: '#{$this->id}',
//  config: editorConfig,
//  mode: 'default' // 或 'simple' 参考下文
//})
//// 创建工具栏
//const toolbar = E.createToolbar({
//  editor,
//  selector: '#toolbar-container',
//  config: toolbarConfig,
//  mode: 'default' // 或 'simple' 参考下文
//})
//
//EOT;
        return parent::render();
    }
}
