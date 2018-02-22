<?php

/******
 *Class Name:MultiTwitter
 *Author: Tracy Ridge
 *Version: 2.0
 *Updated 20th February 2018
 *Author URL:  https:www.worldoweb.co.uk/
 *Page URL: https://wp.me/poe8j-330
 *Description:  Display either single or multiple Twitter feeds on a website.
*******/

include_once'TwitterAPIExchange.php';

class MultiTwitter
{
    private $cacheDir = 'twitter/cache';

    private $defaults = array('maxTweets'=> 20,
                    'style'=>'time_since',
                    'include_retweets' => false,
                    'include_replies' => false,
                    'caching_intervals' => 60,
                    'display_images' => true
                  );
    /**
     * Sets up default values for the object
     * @throws \RuntimeException
     * @param string $defaults Leave empty for default values or use setOptions() instead
     */
    public function __construct($defaults='')
    {
        //If left empty set defaults options
        if (empty($defaults)) {
            $this->setOptions($this->defaults);
        } else {
            throw new RuntimeException("Cannot initialise - Please use setOptions()");
        }
        $this->cacheDir = $this->cacheDir;
    }
    /**
     * You can set your own options. If you don't set any the defaults will be used.
     *
     * @param array $options
     */
    public function setOptions($options)
    {
      //if some arguments are not set grab defaults and merge
        $options = array_merge($this->defaults, $options);

        $this->maxTweets = $options['maxTweets'];
        $this->retweets= $options['include_retweets'];
        $this->style =  $options['style'];
        $this->replies = $options['include_replies'];
        $this->cacheTime = $options['caching_intervals'];
        $this->displayImages = $options['display_images'];
    }

    /**
     * Setup our first users username and user authentication tokens
     * @param string $username
     * @param array $keys
     */
    public function setUser1($username, $keys)
    {
        $keys = $this->setKeys($keys);
        $this->user1 = array_merge(array('username'=>$username), $keys);
        $this->setFilename($username);
    }

    /**
     * Setup our second users username and user authentication tokens
     * @param string $username
     * @param array $keys
     */
    public function setUser2($username, $keys)
    {
        $keys = $this->setKeys($keys);
        $this->user2 = array_merge(array('username'=>$username), $keys);
        $this->setFilename($username);
    }
    /**
     * Sets the filename for the json file to be stored to disk
     * @param string $username
     */
    private function setFilename($username){
        $file = $this->cacheDir.'/'.$username.".json";
        $this->cacheFile = $file;
    }

    /**
     * Processes the keys for setUser1 and setUser2
     * @param array $keys
     */
    private function setKeys($keys)
    {
        return array('consumer_key'=>$keys[0],
                            'consumer_secret'=>$keys[1],
                            'oauth_access_token' =>$keys[2],
                            'oauth_access_token_secret' =>$keys[3]
    );
    }
    /**
     * Converts object to a string for displaying twitter stream(s)
     * @return string
     */
    public function __toString()
    {
        return $this->displayTweets();
    }

    /**
     * Sorts the 2 feeds by date
     * @param  array $val1 first user feed
     * @param  array $val2 second user feed
     * @return array
     */

    private function dateSort($val1, $val2)
    {
        if ($val1['created_at'] == $val2['created_at']) {
            return 0;
        }

        return (strtotime($val1['created_at']) > strtotime($val2['created_at'])) ? -1 : 1;
    }

    /**
     * Creates the folder and cache file if doesn't exist
     *
     * @return boolean Returns true if file and folder already exists
     */
    private function createFolder()
    {
        if (!file_exists($this->cacheDir)) {//if folder doesn't exist
          mkdir($this->cacheDir, 0755);//create folder
        }
        if (!file_exists($this->cacheFile)) {//if cache doesnt exist
            $file = fopen($this->cacheFile, 'w');
            fclose($file);
        } else {
            return true;
        } //return true if folder exists
    }

    /**
     * Checks to see if the file exists and when it was last cached.
     * It caches the file again if its past the the cachetime that you have set
     *
     * @return boolean cache the file again using cacheJson
     */
    private function checkCache()
    {
        if ($this->createFolder()) {//if folder already exists

            $cache_time = time() - filemtime($this->cacheFile); //check to see how long since cached

            if ($cache_time > 60 * $this->cacheTime) {
                return true;//cache the file again
            }
        } else {
            $this->createFolder();
            return true;
        }
    }

    /**
     * Grabs feed(s) from the twitter server. Checks to see if its 1 feed or 2 then caches the file
     * If server down it will reuse old file
     *
     * @return json
     */
    private function cacheJson()
    {
        if ($this->checkCache()) {//if true cache file

            $data = $this->getOauth();

            if ($data != '500') {
                if (is_array($data)) {
                    $json_1 = json_decode($data[0], true);
                    $json_2 = json_decode($data[1], true);
                    $my_data = array_merge($json_1, $json_2);
                    usort($my_data, array($this, 'dateSort'));
                    $data = json_encode($my_data);
                }
                file_put_contents($this->cacheFile, $data);
            }
        }
        return file_get_contents($this->cacheFile);
    }
    /**
     * Finds out the time difference since the tweet was created
     *
     * @param  string  $time1     Todays Date
     * @param  string  $time2     Published date
     * @param  integer $precision
     * @return string
     */
    private function dateDiff($time1, $time2, $precision = 6)
    {
        if (!is_int($time1)) {
            $time1 = strtotime($time1);
        }
        if (!is_int($time2)) {
            $time2 = strtotime($time2);
        }
        if ($time1 > $time2) {
            $ttime = $time1;
            $time1 = $time2;
            $time2 = $ttime;
        }
        $intervals = array(
        'year',
        'month',
        'day',
        'hour',
        'minute',
        'second'
      );
        $diffs     = array();
        foreach ($intervals as $interval) {
            $diffs[$interval] = 0;
            $ttime            = strtotime("+1 " . $interval, $time1);
            while ($time2 >= $ttime) {
                $time1 = $ttime;
                $diffs[$interval]++;
                $ttime = strtotime("+1 " . $interval, $time1);
            }
        }
        $count = 0;
        $times = array();
        foreach ($diffs as $interval => $value) {
            if ($count >= $precision) {
                break;
            }
            if ($value > 0) {
                if ($value != 1) {
                    $interval .= "s";
                }
                $times[] = $value . " " . $interval;
                $count++;
            }
        }
        return implode(", ", $times);
    }

    /**
     * Used by cacheJson, sets up communication with the twitter API using TwitterAPIExchange Class
     *
     * @return string|array
     */
    private function getOauth()
    {
        $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
        $method = 'GET';

        //user 1 setup
        $array = (array)$this->user1; //convert to array
        $name = array_shift($array);//remove first element of array
        $twitter = new TwitterAPIExchange($array);

        $getfields = array(
          'screen_name' => $name,
          'count' => $this->maxTweets,
          'include_rts' => $this->retweets,
          'exclude_replies' => $this->replies
        );
        //user 2 if set
        if (!empty($this->user2)) {
          $array2 = (array)$this->user2; //convert to array
          $name2 = array_shift($array2);//remove first element of array
          $twitter2 = new TwitterAPIExchange($array2);

          $getfields2 = $getfields;//copy of $getfields
          $getfields2['screen_name'] = $name2;//replace username

          $data2 =  $twitter2->includeResponseHeaders()->request($url, $method, $getfields2);
          $response2 = $twitter2->getResponseHeaders();
          $http_code2 = $this->responseCode($response2['status']);
        }
        //user 1
        $data =  $twitter->includeResponseHeaders()->request($url, $method, $getfields);

        $response = $twitter->getResponseHeaders();

        $http_code = $this->responseCode($response['status']);

        if (($http_code == "200") || (!empty($http_code2) == "200")) {
            if (!empty($this->user2)) {
                $result = array($data,$data2);

                return $result;
            } else {
                return $data;
            }
        } else {
            return "500";
        }
    }
    /**
     * Checks Response Code used by getOauth
     *
     * @param  string $response
     * @return string
     */
    private function responseCode($response)
    {
        if ($response == '200 OK' || $response == '304 NOT MODIFIED') {
            return "200";
        } else {
            return "500";
        }
    }
    /**
     * Processes the twitter feed for output
     *
     * @return string
     */
    public function displayTweets()
    {
        $twitter = '';

        $tweets = $this->cacheJson();
        $tweets = json_decode($tweets, true);

        $id = $tweets[0]['user']['screen_name'];

        $twitter .= "<ul id='wow-$id' class='wow-twitter'>";
        if (!empty($tweets)) {
            foreach ($tweets as $tweet) {
                if (array_key_exists('extended_entities', $tweet)) {
                    $media = $tweet['extended_entities']['media'][0]['media_url_https'];
                } else {
                    $media = null;
                }

                $pubDate        = $tweet['created_at'];
                $tweet          = $tweet['text'];

                $today          = time();
                $time           = substr($pubDate, 11, 5);
                $day            = substr($pubDate, 0, 3);
                $date           = substr($pubDate, 7, 4);
                $month          = substr($pubDate, 4, 3);
                $year           = substr($pubDate, 25, 5);
                $english_suffix = date('jS', strtotime(preg_replace('/\s+/', ' ', $pubDate)));
                $full_month     = date('F', strtotime($pubDate));


                #pre-defined tags
                $month   = $full_month . $date . $year;
                $full_date = $day . $date . $month . $year;
                $ddmmyy    = $date . $month . $year;
                $mmyy      = $month . $year;
                $mmddyy    = $month . $date . $year;
                $ddmm      = $date . $month;

                #Time difference
                $timeDiff = $this->dateDiff($today, $pubDate, 1);

                # Turn URLs into links
                $tweet = preg_replace('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\./-]*(\?\S+)?)?)?)@', '<a target="blank" title="$1" href="$1">$1</a>', $tweet);

                #Turn hashtags into links
                $tweet = preg_replace('/#([0-9a-zA-Z_-]+)/', "<a target='blank' title='$1' href=\"http://twitter.com/search?q=%23$1\">#$1</a>", $tweet);

                #Turn @replies into links
                $tweet = preg_replace("/@([0-9a-zA-Z_-]+)/", "<a target='blank' title='$1' href=\"http://twitter.com/$1\">@$1</a>", $tweet);


                $twitter .= "<li class='tweet'>{$tweet}<br />";
                if ($this->displayImages):
                  if (!is_null($media)) {
                      $twitter .= "<img src='{$media}'alt=''>";
                  }
                endif;

                $style = $this->style;

                if (isset($style)) {
                    if (!empty($style)) {
                        $when  = ($style == 'time_since' ? 'About' : 'On');
                        $twitter.="<strong>" . $when . "&nbsp;";

                        switch ($style) {
                          case 'eng_suff': {
                              $twitter .= $english_suffix . '&nbsp;' . $full_month;
                            }
                            break;
                          case 'time_since': {
                              $twitter .= $timeDiff . "&nbsp;ago";
                            }
                            break;
                          case 'ddmmyy': {
                              $twitter .= $ddmmyy;
                            }
                            break;
                          case 'ddmm': {
                              $twitter .= $ddmm;
                            }
                            break;
                          case 'full_date': {
                              $twitter .= $full_date;
                            }
                            break;
                          case 'month': {
                              $twitter .= $month;
                            }
                        } //end switch statement
              $twitter .= "</strong></li>"; //end of List
                    }
                }
            } //end of foreach
        } else {
            $twitter .= '<li>Sorry No Tweets Available</li>';
        } //end if statement
      $twitter .= '</ul>'; //end of Unordered list (Notice it's after the foreach loop!)
      return $twitter;
    }
}
