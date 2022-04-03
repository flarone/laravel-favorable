<?php

namespace Flarone\Favoriteable;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * @mixin \Eloquent
 * @property Favoriteable favoriteable
 * @property string user_id
 * @property string favoriteable_id
 * @property string favoriteable_type
 */
class Favorite extends Eloquent
{
    protected $table = 'favoriteable_favorites';
    public $timestamps = true;
    protected $fillable = ['favoriteable_id', 'favoriteable_type', 'user_id'];

    /**
     * @access private
     */
    public function favoriteable()
    {
        return $this->morphTo();
    }
}
