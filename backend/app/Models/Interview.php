<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    protected $fillable = ['applicant_id', 'interview_date', 'company_name', 'venue', 'status'];
}
