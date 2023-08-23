<html class="no-js">
<head>
	<meta charset="utf-8">
	<title>Check Product Image & Add</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">


	<link rel="shortcut icon" href="favicon.png">
	<link href="https://fonts.googleapis.com/css" rel="stylesheet" type="text/css">
	<link href="../css/font-awesome.min.css" rel="stylesheet" type="text/css">
	<link href="../style.css" rel="stylesheet" type="text/css">
	<link href="../custom.css" rel="stylesheet" type="text/css">
</head>
<body>

	<header id="top" class="navbar" role="banner">
		<div class="container">
			<div class="inner">

				<div class="site-title">
					<h1><a href="index.php"><img src="../img/logo.png" width="144" height="52" alt="Exa"></a></h1>
					<a href="#site-menu" class="site-menu-toggle">
						<span class="sr-only">Toggle navigation</span>
						<em class="first"></em><em class="middle"></em><em class="last"></em>
					</a>
				</div>

				<div id="site-menu">
					<?php include("inc/nav.php"); ?>
				</div>

			</div>
		</div>
	</header>

	

	<main id="content" role="main">
		<div class="container container-masonry">
			<div class="inner">			
				<div class="row">
					<div class="col-md-6 col-md-offset-3">
						<h3>Add Product</h3>
						<?php
						//add product
                            if(isset($_POST["Add"]))
                            {
                                $name=$conn->real_escape_string($_POST["name"]);
                                $price=$conn->real_escape_string($_POST["price"]);
                                $unit=$conn->real_escape_string($_POST["unit"]);
                                $type=$conn->real_escape_string($_POST["type"]);
                                
                                $file= $conn->real_escape_string($_FILES['file']['name']);
                                
                                if(empty($name))
                                    echo "Product Name is Required.";
                                elseif(empty($price))
                                    echo 'Price is Required.';
                                elseif(empty($unit))
                                    echo 'Unit Measurement is Required.';
                                elseif(empty($type))
                                    echo 'Product Type is Required.';
                                else if(empty($file))
                                    echo 'Product Image is Required.';
                                else
                                {
                                    $datename=date("Ymdhis");
                                    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                    $rename=$datename.".".$ext;
                                        
                                    if( $ext=='png' || $ext=='jpg' || $ext=='gif' || $ext=='jpeg') 
                                    { 
                                        //use tmp_name for all paths because needs name only get the current directory
                                        $file2=$conn->real_escape_string($_FILES['file']['tmp_name']);
                                    	$size = getimagesize($file2);
                                    	$width = $size[0];
                                    	$height = $size[1];
                                    	$image_type = $size[2];
                                    	
                                    	if( $image_type == IMAGETYPE_JPEG ) 
                                        {   
                                    	$original = imagecreatefromjpeg($file2);
                                    	$resized = imagecreatetruecolor(800, 600);
                                    	imagecopyresampled($resized, $original, 0, 0, 0, 0, 800, 600, $width, $height);
                                    	imagejpeg($resized, '../uploads/resize/'.$rename);
                                    	}
                                    	elseif( $image_type == IMAGETYPE_PNG  ) 
                                        {   
                                    	 $original = imagecreatefrompng($file2);
                                    	$resized = imagecreatetruecolor(800, 600);
                                    	imagecopyresampled($resized, $original, 0, 0, 0, 0, 800, 600, $width, $height);
                                    	imagepng($resized, '../resize/'.$rename);
                                    	}
                                                
                                        $stmt=$conn->prepare("INSERT INTO `products` (`pname`,`pprice`,`uom`,`cat_id`,`shop_id`,`pimg`) VALUES (?,?,?,?,?,?)");
                                        $stmt->bind_param('sissis',$name,$price,$unit,$type,$shop_id,$rename);
                                        $stmt->execute();
                                        
                                        move_uploaded_file($_FILES['file']['tmp_name'],"../img/".$rename);
                                                        
                                        echo 'Product Added Successfully';
                                        echo '<script>window.location.href="products.php";</script>';
                                    }
                                    else
                                        echo 'Upload file is not Image.';
                                }
                            }
						?>
						<div class="widget">
						
							<form method="post" enctype="multipart/form-data">
							<input type="text" name="name" placeholder="product name"/>
							<input type="number" name="price" placeholder="price"/>
							<input type="text" name="unit" placeholder="unit"/>
							<select name="type" class="form-control">
								<option value="">-- Select Category --</option>
  								<?php 
		  							$cat=mysqli_query($conn,"SELECT * FROM `categories`");
		  							while($res=mysqli_fetch_array($cat))
		  							{
		  							    
    		  							echo '<option value="'.$res["cat_id"].'">'.$res["cat_name"].'</option>';
    		  							
		  							}    
		  						?>
							</select>
							<input type="file" name="file" accept="image/*"/><br><br>
							<input class="button filled" type="submit" name="Add"/>

						</form>
						
						</div>
						
					
					</div>								
				</div>
				
			</div>
			<div class="clear"></div>
		</div>
	</main>