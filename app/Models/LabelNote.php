<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabelNote extends Model
{
    use HasFactory;
    protected $fillable = ['id','label_id','user_id','note_id'];
}
