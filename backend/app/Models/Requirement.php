<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property string $document_name
 * @property string $status
 * @property string|null $file_path
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Requirement extends Model
{
    protected $fillable = ['user_id', 'document_name', 'status', 'file_path', 'expiration_date'];
}
