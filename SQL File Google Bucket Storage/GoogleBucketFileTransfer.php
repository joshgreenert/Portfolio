<?php
/*
 * Creaetd by Joshua Greenert
 *
 * This script will grab a file from within the directory, zip it up, and send it to a Google
 * bucket through Google's API.
 */

require_once '../../credentials.php';   // obv not real...
use Google\Cloud\Storage\StorageClient;

// How to locate file with wild cards and whether this will work.  Obtain file through use of glob
$globfilename = glob("../../businessName" . date("Y-m-d") . "-02-**.sql.txt", GLOB_BRACE);
$filename = "";

// Set the glob file to the file name.
foreach($globfilename as $file){
    $filename = $file;
}

// substring the contents to ensure that the file name is correct.
$filename = substr($filename, 6, strlen($filename));

$bucketName = "file-storage";
$year = $year.get_class;
$cloudpath = "../../" . $filename;

// Set up the private key file content to send over.
$privateKeyFileContent = '{
    "type": "",
    "project_id": "",
    "private_key_id": "",
    "private_key":  "",
    "client_email": "",
    "client_id": "",
    "auth_uri": "",
    "token_uri": "",
    "auth_provider_x509_cert_url": "h",
    "client_x509_cert_url": ""
}';

// Set the function that will upload the file to google Cloud Storage.
function upload_object($bucketName, $fileName, $source)
{

    $privateKeyFileContent = $GLOBALS['privateKeyFileContent'];

    // Create the storage client and store the file in the cloud.
    $storage = new StorageClient([
        'keyFile' => json_decode($privateKeyFileContent, true)
    ]);

    $file = fopen($source, 'r');
    $bucket = $storage->bucket($bucketName);
    $object = $bucket->upload($file, [
        'name' => $fileName
    ]);
    // Print the success message to the user.
    printf('Uploaded %s to gs://%s/%s' . PHP_EOL, basename($source), $bucketName, $fileName);

}

// run the function to place both backup files in the Google Cloud Storage Bucket instance.
upload_object($bucketName, $filename, $cloudpath);

?>