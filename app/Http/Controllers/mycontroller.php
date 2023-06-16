<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Exam;

class mycontroller extends Controller
{
    public function index(){
        return Excel::download(new UsersExport, 'users.xlsx');
    }

    public function download($examId)
    {
        $exam = Exam::findOrFail($examId);
        $results = $exam->results()->with('user')->get();

        $fileName = 'exam_results_' . $exam->name . '.xlsx';

        return Excel::download(new ExamResultsExport($results), $fileName);
    }
}
