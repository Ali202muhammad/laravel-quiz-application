<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;


use Maatwebsite\Excel\Concerns\FromCollection;

class ExamResultsExport implements FromView, WithMapping, WithHeadings
{
    protected $results;

    public function __construct($results)
    {
        $this->results = $results;
    }

    public function view(): View
    {
        return view('exports.exam-results', [
            'results' => $this->results,
            'headings' => $this->headings()
        ]);
    }

    public function map($result): array
    {
        return [
            $result->username,
            $result->exam_name,
            $result->name,
            $result->questions,
            $result->ans,
            $result->options,
            $result->result_json,
        ];
    }

    public function headings(): array
    {
        return [
            'User Name',
            'Exam Name',
            'Department Name',
            'Question',
            'Answers',
            'Selected Option',
            'Results',
        ];
    }
}
