<?php

namespace learn\services;

/**
 * Excel导出
 * Class ExcelService
 * @package learn\services
 */
class ExcelService
{
    /**
     * 单元格宽
     * @var int
     */
    protected static $cellWidth = 20;

    /**
     * 单元格搞
     * @var int
     */
    protected static $cellHeight = 50;

    /**
     * 单元格表行
     * @var string[]
     */
    protected static $cellKey = ['A','B','C','D','E','F','G','H','I','G','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
        'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ'];

    /**
     * 文件名 && title
     * @var string
     */
    protected static $title = "表头";

    /**
     * 二级表头
     * @var string
     */
    protected static $subTitle = "";

    /**
     * 表头数据
     * @var array
     */
    protected static $header = [];

    /**
     * 列数
     * @var int
     */
    protected static $colNum = 0;

    /**
     * 表头行数
     * @var int
     */
    protected static $headerNum = 3;

    /**
     * Excel 对象
     * @var null
     */
    protected static $excel = null;

    /**
     * 样式
     * @var array
     */
    protected static $styleArray = array(
        'borders' => array(
            'allborders' => array('style' => \PHPExcel_Style_Border::BORDER_THIN),
        ),
        'font'=>[
            'bold'=>true
        ],
        'alignment'=>[
            'horizontal'=>\PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical'=>\PHPExcel_Style_Alignment::VERTICAL_CENTER
        ]
    );

    /**
     * Excel 初始化
     */
    public static function init()
    {
        self::$excel = new \PHPExcel();
    }

    /**
     * 表头
     * @return $this
     */
    public function setTitle()
    {
        // 默认设置
        self::$excel->getProperties()
            ->setCreator("Neo")
            ->setLastModifiedBy("Neo")
            ->setTitle(iconv('utf-8', 'utf-8', self::$title))
            ->setSubject("Sheet1")
            ->setDescription("")
            ->setKeywords("Sheet1")
            ->setCategory("");
        self::$excel->setActiveSheetIndex(0);
        self::$excel->getActiveSheet()->setTitle("Sheet1");
        // 标题
        self::$excel ->getActiveSheet()->setCellValue('A1', self::$title);
        self::$excel->getActiveSheet()->getRowDimension(1)->setRowHeight(40);
        self::$excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        self::$excel->getActiveSheet()->mergeCells('A1:'.(self::$cellKey[self::$colNum-1]).'1');
        self::$excel->getActiveSheet()->getStyle('A1')->getFont()->setName('黑体');
        self::$excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
        self::$excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        // 二级标题
        self::$excel->getActiveSheet()->setCellValue('A2',self::$subTitle);
        self::$excel->getActiveSheet()->getRowDimension(2)->setRowHeight(20);
        self::$excel->getActiveSheet()->mergeCells('A2:'.(self::$cellKey[self::$colNum-1]).'2');
        self::$excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        self::$excel->getActiveSheet()->getStyle('A2')->getFont()->setName('宋体');
        self::$excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(14);

        // 表头
        $sheet=self::$excel->getActiveSheet();
        foreach(self::$header as $key=>$val){
            $row=self::$cellKey[$key];
            $sheet->getColumnDimension($row)->setWidth(isset($val['w'])?$val['w']:self::$cellWidth);
            $sheet->setCellValue($row.self::$headerNum,isset($val['name'])?$val['name']:$val);
            $sheet->getStyle($row)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }
        $sheet->getStyle('A1:'.(self::$cellKey[self::$colNum-1])."1")->applyFromArray(self::$styleArray);
        $sheet->getStyle('A2:'.(self::$cellKey[self::$colNum-1])."2")->applyFromArray(self::$styleArray);
        $sheet->getStyle('A3:'.(self::$cellKey[self::$colNum-1])."3")->applyFromArray(self::$styleArray);
        $sheet->getDefaultRowDimension()->setRowHeight(self::$cellHeight);
        return $this;
    }

    /**
     * 设置头部
     * @param string $title
     * @param array $header
     * @param string $subTitle
     * @return mixed
     */
    public static function setHeader(string $title, array $header, string $subTitle = "")
    {
        if (count(self::$cellKey) < count($header)) exit(app("json")->fail("表格列数过长"));
        if (!$subTitle) self::$subTitle = "生成时间：".date('Y-m-d H:i:s',time());
        self::$header = $header;
        self::$title = $title;
        self::$colNum = count($header);
        self::init();
        return (new self)->setTitle();
    }

    /**
     * 表格内容
     * @param array $data
     * @return ExcelService
     */
    public function setBody(array $data)
    {
        $sheet = self::$excel->getActiveSheet();
        $cellKey = array_slice(self::$cellKey,0,self::$colNum);
        if($data!==null && is_array($data)){
            foreach ($cellKey as $k=>$v){
                foreach ($data as $key=>$val){
                    if(isset($val[$k]) && !is_array($val[$k])){
                        $sheet->setCellValue($v.(self::$headerNum+1+$key),$val[$k]);
                    }else if(isset($val[$k]) && is_array($val[$k])){
                        $str='';
                        foreach ($val[$k] as $value){
                            $str.=$value.chr(10);
                        }
                        $sheet->setCellValue($v.(self::$headerNum+1+$key),$str);
                    }
                }
            }
        }
        return $this;
    }

    /**
     * 保存文件
     * @throws \PHPExcel_Reader_Exception
     * @throws \PHPExcel_Writer_Exception
     */
    public function save()
    {
        $objWriter=\PHPExcel_IOFactory::createWriter(self::$excel,'Excel2007');
        $filename=self::$title.'--'.time().'.csv';
        ob_end_clean();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        exit($objWriter->save('php://output'));
    }
}