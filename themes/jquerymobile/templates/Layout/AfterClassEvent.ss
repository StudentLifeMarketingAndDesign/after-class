<!--<header id="detail_header"><h1>Movies</h1></header>-->

<section id="detail_feature" class="feature clearfix">
    	   	<% if Image %>
				<img src="$Image.SmallImage.URL" alt="$Title" class="detail_feature_image"/>
			<% else %>
				<img src="$BaseHref/themes/afterclass/images/placeholder_small.png" alt="Event Image" class="detail_feature_image"/>
			<% end_if %>
    	   	
    	   	<div class="event_group">
    	   	<div class="event_text">
    		<h2><strong>$Title</strong></h2>
    		<p class="event_details">
    		
    			<% control DateAndTime %>$StartDate.format(M). $StartDate.format(d)<% if StartTime %> at $StartTime.nice<% end_if %><br /><% end_control %>
            	$Location<br/>
            	Admission: $Cost</p>           
       	</div>
       	
       	<div class="event_date_tag">
       		
            <!--<ul>
            	<li class="share_facebook"><a href="#">Facebook</a></li>
                <li class="share_rss"><a href="#">RSS</a></li>
                <li class="share_twitter"><a href="#">Twitter</a></li>
                <li class="share_email"><a href="#">Email</a></li>
            </ul>-->
        </div>       
    
			</div>
		<!--<div id="detail_carousel">
			<div class="carousel_thumb">
			<a href="#"><img src="http://dummyimage.com/96x81"/></a>
			</div>
			<div class="carousel_thumb">
			<a href="#"><img src="http://dummyimage.com/96x81"/></a>
			</div>
			<div class="carousel_thumb">
			<a href="#"><img src="http://dummyimage.com/96x81"/></a>
			</div>
			<div class="carousel_thumb_last">
			<a href="#"><img src="http://dummyimage.com/96x81"/></a>
			</div>
		</div>-->
		
    </section>


<section id="detail_event_description">
	
	<div id="detail_event_description_info">
	<h2>What's Happening?</h2>
	$Content
	
	<div>
		<% control Venues %>
		<a href="http://www.google.com/maps?f=d&daddr=$Address">Get Directions</a>
		<% end_control %>
	</div>
	
	</div>
	
</section>


<section id="detail_related_events">
	<% if RelatedEvents %>
	<h2>Related Events</h2>
	
	<% control RelatedEvents %>
	  
	  <div class="related_event">
	  	<% control Event %>
			<a href="$Link">
				<% if Image %>
					<img src="$Image.SmallImage.URL" alt="$Title" />
				<% else %>
					<img src="$BaseHref/themes/afterclass/images/placeholder_small.png" alt="Event Image">
				<% end_if %>
		
			<h3><strong>$Title</strong> @ $Location</h3></a>
			<% control UpcomingDates(1) %>
				$StartDate.format(M). $StartDate.format(d)<% if StartTime %> at $StartTime.nice<% end_if %><br />
			<% end_control %>
		<% end_control %>
		
	  </div>
	  
	<% end_control %>
	<% end_if %>
	
	<!--<div class="related_event">
		<a href="#"><img src="http://dummyimage.com/201x170"/>
		<h3><strong>Kenan Thompson</strong> @ Hubbard Park</h3></a>
	</div>-->
</section>

