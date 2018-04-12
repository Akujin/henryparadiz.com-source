var editposts = {
	onload: (e) => {
		tinymce.init({
		  selector: 'textarea#mainContent',
		  height: 400,
		  menubar: true, 
		  content_css : [
		  	'/css/blog.css',
		  	'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css',
		  	'https://fonts.googleapis.com/css?family=Playfair+Display:700,900'
		  ],
		  plugins: [
		    'advlist autolink lists link image charmap print preview anchor',
		    'searchreplace visualblocks fullscreen',
		    'insertdatetime media table contextmenu code codesample wordcount'
		  ],
		  body_class: 'blog-main',
		  //toolbar: 'emoticons',
		});
		
		$( 'form' ).on( 'submit', (e) => {
			e.preventDefault();

			$('.loader').show();
			
			var ID = $('#BlogPostID')[0].value;
			$.ajax({
				url: '/blog/api/blogpost/json/' + ID + '/edit',
				method: 'POST',
				data: $( 'form' ).serialize(),
				success: (data,status,xhr) => {
					if(!data.success) {
						alert(data.failed.errors);
					}
					else {
						$('#lastEdit').html('Last edited ' + moment().format("MM/DD/YY h:mm A") );
					}
					$('.loader').hide();
				}
			});
		});
	}
};

$(document).ready(editposts.onload);