# phpfilemanager

Install:

```
composer require siestacat/upload-api-client
```

Usage:

```
use Siestacat\PhpUploadApiClient\Client;

$client = new Client('http://localhost/api', 'changeme');

$upload_token = $client->request();

//<<Here the client upload the files from browser>>

//Get the uploaded files:

// Siestacat\PhpUploadApiClient\File[]
$files = $client->getFiles($upload_token);

//Download single file. Get the tmp path of downloaded file:

$downloaded_file = $client->download($upload_token, $file[0]->hash);

```


Tests:

```
git clone https://github.com/SiestaCat/php-upload-api-client.git
cd php-upload-api-client
composer install
composer run-script test
```
