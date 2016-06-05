<?php

namespace LaravelPropertyBag;

use Illuminate\Database\Eloquent\Model;

class UserPropertyBag extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'key',
        'value'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_settings';

    /**
     * A setting belongs to a user.
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get settings id.
     *
     * @return int
     */
    public function id()
    {
        return $this->id;
    }
}
