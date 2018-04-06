{template BlogPost BlogPost}
	<div class="blog-post">
		<h2 class="blog-post-title"><a href="/blog/{date_format $BlogPost->Created "%Y"}/{date_format $BlogPost->Created "%m"}/{$BlogPost->Permalink}/">{$BlogPost->Title}</a></h2>
		<p class="blog-post-meta">{date_format $BlogPost->Created "%B %e, %Y"} by <a href="/blog/about">Henry</a></p>
		{$BlogPost->MainContent}
	</div><!-- /.blog-post -->
{/template}