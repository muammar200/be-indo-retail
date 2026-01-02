<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FcmToken extends Model
{
     protected $fillable = ['user_id', 'token']; // Mendefinisikan kolom yang dapat diisi melalui mass assignment (user_id dan token)
}
