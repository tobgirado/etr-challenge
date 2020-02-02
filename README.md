# etr-challenge

# Instructions

1. Create a new file called ```config.php``` inside of ```config\``` and copy the contents of ```onfig-sample.php```. Inside there you'll find:
    1. ```"SHOPWARE_URL"```: the URL for the shopware ecommerce.
    2. ```"SHOPWARE_CLIENT_ID"```: shopware user.
    3. ```"SHOPWARE_CLIENT_SECRET"```: shopware user's API Key.

2. The source of the file to import can be a file or an URL. To specify it, you should run
    ```php shopware-import.php``` and add ```--file=path\filename.json``` or ```--url="http:\\url.here.com"```. **The only supported format is json.**
    3. Logging: Verbosity level vary from ```-v``` to ```-vvvv```, example: ```php shopware-import.php --file=path\filename.json -vvvv```