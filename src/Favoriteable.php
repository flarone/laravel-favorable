<?php

namespace Flarone\Favoriteable;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * @method static Builder whereFavoritedBy($userId=null)
 * @property Collection|Favorite[] favorites
 * @property Favorited favorited
 * @property integer favoriteCount
 */
trait Favoriteable
{
    public static function bootFavoriteable()
    {
        if (static::removeFavoritesOnDelete()) {
            static::deleting(function ($model) {
                /** @var Favoriteable $model */
                $model->removeFavorites();
            });
        }
    }
    
    /**
     * Populate the $model->favorites attribute
     */
    public function getFavoriteCountAttribute()
    {
        return $this->favoriteCounter ? $this->favoriteCounter->count : 0;
    }

    /**
     * Add a favorite for this record by the given user.
     * @param $userId mixed - If null will use currently logged in user.
     */
    public function favorite($userId=null)
    {
        if (is_null($userId)) {
            $userId = $this->loggedInUserId();
        }
        
        if ($userId) {
            $favorite = $this->favorites()
                ->where('user_id', '=', $userId)
                ->first();
    
            if ($favorite) {
                return;
            }
    
            $favorite = new Favorite();
            $favorite->user_id = $userId;
            $this->favorites()->save($favorite);
        }

        $this->incrementFavoriteCount();
    }

    /**
     * Remove a favorite from this record for the given user.
     * @param $userId mixed - If null will use currently logged in user.
     */
    public function defavorite($userId=null)
    {
        if (is_null($userId)) {
            $userId = $this->loggedInUserId();
        }
        
        if ($userId) {
            $favorite = $this->favorites()
                ->where('user_id', '=', $userId)
                ->first();
    
            if (!$favorite) {
                return;
            }
    
            $favorite->delete();
        }

        $this->decrementFavoriteCount();
    }
    
    /**
     * Has the currently logged in user already "favorited" the current object
     *
     * @param string $userId
     * @return boolean
     */
    public function favorited($userId=null)
    {
        if (is_null($userId)) {
            $userId = $this->loggedInUserId();
        }
        
        return (bool) $this->favorites()
            ->where('user_id', '=', $userId)
            ->count();
    }
    
    /**
     * Should remove favorites on model row delete (defaults to true)
     * public static removeFavoritesOnDelete = false;
     */
    public static function removeFavoritesOnDelete()
    {
        return isset(static::$removeFavoritesOnDelete)
            ? static::$removeFavoritesOnDelete
            : true;
    }
    
    /**
     * Delete favorites related to the current record
     */
    public function removeFavorites()
    {
        $this->favorites()->delete();
        $this->favoriteCounter()->delete();
    }


    /**
     * Collection of the favorites on this record
     * @access private
     */
    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoriteable');
    }

    /**
     * Did the currently logged in user favorite this model
     * Example : if($book->favorited) { }
     * @return boolean
     * @access private
     */
    public function getFavoritedAttribute()
    {
        return $this->favorited();
    }

    /**
     * Counter is a record that stores the total favorites for the
     * morphed record
     * @access private
     */
    public function favoriteCounter()
    {
        return $this->morphOne(FavoriteCounter::class, 'favoriteable');
    }

    /**
     * Private. Increment the total favorite count stored in the counter
     */
    private function incrementFavoriteCount()
    {
        $counter = $this->favoriteCounter()->first();

        if ($counter) {
            $counter->count++;
            $counter->save();
        } else {
            $counter = new FavoriteCounter;
            $counter->count = 1;
            $this->favoriteCounter()->save($counter);
        }
    }

    /**
     * Private. Decrement the total favorite count stored in the counter
     */
    private function decrementFavoriteCount()
    {
        $counter = $this->favoriteCounter()->first();

        if ($counter) {
            $counter->count--;
            if ($counter->count) {
                $counter->save();
            } else {
                $counter->delete();
            }
        }
    }


    /**
     * Fetch records that are favorited by a given user.
     * Ex: Book::whereFavoritedBy(123)->get();
     * @access private
     */
    public function scopeWhereFavoritedBy($query, $userId=null)
    {
        if (is_null($userId)) {
            $userId = $this->loggedInUserId();
        }

        return $query->whereHas('favorites', function ($q) use ($userId) {
            $q->where('user_id', '=', $userId);
        });
    }

    /**
     * Fetch the primary ID of the currently logged in user
     * @return mixed
     */
    private function loggedInUserId()
    {
        return Auth()->id();
    }
}
