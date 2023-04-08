<?
/*
 * Created by Joshua Greenert
 * 
 * This script snippet shows essentially how to create folders/directories via php code.
 */

// Create the file name as the string, open it to create it, then close it.
$filename = $shlstring;

$sqlfilename = fopen($filename, "w") or die("Unable to open txt file!");
fclose($sqlfilename);

// Put sha code into the file and close it.
$shatxtfile = fopen($shafile, "w") or die("Unable to open sha file!");
$sha = sha1_file($filename);
fwrite($shatxtfile, $sha);
fclose($shatxtfile);

// <https://github.com/joshdangdesign/updateCollection/tree/simplehistorylogs>
// This array contains the sha, committer, and converts the new filename to a base64 encoded object.
$data_git = array(
    'sha'=>file_get_contents($shafile),
    'message'=>'updated on ' . date('d-m-Y'),
    'content'=> base64_encode($filename),
    'committer'=> array(
        'name'=>'jgreenert+automation',
        'email' => 'jgreenert+automation@gmail.com')
);

// This portion converts the previous array to a json encoded object to be able to
// send through GitHub API and the curl sends it over.
$data_string_git = json_encode($data_git);

$ch_git = curl_init();
curl_setopt($ch_git, CURLOPT_URL, '<https://api.github.com/repos/joshgreenert/PHP-Projects/>' . $year . "/" . $month . "/" . $day . "/" . $filename);
curl_setopt($ch_git, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch_git, CURLOPT_CUSTOMREQUEST, "PUT");
curl_setopt($ch_git, CURLOPT_POSTFIELDS, $data_string_git);
curl_setopt($ch_git, CURLOPT_HTTPHEADER, array(
    'User-Agent: [USERNAME]',
    'Authorization: token [AUTHORIZATION_TOKEN]'
));

$result_git = curl_exec($ch_git);

// Close the URL contents.
curl_close($ch_git);

// Delete the file that was used.
unlink($filename);

// API only allows 60 records to be updated per hour. Moreover, the API detects abuse if done too quickly.
sleep(5);
?>