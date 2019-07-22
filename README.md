# RouterOSResponseArray
parsing RouterOS response only when access for ROS' raw response.
currently only tested with /ip/firewall/address-list/print
Don't use this. This aren't up to date and It's merged into https://github.com/EvilFreelancer/routeros-api-php.

```
$response = $ros_client->write(['/ip/firewall/address-list/print','?'.$query])->readAsIterator();
```


```
//$response = new ROSRA($ros_client->wr(['/ip/firewall/address-list/print','?'.$query],FALSE)); //out-dated 

//you could treat response as an array except using array_* function.

//export every row using foreach.
foreach ($response as $row){
  var_export($row);
}

last($response);
var_export(key($respose),current($response));
rewind($response);
```
