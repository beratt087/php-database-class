# PHP Database Class

This is a simple PHP class for connecting to a database and executing queries.

## Installation

First of all you need to install the dependencies using composer:

```php
composer install
```

## Create .env file

Copy the .env.example file to .env and fill in the database credentials.

## Usage

Here is a simple example of how to use the class:

```php
    require_once __DIR__ . '/vendor/autoload.php';
    use beratt087\Database\Database;
    
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
    $db = new Database();

    // get all data from accounts table
    $data = $db->table('accounts')->get();
    
    // get a data with where condition
    $data = $db->table('accounts')->where('id', '1')->first();
    
    // data will be return as stdClass
    // for access data: $data->age;
    
    // update data (returns affected row count)
    $data = $db->table('accounts')->update([
        'age' => 21
    ]);
    
    // insert data (returns boolean)
    $data = $db->table('accounts')->insert([
        'username' => 'berat',
        'age' => 19
    ]);
    
    // get data count
    $data = $db->table('accounts')->where('age', 21)->getCount();
    
    // delete data (returns affected row count)
    $data = $db->table('accounts')->where('id', 1)->delete();

```



     
