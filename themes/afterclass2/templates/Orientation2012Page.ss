<!doctype html>

<head>
	<% base_tag %>
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,700italic,400,600,700' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Doppio+One' rel='stylesheet' type='text/css'>

	<title>Orientation  - After Class: The Best of UI's Culture, Events and Nightlife</title>

	<% require themedCSS(layout) %>
	<% require themedCSS(orientation) %>
	 
	<script src="{$ThemeDir}/js/jquery.min.js"></script>
	<script src="{$ThemeDir}/js/modernizr-2.0.6.min.js"></script>

</head>
<body>
<div id="fb-root"></div>
<script>
  window.fbAsyncInit = function() {
    FB.init({appId: '127918570561161', status: true, cookie: true,
             xfbml: true});
  };
  
  //handle a session response from any of the auth related calls
function handleSessionResponse(response) {
    //if we dont have a session (which means the user has been logged out, redirect the user)
    if (!response.session) {
        window.location = "/mysite/Login.aspx";
        return;
    }

    //if we do have a non-null response.session, call FB.logout(),
    //the JS method will log the user out of Facebook and remove any authorization cookies
    FB.logout(handleSessionResponse);
}
  
  
  (function() {
    var e = document.createElement('script'); e.async = true;
    e.src = document.location.protocol +
      '//connect.facebook.net/en_US/all.js';
    document.getElementById('fb-root').appendChild(e);
  }());
  function loginnow() {
	  FB.login(function(response) {
		  if (response.session) {
			if (response.perms) {
			  // user is logged in and granted some permissions.
			  // perms is a comma separated list of granted permissions
			  javascript:location.reload(true);
			} else {
			  // user is logged in, but did not grant any permissions
			}
		  } else {
			// user is not logged in
		  }
		}, {perms:'email,sms'});
  }
  function logoutnow() {
	FB.api('/me', function(response) {
	  //if (response.first_name==undefined) {
	  //alert("Restart!")
	  //} else {
	  document.getElementById('Form_Form_first_name').value = response.first_name;
	  document.getElementById('Form_Form_last_name').value = response.last_name;
	  document.getElementById('Form_Form_email').value = response.email;
	  document.getElementById('Form_Form_facebook_id').value = response.id;
	  var myForm = document.getElementById('Form_Form');
	  myForm.submit();
	  FB.logout(function(response) {
	    //user is now logged out
	  });
	  //}
	  });
	
  }
</script>



	<div class="orientation-container">
	
		<div class="orientation-header-container">
			<img src="{$ThemeDir}/images/orientation/header.png" class="orientation-header" />
			
			<div class="orientation-form">
				<p>Don't have Facebook? Fill this out!</p>
				$Form	
			</div>
			
			<div class="clear">
			
		</div>
		
		
		<img src="{$ThemeDir}/images/orientation/never-miss.png" class="orientation-tagline" />
		
		<div class="orientation-steps">
		
			<ol>
				<li class="step-one"><a href="#" onclick="loginnow();return false;" class="orientation-button">sign in with facebook</a></li>
				<li class="step-two"><p>like us!</p>
					<div class="orientation-facebook">
						<fb:like-box profile_id="64131067165" width="700" colorscheme="dark" show_faces="true" border_color="#555555"  stream="false" header="false" connections="16"></fb:like-box>
					</div>
				</li>
				<li class="step-three"><a onClick="logoutnow();return false;" href="#" class="orientation-button logout">sign out</a></li>
			
				
		
		
		</div>

		
	</div>

</body>
</html>