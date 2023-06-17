<?php
namespace App\Exports;use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\View\View;

class ExamResultsExport implements FromView, WithHeadings
{
    private $results;
    private $headings;

    public function __construct($results, $headings)
    {
        $this->results = $results;
        $this->headings = $headings;
    }

    public function view(): View
    {
        return view('exports.exam_results', [
            'results' => $this->results,
            'headings' => $this->headings,
        ]);
    }

    public function headings(): array
    {
        return $this->headings;
    }
}