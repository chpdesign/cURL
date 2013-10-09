cURL
====

php cURL class

I think it's not to be so fat and hard to use; but an example:

```php
require_once 'curl.class.php';
$curl = new cURL(); // get google.com port 80 (default)
$curl->setHeaders(
  array(
    'Content-Type: application/x-www-form-urlencoded; charset=UTF-8'
    //, '...', '...'
  )
); // set header for example
$curl->followLocation(true); // follow location
$curl->setTimeOut(5); // wait for
$curl->execute(); // run/execute the get/post
$data = $curl->getBody(); // get the body
```
