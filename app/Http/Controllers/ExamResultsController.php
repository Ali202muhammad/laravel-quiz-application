<?php
namespace App\Http\Controllers;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use App\Exports\ExamResultsExport;

class ExamResultsController extends Controller
{
    public function download()
    {
        // Retrieve data and format results
        $results = DB::table('oex_results')
            ->join('user_exams', 'oex_results.exam_id', '=', 'user_exams.exam_id')
            ->join('users', 'user_exams.user_id', '=', 'users.id')
            ->join('oex_exam_masters', 'oex_results.exam_id', '=', 'oex_exam_masters.id')
            ->join('oex_categories', 'oex_exam_masters.category', '=', 'oex_categories.id')
            ->select(
                'users.name',
                'oex_exam_masters.id AS exam_id',
                'oex_exam_masters.title AS Exam Name',
                'oex_categories.name AS Department Name',
                'oex_results.result_json'
            )
            ->get();

        $formattedResults = [];
        foreach ($results as $result) {
            $answers = json_decode($result->result_json, true);
            $questions = DB::table('oex_question_masters')
                ->where('exam_id', $result->exam_id)
                ->get();

            foreach ($answers as $questionId => $answer) {
                $question = $questions[$questionId]->questions ?? '';
                $correctAnswer = $questions[$questionId]->ans ?? '';
                $options = json_decode($questions[$questionId]->options, true) ?? [];

                $formattedResults[] = [
                    'User name' => $result->name,
                    'Exam Name' => $result->{'Exam Name'},
                    'Department Name' => $result->{'Department Name'},
                    'Question' => $question,
                    'Answer' => $answer,
                    'Correct Answer' => $correctAnswer,
                    'Options' => implode(', ', $options),
                ];
            }
        }

        $headings = [
            'User name',
            'Exam Name',
            'Department Name',
            'Question',
            'Answer',
            'Correct Answer',
            'Options',
        ];

        // dd(array_values(array_unique($formattedResults)));

        return Excel::download(new ExamResultsExport($formattedResults, $headings), 'exam_results.xlsx');
    }
}