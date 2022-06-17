<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HideableColumn extends Model
{
    use HasFactory;

    protected $fillable = ['table_name', 'column_name', 'index', 'user_id'];

    /**
     * Get the user that owns the HideableColumn
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
