<?php

namespace LaravelPropertyBag\tests\Classes;

use Illuminate\Database\Eloquent\Model;

class GroupPropertyBag extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'group_id',
        'key',
        'value'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'value' => 'array'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'group_settings';

    /**
     * A setting belongs to a user.
     *
     * @return BelongsTo
     */
    public function groups()
    {
        return $this->belongsTo(Group::class);
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
