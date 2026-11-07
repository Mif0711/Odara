<?php

namespace App\Support;

/**
 * Pembuat file XLSX sederhana tanpa dependency eksternal.
 *
 * File XLSX pada dasarnya adalah arsip ZIP berisi XML. Kelas ini menulis
 * ZIP dengan metode "stored" sehingga tidak membutuhkan ekstensi zip/zlib.
 */
final class XlsxReport
{
    /**
     * @param  array<string, string>  $meta
     * @param  array<int, string>  $headers
     * @param  array<int, array<int, string|int|float|null>>  $rows
     * @param  array<int, string|int|float|null>  $totals
     * @param  array<int, 'decimal'|'integer'>  $numericColumns
     * @param  array<int, float|int>  $widths
     */
    public static function make(
        string $sheetName,
        string $title,
        array $meta,
        array $headers,
        array $rows,
        array $totals,
        array $numericColumns,
        array $widths,
    ): string {
        $lastColumn = self::columnLetter(count($headers) - 1);

        $titleRow = 1;
        $metaStartRow = 2;
        $metaEndRow = $metaStartRow + count($meta) - 1;
        $headerRow = $metaEndRow + 2;
        $dataStartRow = $headerRow + 1;
        $dataEndRow = $dataStartRow + count($rows) - 1;
        $totalRow = max($dataEndRow + 1, $dataStartRow);

        $sheet = self::sheetXml(
            $lastColumn,
            $title,
            $meta,
            $headers,
            $rows,
            $totals,
            $numericColumns,
            $widths,
            $titleRow,
            $metaStartRow,
            $headerRow,
            $dataStartRow,
            $dataEndRow,
            $totalRow,
        );

        $files = [
            '[Content_Types].xml' => self::contentTypesXml(),
            '_rels/.rels' => self::rootRelsXml(),
            'docProps/core.xml' => self::coreXml($title),
            'docProps/app.xml' => self::appXml(),
            'xl/workbook.xml' => self::workbookXml($sheetName),
            'xl/_rels/workbook.xml.rels' => self::workbookRelsXml(),
            'xl/styles.xml' => self::stylesXml(),
            'xl/worksheets/sheet1.xml' => $sheet,
        ];

        return self::zip($files);
    }

    /**
     * @param  array<string, string>  $meta
     * @param  array<int, string>  $headers
     * @param  array<int, array<int, string|int|float|null>>  $rows
     * @param  array<int, string|int|float|null>  $totals
     * @param  array<int, 'decimal'|'integer'>  $numericColumns
     * @param  array<int, float|int>  $widths
     */
    private static function sheetXml(
        string $lastColumn,
        string $title,
        array $meta,
        array $headers,
        array $rows,
        array $totals,
        array $numericColumns,
        array $widths,
        int $titleRow,
        int $metaStartRow,
        int $headerRow,
        int $dataStartRow,
        int $dataEndRow,
        int $totalRow,
    ): string {
        $merges = ['A'.$titleRow.':'.$lastColumn.$titleRow];

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $xml .= '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">';
        $xml .= '<sheetPr><pageSetUpPr fitToPage="1"/></sheetPr>';
        $xml .= '<dimension ref="A1:'.$lastColumn.$totalRow.'"/>';
        $xml .= '<sheetViews><sheetView tabSelected="1" workbookViewId="0">';
        $xml .= '<pane ySplit="'.$headerRow.'" topLeftCell="A'.$dataStartRow.'" activePane="bottomLeft" state="frozen"/>';
        $xml .= '<selection pane="bottomLeft" activeCell="A'.$dataStartRow.'" sqref="A'.$dataStartRow.'"/>';
        $xml .= '</sheetView></sheetViews>';
        $xml .= '<sheetFormatPr defaultRowHeight="16"/>';

        $xml .= '<cols>';
        foreach ($widths as $index => $width) {
            $xml .= '<col min="'.($index + 1).'" max="'.($index + 1).'" width="'.$width.'" customWidth="1"/>';
        }
        $xml .= '</cols>';

        $xml .= '<sheetData>';

        // Baris judul.
        $xml .= '<row r="'.$titleRow.'" ht="26" customHeight="1">';
        $xml .= self::cell('A'.$titleRow, 1, $title, false);
        $xml .= '</row>';

        // Baris meta (Periode, Diekspor).
        $rowNumber = $metaStartRow;
        foreach ($meta as $label => $value) {
            $xml .= '<row r="'.$rowNumber.'">';
            $xml .= self::cell('A'.$rowNumber, 2, $label, false);
            $xml .= self::cell('B'.$rowNumber, 3, $value, false);
            $xml .= '</row>';

            $merges[] = 'B'.$rowNumber.':'.$lastColumn.$rowNumber;
            $rowNumber++;
        }

        // Baris header tabel.
        $xml .= '<row r="'.$headerRow.'" ht="24" customHeight="1">';
        foreach ($headers as $index => $header) {
            $xml .= self::cell(self::columnLetter($index).$headerRow, 4, $header, false);
        }
        $xml .= '</row>';

        // Baris data.
        foreach ($rows as $rowIndex => $row) {
            $rowNumber = $dataStartRow + $rowIndex;
            $xml .= '<row r="'.$rowNumber.'">';

            foreach ($headers as $columnIndex => $_) {
                $value = $row[$columnIndex] ?? null;
                $type = $numericColumns[$columnIndex] ?? null;
                $style = $type === 'decimal' ? 6 : ($type === 'integer' ? 7 : 5);

                $xml .= self::cell(
                    self::columnLetter($columnIndex).$rowNumber,
                    $style,
                    $value,
                    $type !== null,
                );
            }

            $xml .= '</row>';
        }

        // Baris total.
        $firstNumericColumn = $numericColumns === [] ? 0 : min(array_keys($numericColumns));
        if ($firstNumericColumn > 0) {
            $merges[] = 'A'.$totalRow.':'.self::columnLetter($firstNumericColumn - 1).$totalRow;
        }

        $xml .= '<row r="'.$totalRow.'" ht="22" customHeight="1">';
        foreach ($headers as $columnIndex => $_) {
            $value = $totals[$columnIndex] ?? null;
            $type = $numericColumns[$columnIndex] ?? null;
            $style = $type === 'decimal' ? 9 : ($type === 'integer' ? 10 : 8);

            $xml .= self::cell(
                self::columnLetter($columnIndex).$totalRow,
                $style,
                $value,
                $type !== null,
            );
        }
        $xml .= '</row>';

        $xml .= '</sheetData>';

        $filterEndRow = max($headerRow, $dataEndRow);
        $xml .= '<autoFilter ref="A'.$headerRow.':'.$lastColumn.$filterEndRow.'"/>';

        $xml .= '<mergeCells count="'.count($merges).'">';
        foreach ($merges as $merge) {
            $xml .= '<mergeCell ref="'.$merge.'"/>';
        }
        $xml .= '</mergeCells>';

        $xml .= '<pageMargins left="0.4" right="0.4" top="0.5" bottom="0.5" header="0.3" footer="0.3"/>';
        $xml .= '<pageSetup orientation="landscape" fitToWidth="1" fitToHeight="0" paperSize="9"/>';
        $xml .= '</worksheet>';

        return $xml;
    }

    private static function cell(string $reference, int $style, string|int|float|null $value, bool $numeric): string
    {
        if ($value === null || $value === '') {
            return '<c r="'.$reference.'" s="'.$style.'"/>';
        }

        if ($numeric) {
            return '<c r="'.$reference.'" s="'.$style.'"><v>'.(0 + $value).'</v></c>';
        }

        return '<c r="'.$reference.'" s="'.$style.'" t="inlineStr"><is><t xml:space="preserve">'
            .self::escape((string) $value)
            .'</t></is></c>';
    }

    private static function columnLetter(int $index): string
    {
        $letter = '';
        $index++;

        while ($index > 0) {
            $mod = ($index - 1) % 26;
            $letter = chr(65 + $mod).$letter;
            $index = intdiv($index - 1, 26);
        }

        return $letter;
    }

    private static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    private static function contentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            .'<Default Extension="xml" ContentType="application/xml"/>'
            .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            .'<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            .'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            .'<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>'
            .'<Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>'
            .'</Types>';
    }

    private static function rootRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            .'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>'
            .'<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>'
            .'</Relationships>';
    }

    private static function workbookXml(string $sheetName): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            .'<bookViews><workbookView activeTab="0"/></bookViews>'
            .'<sheets><sheet name="'.self::escape($sheetName).'" sheetId="1" r:id="rId1"/></sheets>'
            .'</workbook>';
    }

    private static function workbookRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            .'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            .'</Relationships>';
    }

    private static function coreXml(string $title): string
    {
        $now = now()->toIso8601String();

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" '
            .'xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" '
            .'xmlns:dcmitype="http://purl.org/dc/dcmitype/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'
            .'<dc:title>'.self::escape($title).'</dc:title>'
            .'<dc:creator>ODARA</dc:creator>'
            .'<cp:lastModifiedBy>ODARA</cp:lastModifiedBy>'
            .'<dcterms:created xsi:type="dcterms:W3CDTF">'.$now.'</dcterms:created>'
            .'<dcterms:modified xsi:type="dcterms:W3CDTF">'.$now.'</dcterms:modified>'
            .'</cp:coreProperties>';
    }

    private static function appXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" '
            .'xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">'
            .'<Application>ODARA</Application>'
            .'</Properties>';
    }

    private static function stylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<numFmts count="2">'
            .'<numFmt numFmtId="164" formatCode="#,##0.00"/>'
            .'<numFmt numFmtId="165" formatCode="#,##0"/>'
            .'</numFmts>'
            .'<fonts count="4">'
            .'<font><sz val="11"/><name val="Calibri"/><family val="2"/></font>'
            .'<font><b/><sz val="16"/><color rgb="FF0F5F53"/><name val="Calibri"/><family val="2"/></font>'
            .'<font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/><family val="2"/></font>'
            .'<font><b/><sz val="11"/><color rgb="FF1F2937"/><name val="Calibri"/><family val="2"/></font>'
            .'</fonts>'
            .'<fills count="4">'
            .'<fill><patternFill patternType="none"/></fill>'
            .'<fill><patternFill patternType="gray125"/></fill>'
            .'<fill><patternFill patternType="solid"><fgColor rgb="FF0F5F53"/><bgColor indexed="64"/></patternFill></fill>'
            .'<fill><patternFill patternType="solid"><fgColor rgb="FFE8F3F0"/><bgColor indexed="64"/></patternFill></fill>'
            .'</fills>'
            .'<borders count="2">'
            .'<border><left/><right/><top/><bottom/><diagonal/></border>'
            .'<border><left style="thin"><color rgb="FFD1D5DB"/></left><right style="thin"><color rgb="FFD1D5DB"/></right>'
            .'<top style="thin"><color rgb="FFD1D5DB"/></top><bottom style="thin"><color rgb="FFD1D5DB"/></bottom><diagonal/></border>'
            .'</borders>'
            .'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            .'<cellXfs count="11">'
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            .'<xf numFmtId="0" fontId="1" fillId="3" borderId="0" xfId="0" applyFont="1" applyFill="1" applyAlignment="1">'
            .'<alignment horizontal="left" vertical="center"/></xf>'
            .'<xf numFmtId="0" fontId="3" fillId="0" borderId="0" xfId="0" applyFont="1"/>'
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            .'<xf numFmtId="0" fontId="2" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1">'
            .'<alignment horizontal="center" vertical="center" wrapText="1"/></xf>'
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1">'
            .'<alignment vertical="top" wrapText="1"/></xf>'
            .'<xf numFmtId="164" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1" applyAlignment="1">'
            .'<alignment horizontal="right" vertical="top"/></xf>'
            .'<xf numFmtId="165" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1" applyAlignment="1">'
            .'<alignment horizontal="right" vertical="top"/></xf>'
            .'<xf numFmtId="0" fontId="3" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1">'
            .'<alignment vertical="center"/></xf>'
            .'<xf numFmtId="164" fontId="3" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyNumberFormat="1" applyAlignment="1">'
            .'<alignment horizontal="right" vertical="center"/></xf>'
            .'<xf numFmtId="165" fontId="3" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyNumberFormat="1" applyAlignment="1">'
            .'<alignment horizontal="right" vertical="center"/></xf>'
            .'</cellXfs>'
            .'<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            .'</styleSheet>';
    }

    /**
     * @param  array<string, string>  $files
     */
    private static function zip(array $files): string
    {
        $localData = '';
        $centralDirectory = '';
        $offset = 0;

        foreach ($files as $name => $contents) {
            $name = str_replace('\\', '/', $name);
            $crc = crc32($contents);
            $size = strlen($contents);
            $nameLength = strlen($name);

            $localHeader = pack(
                'VvvvvvVVVvv',
                0x04034b50,
                20,
                0,
                0,
                0,
                0x0021,
                $crc,
                $size,
                $size,
                $nameLength,
                0,
            );

            $localData .= $localHeader.$name.$contents;

            $centralDirectory .= pack(
                'VvvvvvvVVVvvvvvVV',
                0x02014b50,
                20,
                20,
                0,
                0,
                0,
                0x0021,
                $crc,
                $size,
                $size,
                $nameLength,
                0,
                0,
                0,
                0,
                0,
                $offset,
            ).$name;

            $offset += strlen($localHeader) + $nameLength + $size;
        }

        $end = pack(
            'VvvvvVVv',
            0x06054b50,
            0,
            0,
            count($files),
            count($files),
            strlen($centralDirectory),
            $offset,
            0,
        );

        return $localData.$centralDirectory.$end;
    }
}
