<% include Header %>


<div class="container">
	<div class="row justify-content-center">
		<div class="col-sm col-lg-7 content-container pt-5" role="main">
			<article>
				<h1>$Title</h1>
				<div class="content">$Content</div>
				<ul>
				<% loop $Children.Sort('Created DESC') %>
					<li><a href="$Link">$Title - <small>Last Edited $LastEdited.Nice</small></a></li>
				<% end_loop %>
				</ul>
			</article>
			$Form
			$PageComments

		</div>
	</div>
</div>
