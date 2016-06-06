<?php

namespace LaravelPropertyBag\UserSettings;

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
    protected $table = 'user_property_bag';

    /**
     * A setting belongs to a user.
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
