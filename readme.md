
### Speed testing async requests

A really quick and dirty comparison of 26 parallel requests. The summary of which is for 3 runs of the code:


Artax
-----
Time taken = 1.6761
Time taken = 2.0866
Time taken = 1.892


Guzzle
------
Time taken = 3.7705
Time taken = 3.4132
Time taken = 3.4545


i.e. almost twice the speed, but that's for very small request and response.

However, Artax also allows you to process responses as soon as they, which is it's major advantage.

### That's weird

There should haven't been that much of a difference between the two. Running strace for the two with


strace -c /usr/local/bin/php ArtaxTest.php

% time     seconds  usecs/call     calls    errors syscall
------ ----------- ----------- --------- --------- ----------------
 39.62    0.017999          48       374           select
 24.23    0.011006          74       148         4 open
 14.11    0.006409         134        48           lstat
 10.68    0.004854         202        24           stat
  4.25    0.001932          97        20         2 access
  2.23    0.001011           2       477           poll
  1.68    0.000761           3       218           close
  1.14    0.000520           6        86           sendto
  0.92    0.000418           2       203           fstat
  0.48    0.000219           1       245           mmap
  0.38    0.000173           0       812       373 recvfrom
  0.20    0.000089           1       104           munmap


About 0.046 of significant stuff.


strace -c /usr/local/bin/php GuzzleTest.php

% time     seconds  usecs/call     calls    errors syscall
------ ----------- ----------- --------- --------- ----------------
 27.84    0.023363         254        92           lstat
 21.91    0.018387          29       628           select
 19.78    0.016600         130       128         4 open
 13.30    0.011161         203        55           stat
 12.51    0.010500         206        51         2 access
  1.18    0.000991           2       632           recvfrom
  1.18    0.000987           4       245           fstat


About 0.082 of significant stuff.


i.e. neither Guzzle or Artax are actually spending much time in  code.



### Wireshark all the things


I've uploaded two wireshark captures one for [Guzzle](Guzzle.pcap) and one for [Artax](Artax.pcap).

I'll do a better analysis later, but what stands out is that:

* The transfer under Guzzle is having lots of packet retransmission.

* Because of the retransmission it takes Guzzle 1979 packets to do the request, but Artax only uses 802.

I think this may be because Guzzle is opening all the requests at once, which is overloading the small window size that Bing uses. Artax only opens 8 connections, which fit within the window size and allows the data to be transferred quicker, even though less is being transferred at once.