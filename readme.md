
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

However, Artax also allows you to process responses as soon as they, arrive which can save even more time.