<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'specialty_id',
    ];

    /**
     * Get the appointments for the doctor.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get the specialty that the doctor belongs to.
     */
    public function specialty(): BelongsTo // <--- Ahora 'BelongsTo' se refiere a la clase importada
    {
        return $this->belongsTo(Specialty::class);
    }

    // ... otras relaciones o métodos (si tienes)
}