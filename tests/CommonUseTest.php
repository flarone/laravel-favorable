<?php

namespace Flarone\Tests\Favoriteable;

use Flarone\Favoriteable\Favorite;
use Illuminate\Database\Eloquent\Model;
use Flarone\Favoriteable\Favoriteable;
use Flarone\Favoriteable\FavoriteCounter;

class CommonUseBaseTest extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Model::unguard();
    }
    
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        \Schema::create('books', function ($table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->timestamps();
        });
    }
    
    public function tearDown(): void
    {
        \Schema::drop('books');
    }

    public function test_basic_favorite()
    {
        /** @var Stub $stub */
        $stub = Stub::create(['name'=>123]);

        $stub->favorite();
        
        $this->assertEquals(1, $stub->favoriteCount);
    }
    
    public function test_multiple_favorites()
    {
        $stub = Stub::create(['name'=>123]);
        
        $stub->favorite(1);
        $stub->favorite(2);
        $stub->favorite(3);
        $stub->favorite(4);
        
        $this->assertEquals(4, $stub->favoriteCount);
    }
    
    public function test_defavorite()
    {
        /** @var Stub $stub */
        $stub = Stub::create(['name'=>123]);
        
        $stub->defavorite(1);
        
        $this->assertEquals(0, $stub->favoriteCount);
    }
    
    public function test_where_favorited_by()
    {
        Stub::create(['name'=>'A'])->favorite(1);
        Stub::create(['name'=>'B'])->favorite(1);
        Stub::create(['name'=>'C'])->favorite(1);
        
        $stubs = Stub::whereFavoritedBy(1)->get();
        $shouldBeEmpty = Stub::whereFavoritedBy(2)->get();
        
        $this->assertEquals(3, $stubs->count());
        $this->assertEmpty($shouldBeEmpty);
    }
    
    public function test_deleteModel_deletesFavorites()
    {
        /** @var Stub $stub1 */
        $stub1 = Stub::create(['name'=>456]);
        /** @var Stub $stub2 */
        $stub2 = Stub::create(['name'=>123]);
        /** @var Stub $stub3 */
        $stub3 = Stub::create(['name'=>888]);
        
        $stub1->favorite(1);
        $stub1->favorite(7);
        $stub1->favorite(8);
        $stub2->favorite(1);
        $stub2->favorite(2);
        $stub2->favorite(3);
        $stub2->favorite(4);

        $stub3->delete();
        $this->assertEquals(7, Favorite::count());
        $this->assertEquals(2, FavoriteCounter::count());

        $stub1->delete();
        $this->assertEquals(4, Favorite::count());
        $this->assertEquals(1, FavoriteCounter::count());

        $stub2->delete();
        $this->assertEquals(0, Favorite::count());
        $this->assertEquals(0, FavoriteCounter::count());
    }
    
    public function test_rebuild_test()
    {
        $stub1 = Stub::create(['name'=>456]);
        $stub2 = Stub::create(['name'=>123]);

        $stub1->favorite(1);
        $stub1->favorite(7);
        $stub1->favorite(8);
        $stub2->favorite(1);
        $stub2->favorite(2);
        $stub2->favorite(3);
        $stub2->favorite(4);
        
        FavoriteCounter::truncate();
        
        FavoriteCounter::rebuild(Stub::class);

        $this->assertEquals(2, FavoriteCounter::count());
    }
}

/**
 * @mixin \Eloquent
 */
class Stub extends Model
{
    use Favoriteable;
    
    public $table = 'books';
}
