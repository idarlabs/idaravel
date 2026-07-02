<?php

namespace Idaravel\AllPack;

use Illuminate\Support\HtmlString;
use ReflectionFunction;
use SplFileObject;

class HelperHtml {

    public static function htmlNumber($params = [], string $mode = 'package'){ return self::buildStructure('number', $params, $mode); }
    public static function htmlDate($params = [], string $mode = 'package'){ return self::buildStructure('date', $params, $mode); }
    public static function htmlText($params = [], string $mode = 'package'){ return self::buildStructure('text', $params, $mode); }
    public static function htmlCheckbox($params = [], string $mode = 'package'){ return self::buildStructure('checkbox', $params, $mode); }
    public static function htmlRadio($params = [], string $mode = 'package'){ return self::buildStructure('radio', $params, $mode); }
    public static function htmlSelect($params = [], string $mode = 'package'){ return self::buildStructure('select', $params, $mode); }
    public static function htmlDateTime($params = [], string $mode = 'package'){ return self::buildStructure('datetime-local', $params, $mode); }
    public static function htmlHidden($params = [], string $mode = 'package'){ return self::buildStructure('hidden', $params, $mode); }
    public static function htmlPassword($params = [], string $mode = 'package'){ return self::buildStructure('password', $params, $mode); }
    public static function htmlTextarea($params = [], string $mode = 'package'){ return self::buildStructure('textarea', $params, $mode); }
    
    private static function buildStructure($type, $params, $mode){
        $attr = $params['attr'] ?? [];

        if ($mode === 'native') {
            if (!isset($attr['class'])) {
                if (in_array($type, ['radio', 'checkbox'])) {
                    $attr['class'] = 'form-check-input';
                } else {
                    $attr['class'] = 'form-control';
                }
            }

            $attrString = '';
            foreach ($attr as $key => $val) {
                if (in_array(strtolower($key), ['required', 'readonly', 'disabled', 'multiple', 'checked', 'selected'])) {
                    if ($val) { $attrString .= " {$key}"; }
                } else {
                    $attrString .= " {$key}=\"{$val}\"";
                }
            }

            if ($type === 'textarea') {
                return new HtmlString("<textarea{$attrString}></textarea>");
            }

            return new HtmlString("<input type=\"{$type}\"{$attrString}>");
        }

        return [
            '_type' => 'idar-input',
            'input_type' => $type,
            'attr' => $attr,
            'data' => $params['data'] ?? []
        ];
    }

    public static function selectData($dbData, array $columns){
        $formattedData = [];
        $valueKey = $columns[0] ?? 'id'; 
        $textKey = $columns[1] ?? 'nama_obat'; 

        foreach ($dbData as $row) {
            $rowArray = (array) $row;
            $text = $rowArray[$textKey] ?? '';
            
            if (str_starts_with($valueKey, 'base64:')) {
                $subKey = explode(':', $valueKey)[1] ?? 'data';
                
                if ($subKey === 'data') {
                    $value = base64_encode(json_encode($rowArray));
                } else {
                    $payload = [$subKey => $rowArray[$subKey] ?? null];
                    $value = base64_encode(json_encode($payload));
                }
            } elseif (str_starts_with($valueKey, 'json:')) {
                $subKey = explode(':', $valueKey)[1] ?? 'data';
                
                if ($subKey === 'data') {
                    $jsonString = json_encode($rowArray);
                } else {
                    $payload = [$subKey => $rowArray[$subKey] ?? null];
                    $jsonString = json_encode($payload);
                }

                $value = htmlspecialchars($jsonString, ENT_QUOTES, 'UTF-8');
            } else {
                $value = $rowArray[$valueKey] ?? '';
            }

            $formattedData[$value] = $text;
        }

        return $formattedData;
    }

    public static function htmlButton($params = [], string $mode = 'package'){
        $attr = $params['attr'] ?? [];

        if ($mode === 'native') {
            if (!isset($attr['class'])) {
                $attr['class'] = 'btn btn-primary';
            }

            $attrString = '';
            foreach ($attr as $key => $val) {
                if (in_array(strtolower($key), ['required', 'readonly', 'disabled', 'multiple', 'checked', 'selected'])) {
                    if ($val) { $attrString .= " {$key}"; }
                } else {
                    $attrString .= " {$key}=\"{$val}\"";
                }
            }

            $text = $params['text'] ?? 'Simpan';
            return new HtmlString("<button type=\"submit\"{$attrString}>{$text}</button>");
        }

        return [
            '_type' => 'idar-button',
            'text' => $params['text'] ?? 'Simpan',
            'attr' => $attr
        ];
    }

    public static function dataProvider(array $config){
        $ajaxUrl = $config['data'] ?? '';
        $tableConfig = $config['table'] ?? [];
        $kolomList = $tableConfig['kolom'] ?? [];
        $tableAttr = $tableConfig['attr'] ?? [];
        $tableId = $tableAttr['id'] ?? 'idar-dt';
        $cleanId = str_replace(['[', ']'], '', $tableId);
        $tableClass = $tableAttr['class'] ?? 'table-sm table-hover';

        $jsColumns = [];
        $columnDefs = [];
        $columnIndex = 0;

        foreach ($kolomList as $key => $col) {
            $dataField = ($key === 'nomor') ? null : ($col['data'] ?? $key);
            $jsColumns[] = ['data' => $dataField, 'name' => $dataField, 'defaultContent' => ''];
            
            $className = $col['class'] ?? '';

            if ($key === 'nomor') {
                $columnDefs[] = "{ 'targets': {$columnIndex}, 'render': function(data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; }, 'orderable': false, 'className': '{$className}' }";
            } elseif (isset($col['render'])) {
                $renderValue = $col['render'];
                $columnDefs[] = "{ 'targets': {$columnIndex}, 'render': {$renderValue}, 'className': '{$className}' }";
            } else {
                $columnDefs[] = "{ 'targets': {$columnIndex}, 'className': '{$className}' }";
            }
            $columnIndex++;
        }

        $jsColDefs = !empty($columnDefs) ? "columnDefs: [" . implode(',', $columnDefs) . "]," : "";

        $html = "<table id='{$cleanId}' class='table {$tableClass}' style='width:100%'><thead><tr>";
        foreach ($kolomList as $col) { 
            $thClass = isset($col['class']) ? " class='{$col['class']}'" : "";
            $thWidth = isset($col['width']) ? " style='width:{$col['width']}'" : "";
            $html .= "<th{$thClass}{$thWidth}>{$col['label']}</th>"; 
        }
        $html .= "</tr></thead><tbody></tbody></table>";

        $html .= "<script>
            $(document).ready(function() {
                if ($.fn.DataTable.isDataTable('#{$cleanId}')) {
                    $('#{$cleanId}').DataTable().destroy();
                }
                $('#{$cleanId}').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{$ajaxUrl}',
                        type: 'POST',
                        headers: { 'X-CSRF-TOKEN': '" . csrf_token() . "' }
                    },
                    columns: " . json_encode($jsColumns) . ",
                    {$jsColDefs}
                    language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' }
                });
            });
        </script>";

        return new \Illuminate\Support\HtmlString($html);
    }
}