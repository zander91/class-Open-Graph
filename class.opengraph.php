<?php

/**
 * class Opengraph version 1.1 by Chris Santala
 *
 * Provides PHP integration of the facebook Open Graph API.
 *
 * Based on the Simple PHP Framework class.config.php by Tyler Hall. 
 * 
 * For demonstration and implementation, visit <http://opengraphdemo.net>.
 * Latest version found at: <http://github.com/csantala/class-Open-Graph>.
 *
 * ----------------------------------------------------------------------
 * LICENSE
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License (GPL)
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * To read the license please visit http://www.gnu.org/copyleft/gpl.html
 * ======================================================================
*/
 

    class Opengraph {

        // CONFIGURATION BEGIN

        // add your server hostnames to the appropriate arrays
        private $productionServers = array('');
        private $stagingServers    = array('');
        private $localServers      = array('');

        // permissions, see http://developers.facebook.com/docs/authentication/permissions 

        // permissions basic 
        //private $permissions =  "email, read_stream, offline_access, publish_stream, user_about_me, user_birthday, user_hometown, user_location, user_relationships, user_religion_politics, user_status, user_website, user_work_history, read_friendlists, user_photo_video_tags, user_photos";
		
        // permissions opengraphdemo.net
        private $permissions =  "publish_stream, offline_access, email, read_insights, read_stream, user_about_me, user_activities, user_birthday, user_education_history, user_events, user_groups, user_hometown, user_interests, user_likes, user_location, user_notes, user_online_presence, user_photo_video_tags, user_photos, user_relationships, user_religion_politics, user_status, user_videos, user_website, user_work_history, read_friendlists, read_requests, friends_about_me, friends_activities, friends_birthday, friends_education_history, friends_events, friends_groups, friends_hometown, friends_interests, friends_likes, friends_location, friends_notes, friends_online_presence, friends_photo_video_tags, friends_photos, friends_relationships, friends_religion_politics, friends_status, friends_videos, friends_website, friends_work_history";		
		
        // permissions all 
        //private $permissions =  "publish_stream, create_event, rsvp_event, sms, offline_access, manage_pages, email, read_insights, read_stream, read_mailbox, ads_management, xmpp_login, user_about_me, user_activities, user_birthday, user_education_history, user_events, user_groups, user_hometown, user_interests, user_likes, user_location, user_notes, user_online_presence, user_photo_video_tags, user_photos, user_relationships, user_religion_politics, user_status, user_videos, user_website, user_work_history, read_friendlists, read_requests, friends_about_me, friends_activities, friends_birthday, friends_education_history, friends_events, friends_groups, friends_hometown, friends_interests, friends_likes, friends_location, friends_notes, friends_online_presence, friends_photo_video_tags, friends_photos, friends_relationships, friends_religion_politics, friends_status, friends_videos, friends_website, friends_work_history";

        // fields: keys used for extracting information from profiles
        public $fields = "id,first_name,last_name,name,link,about,birthday,work,education,email,website,hometown,location,gender,interested_in,meeting_for,relationship_status,religion,political,verified,significant_other,timezone,home,feed,tagged,posts,picture,friends,activities,interests,music,books,movies,television,likes,photos,albums,videos,groups,statuses,links,notes,events,inbox,outbox,updates,accounts";

        // default invitation text
        //   appears in the header of the invite box
        private $invite_action_text = 'Tell your friends...';
		
        //   appears in the invitiation message
        private $invite_body_text = 'I feel that you would benefit greatly from this resource - please check it out!';
		
        // Singleton constructor
        function __construct() { 

            $this->everywhere();

            $i_am_here = $this->whereAmI();

            if('production' == $i_am_here)
                $this->production();
            elseif('staging' == $i_am_here)
                $this->staging();
            elseif('local' == $i_am_here)
                $this->local();
            elseif('shell' == $i_am_here)
                $this->shell();
            else
                die('<h1>Where am I?</h1> <p>You need to setup your server names in <code>class.opengraph.php</code></p>
                     <p><code>$_SERVER[\'HTTP_HOST\']</code> reported <code>' . $_SERVER['HTTP_HOST'] . '</code></p>');
        }
		
        // Add code to be run on all servers
        private function everywhere() {
	
        }

        // Add code/variables to be run only on production servers
        // Add code/variables to be run only on production servers
        private function production() {
			
             $this->web_root           = '';

             // facebook open graph initialization variables for production facebook app
             $this->app_name           = '';
             $this->fbo_key            = '';
             $this->fbo_secret         = '';	
             $this->fbo_id             = '';

        }

        // Add code/variables to be run only on staging servers
        private function staging() {
	
             $this->web_root           = '';

             // facebook open graph initialization variables for staging facebook app
             $this->app_name           = '';
             $this->fbo_key            = '';
             $this->fbo_secret         = '';	
             $this->fbo_id             = '';
        }

        // Add code/variables to be run only on local (testing) servers
        private function local() {
			
             $this->web_root           = '';

             // facebook open graph initialization variables for local facebook app
             $this->app_name           = '';
             $this->fbo_key            = '';
             $this->fbo_secret         = '';	
             $this->fbo_id             = '';

        }
		

        // CONFIGURATION END
		
		
        // IMPLEMENTATION BEGIN
		
		public function doctype() {
             return '<!DOCTYPE html>
<html xmlns:fb="http://www.facebook.com/2008/fbml" xmlns:og="http://opengraphprotocol.org/schema/">
';
      }

        //  place before closing body tag
        public function init_sdk($logon_page, $logoff_page) {
              return '<div id="fb-root"></div><script src="http://connect.facebook.net/en_US/all.js"></script>
<script>
     FB.init({appId: \''.$this->fbo_id.'\', status: true, cookie: true, xfbml: true});
     FB.Event.subscribe(\'auth.sessionChange\', function(response) {
          if (response.session) {
               // user has logged on, redirect to $logon_page
               //  perform account linkup or further registration at this page.
               window.location = "'.$logon_page.'";
          }
          else {
               // user has logged out
               //  redirect to $logoff_page
               window.location = "'.$logoff_page.'";
               }
          });
</script>
';
		}

        // AUTHORIZATION BEGIN
		
        // login button, set permissions above
        public function fb_login_button($label = "Connect with Facebook") {

             return '<fb:login-button background="white" perms="'.$this->permissions.'"><fb:intl>'.$label.'</fb:intl></fb:login-button>';


        }
		
        // logoff button
        //  logoff redirection is controlled in function init_sdk($logon_page, $logoff_page)
        public function logout_button($label) {
             return '<a href="#" onClick="FB.logout();">'.$label.'</a>';
        }
	
        // logged on?
        public function logged_on() {
             if($this->get_facebook_cookie()) return true;
             else return false;
        }
		
        // check for and returns fb cookie, if found
        //  if present then the user is logged onto facebook and your website
        //	faecebook user id is found in cookie: $uid = $cookie['uid'];

        public function get_facebook_cookie() {
             $app_id = $this->fbo_id; 
             $application_secret = $this->fbo_secret;
             $args = array();
  	         if (!isset($_COOKIE['fbs_' . $app_id])) return null;
                parse_str(trim($_COOKIE['fbs_' . $app_id], '\\"'), $args);
             ksort($args);
             $payload = '';
             foreach ($args as $key => $value) {
                 if ($key != 'sig') {
                     $payload .= $key . '=' . $value;
                 }
             }
             if (md5($payload . $application_secret) != $args['sig']) {
                 return null;
             }
             return $args;
         }
		
        public function getuid($cookie) {
             return (string)$cookie['uid'];
        }
		
        public function getaccess_token($cookie) {
             return $cookie['access_token'];
        }
		
        // AUTHORIZATION END
		
        // SOCIAL PLUGINS BEGIN
		
        // like button
        // see http://developers.facebook.com/docs/reference/plugins/like for Open Graph tags for document
        public function like_button($id = null, $href = null, $count = null) {
             if ($count) $layout='layout="button count'; else $layout = null;
             if ($href) $href='href="'.$href.'?id='.$id.'"'; else $href = 'href="http://opengraphdemo.net"';
             return '<fb:like '.$layout.' '.$href.'></fb:like>
';
        }		

        // invite dialog 
        public function invite($invite_action_text, $invite_body_text) {
             return '<fb:serverfbml>
<script type="text/fbml">
     <fb:fbml>
          <fb:request-form action="'.$this->web_root.'/" method="POST" invite="true" type="'.$this->app_name.'" content="'.$invite_body_text.' &lt;fb:req-choice url=&quot;'.$this->web_root.'/&quot; label=&quot;Add App&quot;">
               <fb:multi-friend-selector showborder="false" actiontext="'.$invite_action_text.'" cols="4" bypass="cancel"></fb:multi-friend-selector>
          </fb:request-form>
     </fb:fbml>
</script>
</fb:serverfbml>
';
		}
		
        // comments
        public function comments($simple = 1, $width = null, $xid = null, $css = null, $numposts = 10, $cssrefresh = null) {
             if ($width) $width = 'width="'.$width.'"';
             if ($xid) $xid = 'xid="'.$xid.'"';
             if ($css) $css = 'css="'.facebook_comments.css.'?'.$cssrefresh.'"';
             return ' <fb:comments numposts="'.$numposts.'" '.$width.' '.$xid.' simple="'.$simple.'" '.$css.'></fb:comments>';
        }

        // SOCIAL PLUGINS END

        // DATA EXTRACTION BEGIN
		
        // get user data from facebook
        //  ensure that uid is a string and not an integer to avoid automatic decimal notation 
        public function getdata($uid, $access_token, $type = null) { //die('https://graph.facebook.com/'.$uid.'/'.$type.'?access_token=' . $access_token);

            // TYPE         RETURNED DATA
            // null         profile data 
            // friends      friend list
            // likes
            // movies
            // books
            // notes
            // photos
            // albums
            // videos
            // events
            // groups
            // feed         main newsfeed
            // home         profile newsfeed

            return json_decode($this->geturl('https://graph.facebook.com/'.$uid.'/'.$type.'?access_token=' . $access_token));
        }
	
        public function getfriendsdata($uid, $access_token) {
			
             return json_decode($this->geturl('https://graph.facebook.com/'.$uid.'/friends?access_token=' . $access_token.'&fields='.$this->fields));
			
        }
		
        public function getpublicdata($uid, $type = null) {
             return json_decode($this->geturl('https://graph.facebook.com/'.$uid.'/'.$type));
        }
		
        // get last status
        public function laststatus($uid, $access_token) {
    
             $feed = $this->getdata($uid, $access_token, 'feed');
             return $feed->data[0]->message;

        }
		
        // profile picture
        public function profile_picture($uid) {
             return '<fb:profile-pic uid="'.$uid.'" facebook-logo="true" linked="false"></fb:profile-pic>';
        }

        // facebook name
        public function facebook_name($uid, $useyou = false) {
             if ($useyou) $useyou = 'useyou="true"'; else $useyou = 'useyou="false"';

             return '<fb:name uid="'.$uid.'" '.$useyou.'></fb:name>';
        }
		
        // search public statuses
        public function search_public_statuses($item) {
             $item = str_replace(' ', "%20", $item);
             return json_decode($this->geturl('https://graph.facebook.com/search?q='.$item.'&type=post'));
        }
		
        // private_profile_search
        public function search_feed($item, $access_token) { 
             $item = str_replace(' ', "%20", $item);
             return json_decode($this->geturl('https://graph.facebook.com/me/home?q='.$item.'&access_token='.$access_token));
        }
		
        // my data search
        public function public_search_name($name) {
             $name = str_replace(' ', "%20", $name); 
             return json_decode($this->geturl('https://graph.facebook.com/search?q='.$name.'&type=user&access_token='.$this->public_at));
        }
		
        // my data feed search
        public function mydata_feed($id) {
             return json_decode($this->geturl('https://graph.facebook.com/'.$id.'/feed&access_token='.$this->public_at));
        }

        // user data
        public function userdata($uid, $access_token, $type = null) { 
             return json_decode($this->geturl('https://graph.facebook.com/'.$uid.'/?fields=likes,movies,books,notes,photos,albums,videos,events,groups,feed&access_token=' . $access_token));
        }
		
        public function public_search($id) {
             return json_decode($this->geturl('https://api.facebook.com/method/fql.query?'));
        }
		
        // generic decode
        public function decode_url($url) {
             return json_decode($this->geturl($url));
        }
		
        // DATA EXTRACTION END
		
        // PUBLISH BEGIN 
        // publish to feed
        public function posttofeed($uid, $access_token, $message, $link, $picture, $name, $caption, $description) {

             $query="access_token=$access_token&message=$message&link=$link&picture=$picture&name=$name&caption=$caption&description=$description";

             $url = "https://graph.facebook.com/$uid/feed"; 
             $ch = curl_init();
             curl_setopt($ch, CURLOPT_URL, $url);
             curl_setopt($ch, CURLOPT_POST, 7);
             curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
             curl_exec ($ch); 
             curl_close ($ch);
			 
        }
        // PUBLISH END

        // miscellaneous methods

        // create album
        public function create_album($uid, $access_token, $name) {
             $query="access_token=$access_token&name=$name";

             $url = "https://graph.facebook.com/me/albums"; 
             $ch = curl_init();
             curl_setopt($ch, CURLOPT_URL, $url);
             curl_setopt($ch, CURLOPT_POST, 2);
             curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
             $aid = curl_exec ($ch); 
             curl_close ($ch);
			 return $aid;
        }
		

        public function publish_photo($uid, $access_token, $file, $caption) { 

             $FILE_PATH = $_SERVER["DOCUMENT_ROOT"]."/".$file; 
			
             $args = array('message' => 'Photo Caption');
             $args['image'] = '@' . realpath($FILE_PATH);
			
             $arr_attachment = array('image' => '@'.realpath($FILE_PATH),
                                     'message' => $caption);
		
             $_curl = curl_init();
             curl_setopt($_curl, CURLOPT_URL, "https://graph.facebook.com/".$uid."/photos?access_token=".$access_token);
             curl_setopt($_curl, CURLOPT_HEADER, false);
             curl_setopt($_curl, CURLOPT_RETURNTRANSFER, true);
             curl_setopt($_curl, CURLOPT_POST, true);
             curl_setopt($_curl, CURLOPT_POSTFIELDS, $arr_attachment);
             curl_setopt($_curl, CURLOPT_SSL_VERIFYHOST, 0);
             curl_setopt($_curl, CURLOPT_SSL_VERIFYPEER, 0);

             $_photo = curl_exec($_curl);
             return $_photo;						

        }

        // get application token
        public function app_token() { 
            return $this->geturl('https://graph.facebook.com/oauth/access_token?grant_type=client_credentials&client_id='.$this->fbo_id.'&client_secret='.$this->fbo_secret);
        }
		
		
        // likes string handler
        //   returns string "1 person likes this.", "x people like this.", or null based on item->likes object 
        public function likes($data) {
            if (isset($data)) { 
                if ($data == 1) $likes = "1 person likes this.";
                else $likes = $data."&nbsp;people like this.";
            }
            else $likes = null;

            return $likes;
        }

        // message string handler
        //   returns message or null, depending if $item->message is set
        public function message($data) {
            if (isset($data)) { 
                $message = $data;
            }
            else $message = null;

            return $message;
        }


        // Grabs the contents of a remote URL. Can perform basic authentication if un/pw are provided.
        public function geturl($url, $username = null, $password = null) { //echo($url);

           if(function_exists('curl_init')) { 
                $ch = curl_init(); //echo $url."<br />";
                if ($this->whereAmI() == "local") curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                if(!is_null($username) && !is_null($password))
                     curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Basic ' .  base64_encode("$username:$password")));
                curl_setopt($ch, CURLOPT_URL, $url); // the url to scrape
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // true: return transfer in a string, false: echo transfer
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); // wait 5 seconds before giving up
                $html = curl_exec($ch);
                curl_close($ch); 
                return $html;
           }
           elseif(ini_get('allow_url_fopen') == true) {
                if(!is_null($username) && !is_null($password))
                     $url = str_replace("://", "://$username:$password@", $url); 
                $html = file_get_contents($url);
                return $html;
           }
           else { 
                // Cannot open url. Either install curl-php or set allow_url_fopen = true in php.ini
                return false;
           }
        }

        public function whereAmI()
        {
            if(in_array($_SERVER['HTTP_HOST'], $this->productionServers))
                return 'production';
            elseif(in_array($_SERVER['HTTP_HOST'], $this->stagingServers))
                return 'staging';
            elseif(in_array($_SERVER['HTTP_HOST'], $this->localServers))
                return 'local';
            elseif(isset($_ENV['SHELL']))
                return 'shell';
            else
                return false;
        }
    }