# groka

your own personal google

simple frontend/backend which mades web fulltext search engine out of [Groonga][1] server.

## install

1. [install Groonga][2]
2. create new database - `groonga -n /path/to/db`
3. start groonga http server - `groonga -d --protocol http /path/to/db`
4. copy `config.sample.php` to `config.php` and change what you want
5. add something to index - `php cmd/add.php http://example.org/`
6. run `php -S localhost:80` and search for something

## scripts in `cmd` dir

* `add.php` - adds URL specified as argument to index
* `multiadd.php` - adds URLs from STDIN to index 
* `getlinks.php` - get all links from specified URL
* `remove.php` - removes URL from index
* `setup.php` - setups groka database

## demo instance?

See http://groka.svita.cz/

It only search in certain parts of the internet, namely some sections of [tildeverse][].

## name?

It's named after [The Groke][3] from Moomins books. Initialy chosen because of name similarity to Groonga, but it has 
deeper meaning. 

As Google is evil, trying to make your own Google may be consideret evil too. But Groka only looks evil, it's actually 
innocent - as in Moomins books.

[1]: http://groonga.org/
[2]: http://groonga.org/docs/install.html
[3]: https://en.wikipedia.org/wiki/The_Groke
[tildeverse]: https://tildeverse.org/