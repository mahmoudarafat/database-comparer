# database-comparer
### simply compare two databases to get
1. New tables in first db
2. New tables in second db
3. New columns in existing tables in both dbs

### to do
1. Get datatype changes in columns

## how to use
1. Add the Database folder to yoour laravel project in
'App/Services'
2. Create this route with exact http type and content
``` php
Route::match(['get', 'post'], 'compare-database', function () {
    return App\Services\Database\CompareChainer::index();
});
```
3. Goto the url 
4. Type in the database connections
5. Compare and see the results 
