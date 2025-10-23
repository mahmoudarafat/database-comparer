# database-comparer
### simply compare two databases to get
1. New tables in first db
2. New tables in second db
3. New columns in existing tables in both dbs

## how to use
1. Add the Database folder to yoour laravel project in
'app/Services' folder
2. Create this route with exact http type and content
``` php
Route::match(['get', 'post'], 'compare-database', function () {
    return App\Services\Database\CompareChainer::index();
});
```
3. Goto the url 
4. Type in the database connections
5. Compare and see the results 

## Applying directly:
1. add this route to your routes:
``` php
Route::post('db-compare-apply', function () {
    return App\Services\Database\CompareChainer::applyUpdates();
})->name('db-compare-apply');
```
2. click Apply for needed section.

## New in the latest Release:
1. Migration file for each section with ability to copy the content or ownload a generated file.
2. Ability to auto update default values.
3. Ability to auto apply directly without manual confirm, just with sumit action.

``` php
Regards,
Mahmoud Arafat
```

