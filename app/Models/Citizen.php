<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable([
    'national_id',
    'full_name',
    'birth_place',
    'birth_date',
    'gender',
    'address',
    'phone_number',
    'email',
    'password',
])]
#[Hidden([
    'password',
])]
class Citizen extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Table name.
     */
    protected $table = 'citizens';

    /**
     * Primary key.
     */
    protected $primaryKey = 'citizen_id';

    /**
     * Primary key type.
     */
    protected $keyType = 'int';

    /**
     * Auto increment.
     */
    public $incrementing = true;


    /**
     * Cast attributes.
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'birth_date' => 'date',
        ];
    }

    public function letterRequests()
    {
        return $this->hasMany(LetterRequest::class, 'citizen_id', 'citizen_id');
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class, 'citizen_id', 'citizen_id');
    }

    public function bookLoans()
    {
        return $this->hasMany(BookLoan::class, 'citizen_id', 'citizen_id');
    }
}
