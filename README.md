# check_existence_of_file_in_remote_server

## What is it?
- tool to check if file inside a local directory exist in a remote HTTP server or not
- example: a deployment server called "mysite.com" was developed from local machine, but for some reason some image assets have been partially uploaded; you need to know which images have not been uploaded yet and only upload the differences 
- uses HTTP method ```HEAD``` for faster load time

## Usage
example usage: ```php check_existence_of_file_in_remote_server.php -l/tmp/images -rhttp://mysite.com/images```
