<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property string $interview_date
 * @property string $company_name
 * @property string $venue
 * @property string $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Interview extends Model
{
    protected $fillable = ['user_id', 'interview_date', 'company_name', 'venue', 'status'];
}
