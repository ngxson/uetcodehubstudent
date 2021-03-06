<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class ExamController extends Controller
{
    public function showExamCourses()
    {
        //$exams = Exam::all();
        $exams = array();
        $courses = Auth::user()->courses();
        //$exams = $courses->get()[1]->exams()->get();
        $size = sizeof($courses->get());
        for ($i=0; $i<$size; $i++){
            $size_exam = sizeof($courses->get()[$i]->exams()->get());
            for($j=0; $j<$size_exam; $j++){
                if($courses->get()[$i]->exams()->get()[$j]->isActive){
                    array_push($exams, $courses->get()[$i]->exams()->get()[$j]);
                }
            }
        }
        return view('exam.showExamCourses', compact('exams','courses'));
    }

    public function startExam($examId){
        $exam = Exam::find($examId);

        if(!$exam->hasJoin(Auth::user()->userId)){
            $user = Auth::user();
            $user->exams()->attach($examId);
        }
        
        //$now = strtotime ((new \DateTime('now', new \DateTimeZone('Asia/Ho_Chi_Minh')))->format('Y-m-d H:i:s'));
		$now = strtotime ((new \DateTime('now'))->format('Y-m-d H:i:s'));
        $joinTime = strtotime($exam->joinTime(Auth::user()->userId));
        $time = $now - $joinTime;
        $remainTime = $exam->duration * 60 - $time;
        
        $problems = $exam->problems;
        return view('exam.showExamDetail', compact('exam', 'problems', 'remainTime'));
    }


    public function showExamDetail($examId)
    {
        $exam = Exam::find($examId);
        $problems = $exam->problems;
		
		//calculate time
		//$now = strtotime ((new \DateTime('now', new \DateTimeZone('Asia/Ho_Chi_Minh')))->format('Y-m-d H:i:s'));
		$now = strtotime ((new \DateTime('now'))->format('Y-m-d H:i:s'));
        $joinTime = strtotime($exam->joinTime(Auth::user()->userId));
        $time = $now - $joinTime;
        $remainTime = $exam->duration * 60 - $time;
		
        return view('exam.showExamDetail', compact('examId', 'problems', 'remainTime'));
    }

    public function showProblemDetail($examId, $problemId)
    {
		$isExam = true;
        $exam = Exam::find($examId);
        $problems = $exam->problems;
        $problem = $problems->find($problemId);
        $submissions = $problem->submissions;
		
		//calculate time
		//$now = strtotime ((new \DateTime('now', new \DateTimeZone('Asia/Ho_Chi_Minh')))->format('Y-m-d H:i:s'));
		$now = strtotime ((new \DateTime('now'))->format('Y-m-d H:i:s'));
        $joinTime = strtotime($exam->joinTime(Auth::user()->userId));
        $time = $now - $joinTime;
        $remainTime = $exam->duration * 60 - $time;
		
        return view('problemDetails.showProblemDetail', compact('isExam', 'examId', 'problem', 'submissions', 'remainTime'));
    }

    /*public function countDown($examId){
        $exam = Exam::find($examId);
//        $now = strtotime ((new \DateTime('now', new \DateTimeZone('Asia/Ho_Chi_Minh')))->format('Y-m-d H:i:s'));
        $now = strtotime ((new \DateTime('now'))->format('Y-m-d H:i:s'));
        $joinTime = strtotime($exam->joinTime(Auth::user()->userId));
        $time = $now - $joinTime;
        $remainTime = $exam->duration * 60 - $time;

        return view('exam.timeCountdown',compact('remainTime'));
    }*/
}
