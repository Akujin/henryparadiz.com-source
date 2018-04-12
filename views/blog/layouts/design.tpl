
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Henry Paradiz">
    <link rel="icon" href="/favicon.ico">

    <title>Musings on Tech</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <!-- Custom styles for this template -->
    <link href="//fonts.googleapis.com/css?family=Playfair+Display:700,900" rel="stylesheet">
    <link href="/css/blog.css" rel="stylesheet">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/prism/1.13.0/themes/prism.css" integrity="sha256-kzX2z7yKWKhzn8IKBRdtcZi6kf3wvi5tdDBYIPMY5bA=" crossorigin="anonymous" />
  </head>

  <body>

    <div class="container">
      <header class="blog-header py-3">
        <div class="row flex-nowrap justify-content-between align-items-center">
          <div class="col-4 pt-1">
            {*<a class="text-muted" href="#">Subscribe</a>*}
          </div>
          <div class="col-4 text-center">
            <a class="blog-header-logo text-dark" href="/blog/">Musings on Tech from the Terminal</a>
          </div>
          <div class="col-4 d-flex justify-content-end align-items-center">
            {*<a class="text-muted" href="#">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-3"><circle cx="10.5" cy="10.5" r="7.5"></circle><line x1="21" y1="21" x2="15.8" y2="15.8"></line></svg>
            </a>
            <a class="btn btn-sm btn-outline-secondary" href="#">Sign up</a>*}
          </div>
        </div>
      </header>

    <main role="main" class="container">
      <div class="row">
        <div class="col-md-8 blog-main">
          <h3 class="pb-3 mb-4 font-italic border-bottom">
            From the Firehose
          </h3>
		  {block "content"}{/block}
        </div><!-- /.blog-main -->

        {include "/blog/sidebar.tpl"}

      </div><!-- /.row -->

    </main><!-- /.container -->

    <footer class="blog-footer">
	  <p>
		  Page generated in {number_format(hpcom\App::getLoadTime(),4)} seconds.
	  </p>
      <p>
        <a href="#" onclick="window.scrollTo(0,0); return false;">Back to top</a>
      </p>
    </footer>

    <script src="//code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/prism/1.13.0/prism.min.js" integrity="sha256-AqXn2u4UOZ36/xOhOEYrMIqgHrq1p8m88HJO+oPzPiM=" crossorigin="anonymous"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/prism/1.13.0/components/prism-php.js" integrity="sha256-EADl7JvHR98ugIWx0a9MEPq1NbOS7rKxLyjWtavg/YQ=" crossorigin="anonymous"></script>
	
  </body>
</html>