<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $full_name
 * @property string $email
 * @property string $contact_number
 * @property string $address
 * @property string $password
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Applicant extends Model
{
    protected $fillable = ['full_name', 'email', 'contact_number', 'address', 'password'];
}
