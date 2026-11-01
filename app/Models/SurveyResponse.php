<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyResponse extends Model
{
    use HasFactory;

    protected $table = 'survey_responses';

    protected $primaryKey = 'survey_response_id';

    protected $fillable = [
        'service_type',
        'age',
        'gender',
        'education',
        'occupation',
        'answers',
        'average_score',
        'suggestion',
    ];

    protected function casts(): array
    {
        return [
            'age' => 'integer',
            'answers' => 'array',
            'average_score' => 'decimal:2',
        ];
    }
}
