<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $primaryKey = 'courseId';

    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'courseusers');
    }

    public function createdUser()
    {
        $created_user = User::find($this->createdUserId);
        return $created_user->firstname.' '.$created_user->lastname;
    }

    public function problems()
    {
        return $this->belongsToMany('App\Models\Problem', 'courseproblems', 'courseId', 'problemId')->withPivot('courseProblemId', 'scoreInCourse', 'hardLevel', 'isActive')->wherePivot('isActive',1);
//        return $this->belongsToMany('App\Models\Problem', 'courseproblems', 'courseId', 'problemId')->where('courseproblems.isActive',1);
    }
    
    public function exams()
    {
        return $this->hasMany('App\Models\Exam','courseId');
    }

    

    public function semester(){
        return $this->belongsTo('App\Models\Semester', 'semesterId');
    }
}
