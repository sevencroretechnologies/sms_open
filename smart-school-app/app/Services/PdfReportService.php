<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

/**
 * PDF Report Service
 * 
 * Prompt 425: Create PDF Report Service
 * 
 * Base service for generating PDF reports with consistent styling.
 * Provides reusable methods for creating PDF documents with school branding.
 * 
 * Features:
 * - Generate PDF from HTML content
 * - Generate PDF from Blade templates
 * - Consistent school branding and headers
 * - Support for various paper sizes and orientations
 * - Table generation helpers
 */
class PdfReportService
{
    protected array $defaultOptions = [
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled' => true,
        'defaultFont' => 'DejaVu Sans',
        'dpi' => 150,
        'defaultMediaType' => 'screen',
        'isFontSubsettingEnabled' => true,
    ];

    /**
     * Generate PDF from HTML content.
     *
     * @param string $html
     * @param string $filename
     * @param string $paper
     * @param string $orientation
     * @return Response
     */
    public function generateFromHtml(
        string $html, 
        string $filename, 
        string $paper = 'a4', 
        string $orientation = 'portrait'
    ): Response {
        $pdf = Pdf::loadHTML($html)
            ->setPaper($paper, $orientation)
            ->setOptions($this->defaultOptions);

        return $pdf->download("{$filename}.pdf");
    }

    /**
     * Generate PDF from Blade template.
     *
     * @param string $view
     * @param array $data
     * @param string $filename
     * @param string $paper
     * @param string $orientation
     * @return Response
     */
    public function generateFromView(
        string $view, 
        array $data, 
        string $filename, 
        string $paper = 'a4', 
        string $orientation = 'portrait'
    ): Response {
        $pdf = Pdf::loadView($view, $data)
            ->setPaper($paper, $orientation)
            ->setOptions($this->defaultOptions);

        return $pdf->download("{$filename}.pdf");
    }

    /**
     * Stream PDF to browser (inline display).
     *
     * @param string $html
     * @param string $filename
     * @param string $paper
     * @param string $orientation
     * @return Response
     */
    public function streamFromHtml(
        string $html, 
        string $filename, 
        string $paper = 'a4', 
        string $orientation = 'portrait'
    ): Response {
        $pdf = Pdf::loadHTML($html)
            ->setPaper($paper, $orientation)
            ->setOptions($this->defaultOptions);

        return $pdf->stream("{$filename}.pdf");
    }

    /**
     * Generate report with standard school header.
     *
     * @param string $title
     * @param string $content
     * @param array $options
     * @return Response
     */
    public function generateReport(string $title, string $content, array $options = []): Response
    {
        $html = $this->buildReportHtml($title, $content, $options);
        $filename = $options['filename'] ?? 'report_' . now()->format('Y-m-d_His');
        $paper = $options['paper'] ?? 'a4';
        $orientation = $options['orientation'] ?? 'portrait';

        return $this->generateFromHtml($html, $filename, $paper, $orientation);
    }

    /**
     * Generate table-based report.
     *
     * @param string $title
     * @param Collection $data
     * @param array $columns
     * @param array $options
     * @return Response
     */
    public function generateTableReport(
        string $title, 
        Collection $data, 
        array $columns = [], 
        array $options = []
    ): Response {
        if (empty($columns) && $data->isNotEmpty()) {
            $columns = array_keys($data->first());
        }

        $tableHtml = $this->buildTableHtml($data, $columns);
        $content = $tableHtml;

        if (!empty($options['summary'])) {
            $content .= $this->buildSummaryHtml($options['summary']);
        }

        return $this->generateReport($title, $content, $options);
    }

    /**
     * Build complete report HTML with header and footer.
     *
     * @param string $title
     * @param string $content
     * @param array $options
     * @return string
     */
    protected function buildReportHtml(string $title, string $content, array $options = []): string
    {
        $schoolName = config('app.name', 'Smart School');
        $schoolAddress = $options['school_address'] ?? '';
        $schoolPhone = $options['school_phone'] ?? '';
        $generatedAt = now()->format('F j, Y \a\t g:i A');
        $subtitle = $options['subtitle'] ?? '';
        $showLogo = $options['show_logo'] ?? true;

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{$title}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }
        .header {
            text-align: center;
            padding: 15px 0;
            border-bottom: 3px solid #4f46e5;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 20px;
            color: #4f46e5;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .header .school-info {
            font-size: 9px;
            color: #666;
            margin-bottom: 10px;
        }
        .header h2 {
            font-size: 14px;
            color: #333;
            font-weight: normal;
            margin-top: 10px;
        }
        .header .subtitle {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }
        .meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 9px;
            color: #666;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .content {
            margin-top: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background-color: #4f46e5;
            color: white;
            padding: 8px 5px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
            border: 1px solid #4f46e5;
        }
        td {
            padding: 6px 5px;
            border: 1px solid #ddd;
            font-size: 9px;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .summary {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .summary h3 {
            font-size: 12px;
            color: #4f46e5;
            margin-bottom: 10px;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 8px;
            color: #666;
        }
        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            text-align: center;
            width: 30%;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
        }
        .page-break {
            page-break-after: always;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .font-bold {
            font-weight: bold;
        }
        .highlight {
            background-color: #fff3cd;
        }
        .success {
            color: #28a745;
        }
        .danger {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{$schoolName}</h1>
        <div class="school-info">{$schoolAddress} {$schoolPhone}</div>
        <h2>{$title}</h2>
        {$subtitle}
    </div>
    
    <div class="meta">
        <span>Generated: {$generatedAt}</span>
    </div>
    
    <div class="content">
        {$content}
    </div>
    
    <div class="footer">
        <p>This report was generated by {$schoolName} Management System</p>
        <p>Generated on {$generatedAt}</p>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Build HTML table from collection data.
     *
     * @param Collection $data
     * @param array $columns
     * @return string
     */
    protected function buildTableHtml(Collection $data, array $columns): string
    {
        if ($data->isEmpty()) {
            return '<p class="text-center">No data available</p>';
        }

        $headerRow = '';
        foreach ($columns as $column) {
            $headerRow .= "<th>" . htmlspecialchars($column) . "</th>";
        }

        $bodyRows = '';
        foreach ($data as $row) {
            $bodyRows .= '<tr>';
            foreach ($columns as $column) {
                $value = is_array($row) ? ($row[$column] ?? '') : ($row->$column ?? '');
                $bodyRows .= "<td>" . htmlspecialchars((string) $value) . "</td>";
            }
            $bodyRows .= '</tr>';
        }

        return <<<HTML
<table>
    <thead>
        <tr>{$headerRow}</tr>
    </thead>
    <tbody>
        {$bodyRows}
    </tbody>
</table>
HTML;
    }

    /**
     * Build summary section HTML.
     *
     * @param array $summary
     * @return string
     */
    protected function buildSummaryHtml(array $summary): string
    {
        $items = '';
        foreach ($summary as $label => $value) {
            $items .= <<<HTML
<div class="summary-item">
    <span>{$label}</span>
    <span class="font-bold">{$value}</span>
</div>
HTML;
        }

        return <<<HTML
<div class="summary">
    <h3>Summary</h3>
    {$items}
</div>
HTML;
    }

    /**
     * Build signature section HTML.
     *
     * @param array $signatures
     * @return string
     */
    public function buildSignatureSection(array $signatures): string
    {
        $boxes = '';
        foreach ($signatures as $title) {
            $boxes .= <<<HTML
<div class="signature-box">
    <div class="signature-line">{$title}</div>
</div>
HTML;
        }

        return <<<HTML
<div class="signature-section">
    {$boxes}
</div>
HTML;
    }

    /**
     * Get PDF as raw content (for saving to file).
     *
     * @param string $html
     * @param string $paper
     * @param string $orientation
     * @return string
     */
    public function getPdfContent(string $html, string $paper = 'a4', string $orientation = 'portrait'): string
    {
        $pdf = Pdf::loadHTML($html)
            ->setPaper($paper, $orientation)
            ->setOptions($this->defaultOptions);

        return $pdf->output();
    }

    /**
     * Save PDF to storage.
     *
     * @param string $html
     * @param string $path
     * @param string $disk
     * @param string $paper
     * @param string $orientation
     * @return bool
     */
    public function savePdf(
        string $html, 
        string $path, 
        string $disk = 'local', 
        string $paper = 'a4', 
        string $orientation = 'portrait'
    ): bool {
        $content = $this->getPdfContent($html, $paper, $orientation);
        return \Illuminate\Support\Facades\Storage::disk($disk)->put($path, $content);
    }
}
