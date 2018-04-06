{extends "/blog/layouts/design.tpl"}

{block "content"}
	{load_templates "/blog/templates/post.tpl"}
	{BlogPost $data.BlogPost}
{/block}