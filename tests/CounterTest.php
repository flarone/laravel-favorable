<?php

namespace Flarone\Tests\Favoriteable;

use Illuminate\Database\Eloquent\Model;
use Mockery as m;
use Flarone\Favoriteable\Favoriteable;

class CounterBaseTest extends BaseTestCase
{
    public function testFavorite()
    {
        $favoriteable = m::mock('Flarone\Tests\Favoriteable\FavoriteableStub[incrementFavoriteCount]');
        $favoriteable->shouldReceive('incrementFavoriteCount')->andReturn(null);
        
        $favoriteable->favorite(0);
    }
    
    public function testDefavorite()
    {
        $favoriteable = m::mock('Flarone\Tests\Favoriteable\FavoriteableStub[decrementFavoriteCount]');
        $favoriteable->shouldReceive('decrementFavoriteCount')->andReturn(null);
        
        $favoriteable->defavorite(0);
    }
}

class FavoriteableStub extends Model
{
    use Favoriteable;

    public function incrementFavoriteCount()
    {
    }
    public function decrementFavoriteCount()
    {
    }
}
