WOW-Multi-Twitter 2.0 Class
=============

Display Multiple Twitter Feeds on your Website

<h2>Features</h2>
<ul>
  <li>Display optional images.</li>
  <li>Caches to Disk</li>
  <li>Date Styling Options</li>
  <li>Uses new customised OAuth wrapper from <a href="https://github.com/j7mbo/twitter-api-php">J7mbo</a> to get access to the API</li>
  <li>Can work with single Twitter feeds too</li>
  <li>Object orientated so you can have multiple separate single feeds or multiple double feeds. Currently, a maximum of 2 feeds together at a time.</li>
</ul>

<h2>Prequisites</h2>

You should already have a consumer key, consumer secret, access token and access token secret for one or both of your twitter accounts.

If not visit Twitter Apps to create them. https://apps.twitter.com/

PHP Server with CURL – The script has been tested using PHP 7 but should work for 5.6 and above.

A little knowledge of PHP and CSS are beneficial but not essential.


<h2>Installation</h2>
Unzip and copy the folder twitter and all it’s contents to your server.

On the page you want to display your twitter feed(s) load the class

<pre><code>
  include_once 'twitter/includes/wow-multi-twitter.php';
</code></pre>

<h2>Adding Keys and Tokens</h2>

Adding your Keys and Tokens to an array

<pre><code>
//User 1
$consKey = '';//Add your Twitter Consumer Key here
$consSec = '';//Add your Twitter Consumer Secret here
$usrToken = '';//Add your Twitter User Token here
$usrSec ='';//Add your Twitter User Secret here

//User 2 If you only want one user, simply ommit any reference to user2
$consKey2 = '';//Add your Twitter Consumer Key here
$consSec2 = '';//Add your Twitter Consumer Secret here
$usrToken2 = '';//Add your Twitter User Token here
$usrSec2 ='';//Add your Twitter User Secret here

//Creates an array of the above keys for each user
$user1 = array($consKey,$consSec,$usrToken,$usrSec);
$user2 = array($consKey2,$consSec2,$usrToken2,$usrSec2);
</code></pre>

<h2>Create a new instance of MultiTwitter</h2>

<pre><code>$twitter = new MultiTwitter();</code></pre>

<h2>Optional - Options</h2>
If no options are set then it will use the defaults detailed in the methods and properties section below. If you only set a few it will automatically use those from the defaults.

<pre><code>
//Sets custom options
$options = array('maxTweets'=> 25,
 'caching_intervals' => 10,
 'display_images' =>true,
 'style'=>'ddmm'
);

$twitter->setOptions($options);
</code></pre>

<h2>Set Users</h2>
Add our username and the key array we created earlier
<pre><code>
//Sets the user. If you only want one user just use setUser1
$twitter->setUser1('username1',$user1);
$twitter->setUser2('username2',$user2);
</code></pre>

<h2>Displaying the Tweets</h2>
<pre><code>
  echo $twitter->displayTweets();
</code></pre>

<h2>Methods and Properties</h2>

<h4>setUser1($username,$keys);</h4>
Setup authentication keys and tokens for the first user.

**$username**
(String) Required

**$keys**
(Array) Required

<h4>setUser2($username,$keys);</h4>
Setup authentication keys and tokens for the 2nd user, if a 2nd user is required.

**$username**
(String) Required

**$keys**
(Array) Required

<h4>setOptions($options);</h4>
Configure your own options

**$options**
(Array) Optional

*Default*
<pre><code>
  $defaults = array('maxTweets'=> 20,
                  'style'=>'time_since',
                  'include_retweets' => false,
                  'include_replies' => false,
                  'caching_intervals' => 60,
                  'display_images' => true
                  );
</code></pre>
**maxTweets- Default 20 (int)**
How many tweets you want to fetch from the Twitter Server per account.

**style- default ‘time_since’ (string)**
Displays the date or time since format.
The arguments are:

  *‘eng_suff’ – Displays 21st February*
  *‘ddmm’ – Displays 21 Feb*
  *‘ddmmyy’ – Displays 21 Feb 2018*
  *‘full_date’ – Displays Wed 21 Feb 2018*
  *‘time_since’ – Displays the time since the tweet in hours, minutes etc.*
  *‘month’ – Displays February 21, 2018*

**include_retweets- default false(bool)**
Do you want to include any retweets?

**include_replies- default false(bool)**
Do you want to include any replies?

**caching_intervals- default 60(int)**
How often in minutes do you want to cache the JSON request?

**display_images- default true(bool)**
Images are enabled by default but may not show on all tweets. You can optionally disable them.


<h4>displayTweets();</h4>
Output(echo) your twitter feed(s) for the world to see.

Further options including CSS styling are available on my blog:  https://www.worldoweb.co.uk/2018/wow-multi-twitter-2-0-reloaded-php-class
