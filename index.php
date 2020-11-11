
<?php


function Dot2LongIP ($IPaddr)
{
    if ($IPaddr == "") {
        return 0;
    } else {
        $ips = explode(".", "$IPaddr");
        return ($ips[3] + ($ips[2] * 256) + ($ips[1] * 256 * 256) + ($ips[0] * 256 * 256 * 256));
    }
}









$time = microtime(true);
echo "pihole_adv_db_size ".filesize("/etc/pihole/pihole-FTL.db")."\n";
echo "pihole_adv_log_size ".filesize("/var/log/pihole.log")."\n";



$conn = new mysqli('localhost','advpihole','insertpasswordhere','ip2location');
if ($conn->connect_error){
  die('#mysql connection error: (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
}



$max = 500;



$db = new SQLite3('/etc/pihole/pihole-FTL.db');

$sql = 'SELECT domain, COUNT(domain) as number, status FROM queries GROUP BY domain ORDER BY COUNT(domain) DESC LIMIT '.($max+50);
$results = $db->query($sql);



$count = 0;

while ($row = $results->fetchArray() and $count <= $max) {
    if (strpos($row['domain'],'nisc') == false and strpos($row['domain'],'in-addr') == false){
      if ($row['status'] == 2 or $row['status'] == 3){
        $status = 'allowed';
      } elseif ($row['status'] == 0){
        $status = "n/a";
      } else {
        $status = "blocked";
      }
      
      $ip = Dot2LongIp(gethostbyname($row['domain']));
      $sql = 'SELECT * FROM ip2location_db5 WHERE ((ip_from <= '.$ip.') AND (ip_to >= '.$ip.'));';
      $ghost = $conn->query($sql);
      $ghost = mysqli_fetch_array($ghost, MYSQLI_ASSOC);
      if ($ghost['latitude'] != 0 and $ghost['longitude'] != 0){
        echo 'pihole_adv_domains{domain="'.$row['domain'].'",country="'.$ghost['country_code'].'", latitude="'.$ghost['latitude'].'", longitude="'.$ghost['longitude'].'" status="'.$status.'"} '.$row['number']."\n";
      }
      $count = $count + 1;
    }

}


$time = (microtime(true)-$time)/1000000;
echo "scrape_time_seconds ".$time."\n";


?>
