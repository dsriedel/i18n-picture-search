<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>" ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

  <head>
	  <script src="http://www.google.com/jsapi"></script>
	  <script type="text/javascript">
	  google.load("jquery", "1.3.2");
	  </script>
	  <script type="text/javascript" src="jquery-validate/jquery.validate.min.js"></script>
	
	  <script type="text/javascript">
	
		google.load("language", "1");
		
		function translateUserText()
		{
		  var hasUserText = jQuery("#translateForm").validate().element("#userText");
		
			if(hasUserText)
			{
			  var text = jQuery("#userText").val();
			  google.language.detect(text, function(resultDetect) {
			    if (!resultDetect.error) {
			      var language = 'unknown';
			      for (l in google.language.Languages) {
			        if (google.language.Languages[l] == resultDetect.language) {
			          language = l;
			          break;
			        }
			      }
			      
			      for (l in google.language.Languages) {
				      
		          if(google.language.Languages[l] != resultDetect.language) {
				    	 
			          google.language.translate(text, resultDetect.language, google.language.Languages[l], function(resultTranslate) {
			            if (!resultTranslate.error) {
			              var container = document.getElementById("translation");
			              container.innerHTML += "<p class=\"test\">"+resultTranslate.translation+"</p>";
			            }
			          });
				      }
		        }
		
			      var container = document.getElementById("translation");
		        container.innerHTML += "<p class=\""+resultDetect.language+"\">"+text+"</p>";
			      
		        return false;
			    }
			  });
			}
			else
			{
		    jQuery("#userText").addClass("error");
		
		    return false;
			}	
		}

		function flickr() {
			var translations = "";
			
			jQuery("#translation p").each(function(index) {
		    translations += "+"+jQuery(this).text();
		  });
		
			window.location.href = "?translations=" + translations;
		}
		
		$(document).ready(function(){
			  google.language.getBranding('branding');
		
			  jQuery("#resetButton").click(function() {
				  var container = document.getElementById("translation");
			    container.innerHTML = "";
		
			    var container = document.getElementById("images");
			    container.innerHTML = "";
				});
		});
	  </script>
	
		<style type="text/css">
		input.error {
		  border: 1px solid #FF0000;
		}
		
		label.error {
		  color: #FF0000;
		}
		</style>

  </head>

	<body>
	
		<form id="translateForm"
		    name="translateForm"
		    action=""
		    method="post"
		    enctype="application/x-www-form-urlencoded"
		    onsubmit="javascript:translateUserText(); return false;"
		  >
		  <input type="text" name="userText" id="userText" class="required"></input>
		  <input type="submit" name="submitButton" id="submitButton" value="Translate"></input>
		  <input type="reset" name="resetButton" id="resetButton" value="Reset"></input>
		</form>
		
		<div id="detection"><!-- Display detected language (not used right now) --></div>
		
		<div id="translation"><!-- Display translations of text in all availabel languages --></div>
		
		<div id="branding"><!-- Display Google Branding --></div>
		
		<div id="link"><a href="javascript:flickr();">Flickr</a></div>
		
		<div id="images">
		<?php
		if(isset($_GET['translations']))
		{
			require_once("phpFlickr/phpFlickr.php");
			
			$f = new phpFlickr("a2f3cd0865b4a95c1d6915cc9759ce9d");
			
			$translations = array_filter(explode(" ",$_GET['translations']));
			
			foreach($translations as $translation)
			{
				//search for images on flickr using translated tags, max. 4 per language
				$result = $f->photos_search(array("tags"=>$translation, "tag_mode"=>"any", "safe_search"=>"1", "per_page"=>"4"));
				
				$counter = 1;
				foreach ($result['photo'] as $photo)
				{
				  echo '<img title="'.$translation.' by '.$photo['owner'].'" alt="'.$translation.' by '.$photo['owner'].'" src="http://farm'.$photo['farm'].'.static.flickr.com/'.$photo['server'].'/'.$photo['id'].'_'.$photo['secret'].'_s.jpg"';
				  
				  //break line after 4 images
				  if($counter == 4)
				  {
				    echo "<br/>";
				  }
				  else
				  {
				  	$counter++;
				  }
				}
				//maybe less than 4 images for one language have been found, add break line
				if($counter < 4)
				{
					echo "<br/>";
				}
				echo "<p>Total: ".$result['total']."</p>";
			}
			
			/**
			 * Save queries in text file.
			 * This will be replaced with a database connection to save queries
			 * for users to reuse accessing the data via an unique URL using some
			 * form of identfication.
			 * 
			 */
		  $f=fopen('queries.txt','a');
		  $content = $translations[1]."\r\n";
		  fwrite($f,$content,strlen($content));
		  fclose($f); 
		}
		?>
		</div>

  </body>
  
</html>