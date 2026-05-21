<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Requirement extends Model
{
    protected $fillable = ['applicant_id', 'document_name', 'status', 'expiration_date'];
}
