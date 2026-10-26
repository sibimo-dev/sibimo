<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['classification_index', 'year', 'last_sequence'])]
class LetterClassificationSequence extends Model
{
    use HasFactory;

    protected $table = 'letter_classification_sequences';
    protected $primaryKey = 'classification_sequence_id';
    public $timestamps = false;
    const CREATED_AT = null;
    const UPDATED_AT = 'updated_at';
}
