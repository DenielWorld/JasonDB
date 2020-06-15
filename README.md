# JasonDB

**A better way to store your J~~a~~SON**

## Why JasonDB?

Most proper databases that PMMP users usually hear of are
mainly SQL based. Barely anyone within our community
knows of MongoDB as a database which is formatted in 
JSON-style and is based on a NoSQL principle. 
This library wraps around MongoDB!

This library is named JasonDB in honor of a great plugin
developer (@jasonwynn10), and also for the meme of it.
You should totally check out Jason's plugins and spam
him about plot merging!

## Pros

* Much faster than storing and using data with a JSON/YAML 
file, and is more healthy for RAM.
* The DBs are wrapped around a Config-like wrapper, making
the transfer from Config databases to JasonDB easy.
* Support for asynchronous handling of the DBs (you should
only consider such for REALLY BIG data retrievals/changes).
* Insert (set/remove) speed is highly superior to MySQL.
* If you are transferring from a Config to this, you get to
remove all the $config->save() stuff. 
* Supports PM 3.0 and PM 4.0.

## Cons

* Handled a bit differently from Configs.
* On a VERY LARGE scale, selection (get) is slower than
MySQL, but is still only a matter of milliseconds.

## Tests

Once tests of the lib are complete, this section will be
filled with more information such as functionality speed 
& bugs.

## API

To initialize the API on the main thread, you can execute
the following code.

```php
\DenielWorld\JasonDB\JasonDB::init();
```

To initialize the API on a new thread/asynchronously,
you can use the following code.

```php
\DenielWorld\JasonDB\DatabaseManager::init();
```

Another way to initialize the API asynchronously would
involve making a task with the following code.

```php
class ExampleAsyncDBTask extends DenielWorld\JasonDB\task\AsyncDBTask{

    public function onRun() : void{
        parent::onRun(); //This line is very important for functionality!

        //Database handling code - Setting, Removing, Getting, whatever you want.
    }

}
```

To create a new database or retrieve a database, use the
following code.

```php
$db = \DenielWorld\JasonDB\DatabaseManager::getDatabase("exampleDb");
```

If the database does not exist, it will be created and then
returned. If it does exist, it will be returned.

If you don't want to make extra databases, you can always
use the default one using the following code.

```php
$defaultDb = \DenielWorld\JasonDB\DatabaseManager::getDefaultDatabase();
```

To create a new collection within a Database, which can then
be manipulated similarly to your everyday Config, you can use
the following code.

```php
//$db has been referenced in a previous example.
$collection = $db->createCollection("exampleCollection");
```

Even though createCollection() in the previous example
code returns the CollectionWrapper, if you ever need to
retrieve the collection from the DB again, you can use
the following code.

```php
//$db has been referenced in a previous example.
$collection = $db->getCollection("exampleCollection");
```

Now that you have a collection created, you can manipulate
it as you would a Config, because it has all of the Config's
most used methods. Below are examples of all the methods
in the collection, assuming that we are making it to store
player data.
```php
//$collection has been referenced in a previous example.

//This returns your data in the following format:
//["key" => "value"]
//Value can be anything.
//If the first parameter was true, an array of all the...
//keys in the collection would be returned, like this:
//["key", "key"]
$collection->getAll(false);

//If data with the given key "DenielWorld" exists,
//the data will be returned.
//Otherwise, the second parameter is returned, in this case:
//null
$collection->get("DenielWorld", null);

//If data with the key "DenielWorld" exists,...
//and that data has a key called "rank", then whatever is...
//behind the "rank" key will be returned. The nesting level...
//is unlimited.
//Otherwise, the second parameter is returned, in this case:
//false
$collection->getNested("DenielWorld.rank", false);

//All of the existing keys' data is changed to the first 
//parameter you provide.
$collection->setAll(["rank" => "Guest"]);

//The data behind the key "DenielWorld" will turn into
//the second parameter.
$collection->set("DenielWorld", ["rank" => "Owner"]);

//The data with the key "DenielWorld" will have another key...
//("rank") within itself set to "Owner".
$collection->setNested("DenielWorld.rank", "Owner");

//If data with the key "DenielWorld" exists, it will...
//be removed from the collection.
$collection->remove("DenielWorld");

//If data with the key "DenielWorld" exists, and that...
//data has a key called "level" within itself, then that...
//key gets removed.
$collection->removeNested("DenielWorld.level");

//If data with a key "DenielWorld" exists, then will...
//return true. Otherwise will return false.
$collection->exists("DenielWorld");

//If data with a key "DenielWorld" exists, and that data...
//has a key called "level" within itself, then will return...
//true. Otherwise will return false.
$collection->exists("DenielWorld.level");
```

## Extended API

In the following section, some of the more advanced parts of
the API will be showcased.

By default, the DatabaseManager always attempts to manage
data from your local MongoDB.

Although, you can create additional MongoDB connections,
using the following code.

```php
\DenielWorld\JasonDB\DatabaseManager::createAdditionalClient("exampleConnection", "mongodb://localhost:27017", ["connect" => TRUE]);
```

The first parameter is the name you wish to give your new
connection. You may use this name later on in your code to
retrieve databases from this connection.

The second parameter is an encoded URL for your connection.
Don't know how to encode URLs? Not a problem, because PHP
has a handy function for that called ``rawurlencode()``.
The first and only parameter of that function is your URL,
easy enough?

The third parameter is additional URL options for your
connection, you should only mess with that if you know
what you are doing.

After your new connection is established, you may create
and retrieve databases from it. The following code will
show an example.

```php
\DenielWorld\JasonDB\DatabaseManager::getDatabaseFrom("exampleConnection", "testDb");
```

In the code above, the first parameter is the name of your
newly established MongoDB connection.

The second parameter is the name of a database that you
either want to create or retrieve from the connection.
Either way, the according DatabaseWrapper will be returned.

At last, I would like to underline that for safety reasons,
the CollectionWrapper will not throw any exceptions, which
is why if your code does not work properly, you won't know
exactly why.

If you need to debug your code and see what doesn't work
properly with your CollectionWrapper, you can use the
following code.

```php
//$collection has been referenced in a previous example.
$errorMessage = $collection->getLastError();
```

When an exception is caught by the CollectionWrapper, a
complete error message of that exception is logged, and
can be retrieved with the method shown above. Using that,
you may find out where your last error has occurred and
for what reason, and if it turns out to be a bug on the
end of JasonDB, you can always open an issue to report it.