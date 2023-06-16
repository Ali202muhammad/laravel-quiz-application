<?php

namespace App\Http\Controllers;
use App\Models\user_exam;
use App\Models\User;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExamResultsExport;
use App\Models\Oex_question_master;
use App\Models\Oex_category; 
use App\Models\Oex_exam_master;
use App\Models\Oex_result;
use Illuminate\Support\Facades\DB;


use Illuminate\Http\Request;

class ExamResultsController extends Controller
{
    public function download($examId)
    {
        $results = DB::table('oex_results')
            ->join('users', 'oex_results.user_id', '=', 'users.id')
            ->join('oex_exam_masters', 'oex_results.exam_id', '=', 'oex_exam_masters.id')
            ->join('oex_categories', 'oex_exam_masters.category', '=', 'oex_categories.id')
            ->join('oex_question_masters', 'oex_question_masters.exam_id', '=', 'oex_results.exam_id')
            ->select(
                'users.name as username',
                'oex_exam_masters.title as exam_name',
                'oex_categories.name',
                'oex_question_masters.questions',
                'oex_question_masters.ans',
                'oex_question_masters.options',
                'oex_results.result_json'
            )
            ->get();
            $formattedResults = [];
            foreach($results as $item){
                foreach(json_decode($item->result_json, true) as $item2){
                    $formattedResults[] = [
                        $item->username,
                        $item->exam_name,
                        $item->name,
                        $item->questions,
                        $item->ans,
                        $item->options,
                        $item2,
                    ];
                }
            }
        
        // $results = $exam->results()->with('user')->get();

        $fileName = 'exam_results_' . '.xlsx';

        return Excel::download(new ExamResultsExport($formattedResults), $fileName);
    }

}
