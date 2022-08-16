# AWS-TEST

## Description
This project is created to learn basic usage of aws filesystem.

## Setup
```shell
docker-compose up
docker exec -it app bash
composer i
```

Copy .env.example to .env.

```shell
php artisan key:generate
```

Go to http://localhost:9001 and create service user, copy keys to .env file.  
Done.  
## Usage
To upload file, send it as POST multipart request with file on endpoint `localhost/api/files`.  
To look for all files make GET request on `localhost/api/files`.  
To download file make GET request on `localhost/api/files/your_file_name.txt`.
