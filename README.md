# RouterOSResponseArray
parsing RouterOS response only when access for ROS' raw response.
currently only tested with /ip/firewall/address-list/print

```
$response = new ROSRA($ros_client->wr(['/ip/firewall/address-list/print','?'.$query],FALSE));

//you could treat response as an array except using array_* function.

//export every row using foreach.
foreach ($response as $row){
  var_export($row);
}

last($response);
var_export(key($respose),current($response));
rewind($response);
```
