<!DOCTYPE html>
<head>

	<title>Twitter On My Website</title>
	<meta name="author" content="Tracy Ridge" />
	<link href="css/min/light-min.css" rel="stylesheet" /><!--Light Stylesheet, Choose from dark.css, light.css or blue.css-->

</head>
<body>

<?php include_once 'twitter/includes/wow-multi-twitter.php';

	/******
	*
	* Setup user keys and tokens
	*
	* *****/
	//User 1
	$consKey = '';//Add your Twitter Consumer Key here
	$consSec = '';//Add your Twitter Consumer Secret here
	$usrToken = '';//Add your Twitter User Token here
	$usrSec ='';//Add your Twitter User Secret here

	//User 2
	$consKey2 = '';//Add your Twitter Consumer Key here
	$consSec2 = '';//Add your Twitter Consumer Secret here
	$usrToken2 = '';//Add your Twitter User Token here
	$usrSec2 ='';//Add your Twitter User Secret here


	//Creates an array of the above keys for each user
	$user1 = array($consKey,$consSec,$usrToken,$usrSec);
	$user2 = array($consKey2,$consSec2,$usrToken2,$usrSec2);

	//Sets custom options
	$options = array(	'maxTweets'=> 25,
										'caching_intervals' => 1,
										'display_images' =>true,
										'style'=>'ddmm'
									);

	//Creates a new object
	$twitter = new MultiTwitter();

	//If you want to seperate twitter feeds you can create another twitter object
	//$twitter2 = new MultiTwitter();

	//set the above options
	$twitter->setOptions($options);
	//$twitter2->setOptions($options);

	//Sets the user. If you only want one user just use setUser1
	$twitter->setUser1('username1',$user1);
	$twitter->setUser2('username2',$user2);

	//If you want 2 seperate feeds
	//$twitter2->setUser1('username2',$user2);

	echo $twitter->displayTweets();
	//echo $twitter2->displayTweets();

?>
</body>
</html>
