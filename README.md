Laravel Favoriteable
============

Laravel favorable package to like, dislike and favorite your models.

#### Composer Install

    composer require flarone/laravel-favorable

#### Then run the migrations

```bash
php artisan migrate
```

#### Setup your models

```php
class Article extends \Illuminate\Database\Eloquent\Model {
	use \Flarone\Favoriteable\Favoriteable;
}
```

#### Sample Usage

```php
$model->favorite(); // favorite the model for current user
$model->favorite($myUserId); // pass in your own user id
$model->favorite(0); // just add favorites to the count, and don't track by user

$model->defavorite(); // remove favorite from the model
$model->defavorite($myUserId); // pass in your own user id
$model->defavorite(0); // remove favorites from the count -- does not check for user

$model->favoriteCount; // get count of favorites

$model->favorites; // Iterable Illuminate\Database\Eloquent\Collection of existing favorites

$model->favorited(); // check if currently logged in user favorited the model
$model->favorited($myUserId);

Model::whereFavoritedBy($myUserId) // find only models where user favorited them
	->with('favoriteCounter') // highly suggested to allow eager load
	->get();
```

#### Credits

 - Flarone - https://flarone.com
