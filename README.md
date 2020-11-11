# pihole-location-metrics
A php script to be used with prometheus/grafana to deliver location based metrics from pihole, such as query locations of blocked domains. Requires you to have a database of all ip address locations, I used ip2location.

When I ran this on my raspberry pi it was quite performance intensive and took it about a minute to get full results (depending on the amount of results you want).

![Screenshot](screenshot.PNG?raw=true "Title")
