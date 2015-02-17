<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Crawls</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="James Nicholas">

	<!--link rel="stylesheet/less" href="less/bootstrap.less" type="text/css" /-->
	<!--link rel="stylesheet/less" href="less/responsive.less" type="text/css" /-->
	<!--script src="js/less-1.3.3.min.js"></script-->
	<!--append ‘#!watch’ to the browser URL, then refresh the page. -->
	
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/style.css" rel="stylesheet">
	<link href="css/colorbox.css" rel="stylesheet">

  <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
  <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
  <![endif]-->

  <!-- Fav and touch icons -->
  <link rel="apple-touch-icon-precomposed" sizes="144x144" href="img/apple-touch-icon-144-precomposed.png">
  <link rel="apple-touch-icon-precomposed" sizes="114x114" href="img/apple-touch-icon-114-precomposed.png">
  <link rel="apple-touch-icon-precomposed" sizes="72x72" href="img/apple-touch-icon-72-precomposed.png">
  <link rel="apple-touch-icon-precomposed" href="img/apple-touch-icon-57-precomposed.png">
  <link rel="shortcut icon" href="img/favicon.png">
  
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/scripts.js"></script>
  <script type="text/javascript" src="js/jquery.colorbox-min.js"></script>
</head>

<body>
<div class="container">
	<div class="row clearfix">
  	<div class="page-header">
			<h1>
				
			</h1>
		</div>
		<div class="col-md-12 column">
			<div class="tabbable" id="tabs-328723">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#panel-813167" data-toggle="tab">Overview</a>
					</li>
					<li>
						<a href="#panel-1468" data-toggle="tab">Page Analysis</a>
					</li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane" id="panel-813167">
						<p>
							<?php
                // Summary type content, but for whole site
                // All these PHP blocks should be using AJAX calls to load the page
              ?>
						</p>
					</div>
					<div class="tab-pane active" id="panel-1468">
						<p>
            	<div class="row clearfix">
		            <div class="col-md-8 column">
			            <div class="jumbotron">
                    <?php // Image will go here?>
			            </div>
		            </div>
		            <div class="col-md-4 column">
			            <div class="panel panel-default">
				            <div class="panel-heading">
					            <h3 class="panel-title">
						            Summary
					            </h3>
				            </div>
				            <div class="panel-body">
					            <?php 
                       // Summary of things like common concepts, # scripts, outbound links, inbound links, etc.
                      ?>
				            </div>
				            <div class="panel-footer">
					            Score: <?php // Assign some scoring algorithm ?>
				            </div>
			            </div>
			            <div class="panel-group" id="panel-810785">
				            <div class="panel panel-default">
					            <div class="panel-heading">
						             <a class="panel-title" data-toggle="collapse" data-parent="#panel-810785" href="#panel-element-322021">Outbound links</a>
					            </div>
					            <div id="panel-element-322021" class="panel-collapse in">
						            <div class="panel-body">
							            <?php 
                            // dump of outbound links 
                          ?>
						            </div>
					            </div>
				            </div>
				            <div class="panel panel-default">
					            <div class="panel-heading">
						             <a class="panel-title" data-toggle="collapse" data-parent="#panel-810785" href="#panel-element-322022">Inbound links</a>
					            </div>
					            <div id="panel-element-322022" class="panel-collapse in">
						            <div class="panel-body">
							            <?php 
                            // dump of inbound links 
                          ?>
						            </div>
					            </div>
				            </div>
				            <div class="panel panel-default">
					            <div class="panel-heading">
						             <a class="panel-title collapsed" data-toggle="collapse" data-parent="#panel-810785" href="#panel-element-717049">Scripts</a>
					            </div>
					            <div id="panel-element-717049" class="panel-collapse collapse">
						            <div class="panel-body">
							            <?php 
                            // dump of scripts
                          ?>
						            </div>
					            </div>
				            </div>
			            </div>
		            </div>
	            </div>
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</body>
</html>
