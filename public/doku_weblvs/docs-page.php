<!DOCTYPE html>
<html lang="en"> 
<head>
    <title>Dokumentation webLVS Das webbasierte Lagerverwaltungssystem</title>
    
    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <meta name="description" content="Bootstrap 4 Template For Software Startups">
    <meta name="author" content="Xiaoying Riley at 3rd Wave Media">    
    <link rel="shortcut icon" href="favicon.ico"> 
    
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome JS-->
    <script defer src="assets/fontawesome/js/all.js"></script>
    
    <!-- Plugins CSS -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.2/styles/atom-one-dark.min.css">

    <!-- Theme CSS -->  
    <link id="theme-style" rel="stylesheet" href="assets/css/theme.css">

</head> 

<body class="docs-page">    
    <header class="header fixed-top">	    
        <div class="branding docs-branding">
            <div class="container-fluid position-relative py-2">
                <div class="docs-logo-wrapper">
					<button id="docs-sidebar-toggler" class="docs-sidebar-toggler docs-sidebar-visible mr-2 d-xl-none" type="button">
	                    <span></span>
	                    <span></span>
	                    <span></span>
	                </button>
	                <div class="site-logo"><a class="navbar-brand" href="index.php"><img class="logo-icon mr-2" src="assets/images/webWMS_logo7.png" style="width: 15%" alt="logo"><span class="logo-text">Das webbasierte Lagerverwaltungssystem</span></a></div>
                </div><!--//docs-logo-wrapper-->
	            <div class="docs-top-utilities d-flex justify-content-end align-items-center">
	                <div class="top-search-box d-none d-lg-flex">
		                <form class="search-form">
				            <input type="text" placeholder="Dokumentation durchsuchen..." name="search" class="form-control search-input">
				            <button type="submit" class="btn search-btn" value="Search"><i class="fas fa-search"></i></button>
				        </form>
	                </div>
	
					<ul class="social-list list-inline mx-md-3 mx-lg-5 mb-0 d-none d-lg-flex">
						<li class="list-inline-item"><a href="https://github.com/SoftDevNord/webLVS"><i class="fab fa-github-square fa-fw"></i></a></li>
						<li class="list-inline-item"><a href="https://www.linkedin.com/in/rene-irrgang-364714188/"><i class="fab fa-linkedin fa-fw"></i></a></li>
						<li class="list-inline-item"><a href="https://www.xing.com/profile/Rene_Irrgang/"><i class="fab fa-xing-square fa-fw"></i></a></li>
						<li class="list-inline-item"><a href="mailto:rene.irrgang@outlook.de"><i class="fas fa-envelope-square"></i></a></li>
		            </ul><!--//social-list-->
	            </div><!--//docs-top-utilities-->
            </div><!--//container-->
        </div><!--//branding-->
    </header><!--//header-->
    
    <div class="docs-wrapper">
	    <div id="docs-sidebar" class="docs-sidebar">
		    <div class="top-search-box d-lg-none p-3">
                <form class="search-form">
		            <input type="text" placeholder="Search the docs..." name="search" class="form-control search-input">
		            <button type="submit" class="btn search-btn" value="Search"><i class="fas fa-search"></i></button>
		        </form>
            </div>
		    <nav id="docs-nav" class="docs-nav navbar">
			    <ul class="section-items list-unstyled nav flex-column pb-3">
				    <li class="nav-item section-title"><a class="nav-link scrollto active" href="#section-1"><span class="theme-icon-holder mr-2"><i class="fas fa-map-signs"></i></span>Einleitung</a></li>
				    <li class="nav-item section-title mt-3"><a class="nav-link scrollto" href="#section-2"><span class="theme-icon-holder mr-2"><i class="fas fa-arrow-down"></i></span>Installation</a></li>
				    <li class="nav-item"><a class="nav-link scrollto" href="#item-2-1">Section Item 2.1</a></li>
				    <li class="nav-item"><a class="nav-link scrollto" href="#item-2-2">Section Item 2.2</a></li>
				    <li class="nav-item"><a class="nav-link scrollto" href="#item-2-3">Section Item 2.3</a></li>
				    <li class="nav-item section-title mt-3"><a class="nav-link scrollto" href="#section-3"><span class="theme-icon-holder mr-2"><i class="fas fa-box"></i></span>APIs</a></li>
				    <li class="nav-item"><a class="nav-link scrollto" href="#item-3-1">Section Item 3.1</a></li>
				    <li class="nav-item"><a class="nav-link scrollto" href="#item-3-2">Section Item 3.2</a></li>
				    <li class="nav-item"><a class="nav-link scrollto" href="#item-3-3">Section Item 3.3</a></li>
				    <li class="nav-item section-title mt-3"><a class="nav-link scrollto" href="#section-4"><span class="theme-icon-holder mr-2"><i class="fas fa-cogs"></i></span>Integrations</a></li>
				    <li class="nav-item"><a class="nav-link scrollto" href="#item-4-1">Section Item 4.1</a></li>
				    <li class="nav-item"><a class="nav-link scrollto" href="#item-4-2">Section Item 4.2</a></li>
				    <li class="nav-item"><a class="nav-link scrollto" href="#item-4-3">Section Item 4.3</a></li>
				    <li class="nav-item section-title mt-3"><a class="nav-link scrollto" href="#section-5"><span class="theme-icon-holder mr-2"><i class="fas fa-tools"></i></span>Utilities</a></li>
				    <li class="nav-item"><a class="nav-link scrollto" href="#item-5-1">Section Item 5.1</a></li>
				    <li class="nav-item"><a class="nav-link scrollto" href="#item-5-2">Section Item 5.2</a></li>
				    <li class="nav-item"><a class="nav-link scrollto" href="#item-5-3">Section Item 5.3</a></li>
				    <li class="nav-item section-title mt-3"><a class="nav-link scrollto" href="#section-6"><span class="theme-icon-holder mr-2"><i class="fas fa-laptop-code"></i></span>Web</a></li>
				    <li class="nav-item"><a class="nav-link scrollto" href="#item-6-1">Section Item 6.1</a></li>
				    <li class="nav-item"><a class="nav-link scrollto" href="#item-6-2">Section Item 6.2</a></li>
				    <li class="nav-item"><a class="nav-link scrollto" href="#item-6-3">Section Item 6.3</a></li>
				    <li class="nav-item section-title mt-3"><a class="nav-link scrollto" href="#section-7"><span class="theme-icon-holder mr-2"><i class="fas fa-tablet-alt"></i></span>Mobile</a></li>
				    <li class="nav-item"><a class="nav-link scrollto" href="#item-7-1">Section Item 7.1</a></li>
				    <li class="nav-item"><a class="nav-link scrollto" href="#item-7-2">Section Item 7.2</a></li>
				    <li class="nav-item"><a class="nav-link scrollto" href="#item-7-3">Section Item 7.3</a></li>
				    <li class="nav-item section-title mt-3"><a class="nav-link scrollto" href="#section-8"><span class="theme-icon-holder mr-2"><i class="fas fa-book-reader"></i></span>Resources</a></li>
				    <li class="nav-item"><a class="nav-link scrollto" href="#item-8-1">Section Item 8.1</a></li>
				    <li class="nav-item"><a class="nav-link scrollto" href="#item-8-2">Section Item 8.2</a></li>
				    <li class="nav-item"><a class="nav-link scrollto" href="#item-8-3">Section Item 8.3</a></li>
				    <li class="nav-item section-title mt-3"><a class="nav-link scrollto" href="#section-9"><span class="theme-icon-holder mr-2"><i class="fas fa-lightbulb"></i></span>FAQs</a></li>
				    <li class="nav-item"><a class="nav-link scrollto" href="#item-9-1">Section Item 9.1</a></li>
				    <li class="nav-item"><a class="nav-link scrollto" href="#item-9-2">Section Item 9.2</a></li>
				    <li class="nav-item"><a class="nav-link scrollto" href="#item-9-3">Section Item 9.3</a></li>
			    </ul>

		    </nav><!--//docs-nav-->
	    </div><!--//docs-sidebar-->
	    <div class="docs-content">
		    <div class="container">
			    <article class="docs-article" id="section-1">
				    <header class="docs-header">
					    <h1 class="docs-heading">Einleitung <span class="docs-time">Letztes Update: 08.10.2019</span></h1>
					    <section class="docs-intro">
						    <p><img src="assets/images/webWMS_logo7.png" style="width: 8%; margin-bottom: 10px" alt="logo"> ist ein webbasierendes Lagerverwaltungssystem, welches sowohl für kleinere, aber auch für große Läger konzipiert ist.<br><br>
								Egal ob Handelsunternehmen, Produktion oder Logistikdienstleister, mit webLVS finden Sie ihr System, dass sich individuell auf alle Bedürfnisse und Anforderungen anpassen lässt.</p>
						</section><!--//docs-intro-->
						<!--//docs-code-block--></header>
			    </article>

				<article class="docs-article" id="section-2">
					<header class="docs-header">
						<h1 class="docs-heading">Web</h1>
						<section class="docs-intro">
							<p>Section intro goes here. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque finibus condimentum nisl id vulputate. Praesent aliquet varius eros interdum suscipit. Donec eu purus sed nibh convallis bibendum quis vitae turpis. Duis vestibulum diam lorem, vitae dapibus nibh facilisis a. Fusce in malesuada odio.</p>
						</section><!--//docs-intro-->
					</header>
					<section class="docs-section" id="item-2-1">
						<h2 class="section-heading">Section Item 2.1</h2>
						<p>Vivamus efficitur fringilla ullamcorper. Cras condimentum condimentum mauris, vitae facilisis leo. Aliquam sagittis purus nisi, at commodo augue convallis id. Sed interdum turpis quis felis bibendum imperdiet. Mauris pellentesque urna eu leo gravida iaculis. In fringilla odio in felis ultricies porttitor. Donec at purus libero. Vestibulum libero orci, commodo nec arcu sit amet, commodo sollicitudin est. Vestibulum ultricies malesuada tempor.</p>
					</section><!--//section-->

					<section class="docs-section" id="item-6-2">
						<h2 class="section-heading">Section Item 6.2</h2>
						<p>Vivamus efficitur fringilla ullamcorper. Cras condimentum condimentum mauris, vitae facilisis leo. Aliquam sagittis purus nisi, at commodo augue convallis id. Sed interdum turpis quis felis bibendum imperdiet. Mauris pellentesque urna eu leo gravida iaculis. In fringilla odio in felis ultricies porttitor. Donec at purus libero. Vestibulum libero orci, commodo nec arcu sit amet, commodo sollicitudin est. Vestibulum ultricies malesuada tempor.</p>
					</section><!--//section-->

					<section class="docs-section" id="item-6-3">
						<h2 class="section-heading">Section Item 6.3</h2>
						<p>Vivamus efficitur fringilla ullamcorper. Cras condimentum condimentum mauris, vitae facilisis leo. Aliquam sagittis purus nisi, at commodo augue convallis id. Sed interdum turpis quis felis bibendum imperdiet. Mauris pellentesque urna eu leo gravida iaculis. In fringilla odio in felis ultricies porttitor. Donec at purus libero. Vestibulum libero orci, commodo nec arcu sit amet, commodo sollicitudin est. Vestibulum ultricies malesuada tempor.</p>
					</section><!--//section-->
				</article><!--//docs-article-->

			    <article class="docs-article" id="section-2">
				    <header class="docs-header">
					    <h1 class="docs-heading">Installation</h1>
					    <section class="docs-intro">
						    <p>Section intro goes here. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque finibus condimentum nisl id vulputate. Praesent aliquet varius eros interdum suscipit. Donec eu purus sed nibh convallis bibendum quis vitae turpis. Duis vestibulum diam lorem, vitae dapibus nibh facilisis a. Fusce in malesuada odio.</p>
						</section><!--//docs-intro-->
				    </header>
				     <section class="docs-section" id="item-2-1">
						<h2 class="section-heading">Section Item 2.1</h2>
						<p>Vivamus efficitur fringilla ullamcorper. Cras condimentum condimentum mauris, vitae facilisis leo. Aliquam sagittis purus nisi, at commodo augue convallis id. Sed interdum turpis quis felis bibendum imperdiet. Mauris pellentesque urna eu leo gravida iaculis. In fringilla odio in felis ultricies porttitor. Donec at purus libero. Vestibulum libero orci, commodo nec arcu sit amet, commodo sollicitudin est. Vestibulum ultricies malesuada tempor.</p>
					</section><!--//section-->
					
					<section class="docs-section" id="item-2-2">
						<h2 class="section-heading">Section Item 2.2</h2>
						<p>Vivamus efficitur fringilla ullamcorper. Cras condimentum condimentum mauris, vitae facilisis leo. Aliquam sagittis purus nisi, at commodo augue convallis id. Sed interdum turpis quis felis bibendum imperdiet. Mauris pellentesque urna eu leo gravida iaculis. In fringilla odio in felis ultricies porttitor. Donec at purus libero. Vestibulum libero orci, commodo nec arcu sit amet, commodo sollicitudin est. Vestibulum ultricies malesuada tempor.</p>
					</section><!--//section-->
					
					<section class="docs-section" id="item-2-3">
						<h2 class="section-heading">Section Item 2.3</h2>
						<p>Vivamus efficitur fringilla ullamcorper. Cras condimentum condimentum mauris, vitae facilisis leo. Aliquam sagittis purus nisi, at commodo augue convallis id. Sed interdum turpis quis felis bibendum imperdiet. Mauris pellentesque urna eu leo gravida iaculis. In fringilla odio in felis ultricies porttitor. Donec at purus libero. Vestibulum libero orci, commodo nec arcu sit amet, commodo sollicitudin est. Vestibulum ultricies malesuada tempor.</p>
					</section><!--//section-->
			    </article><!--//docs-article-->
			    
			    
			    <article class="docs-article" id="section-3">
				    <header class="docs-header">
					    <h1 class="docs-heading">APIs</h1>
					    <section class="docs-intro">
						    <p>Section intro goes here. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque finibus condimentum nisl id vulputate. Praesent aliquet varius eros interdum suscipit. Donec eu purus sed nibh convallis bibendum quis vitae turpis. Duis vestibulum diam lorem, vitae dapibus nibh facilisis a. Fusce in malesuada odio.</p>
						</section><!--//docs-intro-->
				    </header>
				     <section class="docs-section" id="item-3-1">
						<h2 class="section-heading">Section Item 3.1</h2>
						<p>Vivamus efficitur fringilla ullamcorper. Cras condimentum condimentum mauris, vitae facilisis leo. Aliquam sagittis purus nisi, at commodo augue convallis id. Sed interdum turpis quis felis bibendum imperdiet. Mauris pellentesque urna eu leo gravida iaculis. In fringilla odio in felis ultricies porttitor. Donec at purus libero. Vestibulum libero orci, commodo nec arcu sit amet, commodo sollicitudin est. Vestibulum ultricies malesuada tempor.</p>
					</section><!--//section-->
					
					<section class="docs-section" id="item-3-2">
						<h2 class="section-heading">Section Item 3.2</h2>
						<p>Vivamus efficitur fringilla ullamcorper. Cras condimentum condimentum mauris, vitae facilisis leo. Aliquam sagittis purus nisi, at commodo augue convallis id. Sed interdum turpis quis felis bibendum imperdiet. Mauris pellentesque urna eu leo gravida iaculis. In fringilla odio in felis ultricies porttitor. Donec at purus libero. Vestibulum libero orci, commodo nec arcu sit amet, commodo sollicitudin est. Vestibulum ultricies malesuada tempor.</p>
					</section><!--//section-->
					
					<section class="docs-section" id="item-3-3">
						<h2 class="section-heading">Section Item 3.3</h2>
						<p>Vivamus efficitur fringilla ullamcorper. Cras condimentum condimentum mauris, vitae facilisis leo. Aliquam sagittis purus nisi, at commodo augue convallis id. Sed interdum turpis quis felis bibendum imperdiet. Mauris pellentesque urna eu leo gravida iaculis. In fringilla odio in felis ultricies porttitor. Donec at purus libero. Vestibulum libero orci, commodo nec arcu sit amet, commodo sollicitudin est. Vestibulum ultricies malesuada tempor.</p>
					</section><!--//section-->
			    </article><!--//docs-article-->
			    
			    <article class="docs-article" id="section-4">
				    <header class="docs-header">
					    <h1 class="docs-heading">Intergrations</h1>
					    <section class="docs-intro">
						    <p>Section intro goes here. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque finibus condimentum nisl id vulputate. Praesent aliquet varius eros interdum suscipit. Donec eu purus sed nibh convallis bibendum quis vitae turpis. Duis vestibulum diam lorem, vitae dapibus nibh facilisis a. Fusce in malesuada odio.</p>
						</section><!--//docs-intro-->
				    </header>
				     <section class="docs-section" id="item-4-1">
						<h2 class="section-heading">Section Item 4.1</h2>
						<p>Vivamus efficitur fringilla ullamcorper. Cras condimentum condimentum mauris, vitae facilisis leo. Aliquam sagittis purus nisi, at commodo augue convallis id. Sed interdum turpis quis felis bibendum imperdiet. Mauris pellentesque urna eu leo gravida iaculis. In fringilla odio in felis ultricies porttitor. Donec at purus libero. Vestibulum libero orci, commodo nec arcu sit amet, commodo sollicitudin est. Vestibulum ultricies malesuada tempor.</p>
					</section><!--//section-->
					
					<section class="docs-section" id="item-4-2">
						<h2 class="section-heading">Section Item 4.2</h2>
						<p>Vivamus efficitur fringilla ullamcorper. Cras condimentum condimentum mauris, vitae facilisis leo. Aliquam sagittis purus nisi, at commodo augue convallis id. Sed interdum turpis quis felis bibendum imperdiet. Mauris pellentesque urna eu leo gravida iaculis. In fringilla odio in felis ultricies porttitor. Donec at purus libero. Vestibulum libero orci, commodo nec arcu sit amet, commodo sollicitudin est. Vestibulum ultricies malesuada tempor.</p>
					</section><!--//section-->
					
					<section class="docs-section" id="item-4-3">
						<h2 class="section-heading">Section Item 4.3</h2>
						<p>Vivamus efficitur fringilla ullamcorper. Cras condimentum condimentum mauris, vitae facilisis leo. Aliquam sagittis purus nisi, at commodo augue convallis id. Sed interdum turpis quis felis bibendum imperdiet. Mauris pellentesque urna eu leo gravida iaculis. In fringilla odio in felis ultricies porttitor. Donec at purus libero. Vestibulum libero orci, commodo nec arcu sit amet, commodo sollicitudin est. Vestibulum ultricies malesuada tempor.</p>
					</section><!--//section-->
			    </article><!--//docs-article-->
			    
			    <article class="docs-article" id="section-5">
				    <header class="docs-header">
					    <h1 class="docs-heading">Utilities</h1>
					    <section class="docs-intro">
						    <p>Section intro goes here. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque finibus condimentum nisl id vulputate. Praesent aliquet varius eros interdum suscipit. Donec eu purus sed nibh convallis bibendum quis vitae turpis. Duis vestibulum diam lorem, vitae dapibus nibh facilisis a. Fusce in malesuada odio.</p>
						</section><!--//docs-intro-->
				    </header>
				     <section class="docs-section" id="item-5-1">
						<h2 class="section-heading">Section Item 5.1</h2>
						<p>Vivamus efficitur fringilla ullamcorper. Cras condimentum condimentum mauris, vitae facilisis leo. Aliquam sagittis purus nisi, at commodo augue convallis id. Sed interdum turpis quis felis bibendum imperdiet. Mauris pellentesque urna eu leo gravida iaculis. In fringilla odio in felis ultricies porttitor. Donec at purus libero. Vestibulum libero orci, commodo nec arcu sit amet, commodo sollicitudin est. Vestibulum ultricies malesuada tempor.</p>
					</section><!--//section-->
					
					<section class="docs-section" id="item-5-2">
						<h2 class="section-heading">Section Item 5.2</h2>
						<p>Vivamus efficitur fringilla ullamcorper. Cras condimentum condimentum mauris, vitae facilisis leo. Aliquam sagittis purus nisi, at commodo augue convallis id. Sed interdum turpis quis felis bibendum imperdiet. Mauris pellentesque urna eu leo gravida iaculis. In fringilla odio in felis ultricies porttitor. Donec at purus libero. Vestibulum libero orci, commodo nec arcu sit amet, commodo sollicitudin est. Vestibulum ultricies malesuada tempor.</p>
					</section><!--//section-->
					
					<section class="docs-section" id="item-5-3">
							<h2 class="section-heading">Section Item 5.3</h2>
						<p>Vivamus efficitur fringilla ullamcorper. Cras condimentum condimentum mauris, vitae facilisis leo. Aliquam sagittis purus nisi, at commodo augue convallis id. Sed interdum turpis quis felis bibendum imperdiet. Mauris pellentesque urna eu leo gravida iaculis. In fringilla odio in felis ultricies porttitor. Donec at purus libero. Vestibulum libero orci, commodo nec arcu sit amet, commodo sollicitudin est. Vestibulum ultricies malesuada tempor.</p>
					</section><!--//section-->
			    </article><!--//docs-article-->
			    
			    
		        <article class="docs-article" id="section-6">
				    <header class="docs-header">
					    <h1 class="docs-heading">Web</h1>
					    <section class="docs-intro">
						    <p>Section intro goes here. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque finibus condimentum nisl id vulputate. Praesent aliquet varius eros interdum suscipit. Donec eu purus sed nibh convallis bibendum quis vitae turpis. Duis vestibulum diam lorem, vitae dapibus nibh facilisis a. Fusce in malesuada odio.</p>
						</section><!--//docs-intro-->
				    </header>
				     <section class="docs-section" id="item-6-1">
						<h2 class="section-heading">Section Item 6.1</h2>
						<p>Vivamus efficitur fringilla ullamcorper. Cras condimentum condimentum mauris, vitae facilisis leo. Aliquam sagittis purus nisi, at commodo augue convallis id. Sed interdum turpis quis felis bibendum imperdiet. Mauris pellentesque urna eu leo gravida iaculis. In fringilla odio in felis ultricies porttitor. Donec at purus libero. Vestibulum libero orci, commodo nec arcu sit amet, commodo sollicitudin est. Vestibulum ultricies malesuada tempor.</p>
					</section><!--//section-->
					
					<section class="docs-section" id="item-6-2">
						<h2 class="section-heading">Section Item 6.2</h2>
						<p>Vivamus efficitur fringilla ullamcorper. Cras condimentum condimentum mauris, vitae facilisis leo. Aliquam sagittis purus nisi, at commodo augue convallis id. Sed interdum turpis quis felis bibendum imperdiet. Mauris pellentesque urna eu leo gravida iaculis. In fringilla odio in felis ultricies porttitor. Donec at purus libero. Vestibulum libero orci, commodo nec arcu sit amet, commodo sollicitudin est. Vestibulum ultricies malesuada tempor.</p>
					</section><!--//section-->
					
					<section class="docs-section" id="item-6-3">
							<h2 class="section-heading">Section Item 6.3</h2>
						<p>Vivamus efficitur fringilla ullamcorper. Cras condimentum condimentum mauris, vitae facilisis leo. Aliquam sagittis purus nisi, at commodo augue convallis id. Sed interdum turpis quis felis bibendum imperdiet. Mauris pellentesque urna eu leo gravida iaculis. In fringilla odio in felis ultricies porttitor. Donec at purus libero. Vestibulum libero orci, commodo nec arcu sit amet, commodo sollicitudin est. Vestibulum ultricies malesuada tempor.</p>
					</section><!--//section-->
			    </article><!--//docs-article-->
			    
			    
			    <article class="docs-article" id="section-7">
				    <header class="docs-header">
					    <h1 class="docs-heading">Mobile</h1>
					    <section class="docs-intro">
						    <p>Section intro goes here. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque finibus condimentum nisl id vulputate. Praesent aliquet varius eros interdum suscipit. Donec eu purus sed nibh convallis bibendum quis vitae turpis. Duis vestibulum diam lorem, vitae dapibus nibh facilisis a. Fusce in malesuada odio.</p>
						</section><!--//docs-intro-->
				    </header>
				     <section class="docs-section" id="item-7-1">
						<h2 class="section-heading">Section Item 7.1</h2>
						<p>Vivamus efficitur fringilla ullamcorper. Cras condimentum condimentum mauris, vitae facilisis leo. Aliquam sagittis purus nisi, at commodo augue convallis id. Sed interdum turpis quis felis bibendum imperdiet. Mauris pellentesque urna eu leo gravida iaculis. In fringilla odio in felis ultricies porttitor. Donec at purus libero. Vestibulum libero orci, commodo nec arcu sit amet, commodo sollicitudin est. Vestibulum ultricies malesuada tempor.</p>
					</section><!--//section-->
					
					<section class="docs-section" id="item-7-2">
						<h2 class="section-heading">Section Item 7.2</h2>
						<p>Vivamus efficitur fringilla ullamcorper. Cras condimentum condimentum mauris, vitae facilisis leo. Aliquam sagittis purus nisi, at commodo augue convallis id. Sed interdum turpis quis felis bibendum imperdiet. Mauris pellentesque urna eu leo gravida iaculis. In fringilla odio in felis ultricies porttitor. Donec at purus libero. Vestibulum libero orci, commodo nec arcu sit amet, commodo sollicitudin est. Vestibulum ultricies malesuada tempor.</p>
					</section><!--//section-->
					
					<section class="docs-section" id="item-7-3">
							<h2 class="section-heading">Section Item 7.3</h2>
						<p>Vivamus efficitur fringilla ullamcorper. Cras condimentum condimentum mauris, vitae facilisis leo. Aliquam sagittis purus nisi, at commodo augue convallis id. Sed interdum turpis quis felis bibendum imperdiet. Mauris pellentesque urna eu leo gravida iaculis. In fringilla odio in felis ultricies porttitor. Donec at purus libero. Vestibulum libero orci, commodo nec arcu sit amet, commodo sollicitudin est. Vestibulum ultricies malesuada tempor.</p>
					</section><!--//section-->
			    </article><!--//docs-article-->
			    
			    
			    <article class="docs-article" id="section-8">
				    <header class="docs-header">
					    <h1 class="docs-heading">Resources</h1>
					    <section class="docs-intro">
						    <p>Section intro goes here. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque finibus condimentum nisl id vulputate. Praesent aliquet varius eros interdum suscipit. Donec eu purus sed nibh convallis bibendum quis vitae turpis. Duis vestibulum diam lorem, vitae dapibus nibh facilisis a. Fusce in malesuada odio.</p>
						</section><!--//docs-intro-->
				    </header>
				     <section class="docs-section" id="item-8-1">
						<h2 class="section-heading">Section Item 8.1</h2>
						<p>Vivamus efficitur fringilla ullamcorper. Cras condimentum condimentum mauris, vitae facilisis leo. Aliquam sagittis purus nisi, at commodo augue convallis id. Sed interdum turpis quis felis bibendum imperdiet. Mauris pellentesque urna eu leo gravida iaculis. In fringilla odio in felis ultricies porttitor. Donec at purus libero. Vestibulum libero orci, commodo nec arcu sit amet, commodo sollicitudin est. Vestibulum ultricies malesuada tempor.</p>
					</section><!--//section-->
					
					<section class="docs-section" id="item-8-2">
						<h2 class="section-heading">Section Item 8.2</h2>
						<p>Vivamus efficitur fringilla ullamcorper. Cras condimentum condimentum mauris, vitae facilisis leo. Aliquam sagittis purus nisi, at commodo augue convallis id. Sed interdum turpis quis felis bibendum imperdiet. Mauris pellentesque urna eu leo gravida iaculis. In fringilla odio in felis ultricies porttitor. Donec at purus libero. Vestibulum libero orci, commodo nec arcu sit amet, commodo sollicitudin est. Vestibulum ultricies malesuada tempor.</p>
					</section><!--//section-->
					
					<section class="docs-section" id="item-8-3">
							<h2 class="section-heading">Section Item 8.3</h2>
						<p>Vivamus efficitur fringilla ullamcorper. Cras condimentum condimentum mauris, vitae facilisis leo. Aliquam sagittis purus nisi, at commodo augue convallis id. Sed interdum turpis quis felis bibendum imperdiet. Mauris pellentesque urna eu leo gravida iaculis. In fringilla odio in felis ultricies porttitor. Donec at purus libero. Vestibulum libero orci, commodo nec arcu sit amet, commodo sollicitudin est. Vestibulum ultricies malesuada tempor.</p>
					</section><!--//section-->
			    </article><!--//docs-article-->
			    
			    
			    <article class="docs-article" id="section-9">
				    <header class="docs-header">
					    <h1 class="docs-heading">FAQs</h1>
					    <section class="docs-intro">
						    <p>Section intro goes here. You can list all your FAQs using the format below.</p>
						</section><!--//docs-intro-->
				    </header>
				     <section class="docs-section" id="item-9-1">
						<h2 class="section-heading">Section Item 9.1 <small>(FAQ Category One)</small></h2>
						<h5 class="pt-3"><i class="fas fa-question-circle mr-1"></i>What's sit amet quam eget lacinia?</h5>
						<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium.</p>
						<h5 class="pt-3"><i class="fas fa-question-circle mr-1"></i>How to ipsum dolor sit amet quam tortor?</h5>
						<p>Donec sodales sagittis magna. Sed consequat, leo eget bibendum sodales, augue velit cursus nunc, quis gravida magna mi a libero. Fusce vulputate eleifend sapien. Vestibulum purus quam, scelerisque ut, mollis sed, nonummy id, metus. Nullam accumsan lorem in dui. </p>
						<h5 class="pt-3"><i class="fas fa-question-circle mr-1"></i>Can I  bibendum sodales?</h5>
						<p>Fusce vulputate eleifend sapien. Vestibulum purus quam, scelerisque ut, mollis sed, nonummy id, metus. Nullam accumsan lorem in dui. </p>
						<h5 class="pt-3"><i class="fas fa-question-circle mr-1"></i>Where arcu sed urna gravida?</h5>
						<p>Aenean et sodales nisi, vel efficitur sapien. Quisque molestie diam libero, et elementum diam mollis ac. In dignissim aliquam est eget ullamcorper. Sed id sodales tortor, eu finibus leo. Vivamus dapibus sollicitudin justo vel fermentum. Curabitur nec arcu sed urna gravida lobortis. Donec lectus est, imperdiet eu viverra viverra, ultricies nec urna. </p>
					</section><!--//section-->
					
					<section class="docs-section" id="item-9-2">
						<h2 class="section-heading">Section Item 9.2 <small>(FAQ Category Two)</small></h2>
						<h5 class="pt-3"><i class="fas fa-question-circle mr-1"></i>What's sit amet quam eget lacinia?</h5>
						<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium.</p>
						<h5 class="pt-3"><i class="fas fa-question-circle mr-1"></i>How to ipsum dolor sit amet quam tortor?</h5>
						<p>Donec sodales sagittis magna. Sed consequat, leo eget bibendum sodales, augue velit cursus nunc, quis gravida magna mi a libero. Fusce vulputate eleifend sapien. Vestibulum purus quam, scelerisque ut, mollis sed, nonummy id, metus. Nullam accumsan lorem in dui. </p>
						<h5 class="pt-3"><i class="fas fa-question-circle mr-1"></i>Can I  bibendum sodales?</h5>
						<p>Fusce vulputate eleifend sapien. Vestibulum purus quam, scelerisque ut, mollis sed, nonummy id, metus. Nullam accumsan lorem in dui. </p>
						<h5 class="pt-3"><i class="fas fa-question-circle mr-1"></i>Where arcu sed urna gravida?</h5>
						<p>Aenean et sodales nisi, vel efficitur sapien. Quisque molestie diam libero, et elementum diam mollis ac. In dignissim aliquam est eget ullamcorper. Sed id sodales tortor, eu finibus leo. Vivamus dapibus sollicitudin justo vel fermentum. Curabitur nec arcu sed urna gravida lobortis. Donec lectus est, imperdiet eu viverra viverra, ultricies nec urna. </p>
					</section><!--//section-->
					
					<section class="docs-section" id="item-9-3">
							<h2 class="section-heading">Section Item 9.3 <small>(FAQ Category Three)</small></h2>
						    <h5 class="pt-3"><i class="fas fa-question-circle mr-1"></i>How to dapibus sollicitudin justo vel fermentum?</h5>
							<p>Donec sodales sagittis magna. Sed consequat, leo eget bibendum sodales, augue velit cursus nunc, quis gravida magna mi a libero. Fusce vulputate eleifend sapien. Vestibulum purus quam, scelerisque ut, mollis sed, nonummy id, metus. Nullam accumsan lorem in dui. </p>
							<h5 class="pt-3"><i class="fas fa-question-circle mr-1"></i>How long bibendum sodales?</h5>
							<p>Fusce vulputate eleifend sapien. Vestibulum purus quam, scelerisque ut, mollis sed, nonummy id, metus. Nullam accumsan lorem in dui. </p>
							<h5 class="pt-3"><i class="fas fa-question-circle mr-1"></i>Where dapibus sollicitudin?</h5>
							<p>Aenean et sodales nisi, vel efficitur sapien. Quisque molestie diam libero, et elementum diam mollis ac. In dignissim aliquam est eget ullamcorper. Sed id sodales tortor, eu finibus leo. Vivamus dapibus sollicitudin justo vel fermentum. Curabitur nec arcu sed urna gravida lobortis. Donec lectus est, imperdiet eu viverra viverra, ultricies nec urna. </p>
					</section><!--//section-->
			    </article><!--//docs-article-->

			    <footer class="footer">
				    <div class="container text-center py-5">
				        <small class="copyright">Template Copyright &copy; <a href="https://themes.3rdwavemedia.com/" target="_blank">3rd Wave Media</a></small><br>
						<small class="copyright"> webLVS Copyright &copy; <a href="https://softdev-nord.de/" target="_blank">SoftDev Nord </a>Rene Irrgang</small>
				        <ul class="social-list list-unstyled pt-4 mb-0">
							<li class="list-inline-item"><a href="https://github.com/SoftDevNord/webLVS"><i class="fab fa-github-square fa-fw"></i></a></li>
							<li class="list-inline-item"><a href="https://www.linkedin.com/in/rene-irrgang-364714188/"><i class="fab fa-linkedin fa-fw"></i></a></li>
							<li class="list-inline-item"><a href="https://www.xing.com/profile/Rene_Irrgang/"><i class="fab fa-xing-square fa-fw"></i></a></li>
                            <li class="list-inline-item"><a href="mailto:rene.irrgang@outlook.de"><i class="fas fa-envelope-square"></i></a></li>
				        </ul><!--//social-list-->
				    </div>
			    </footer>
		    </div> 
	    </div>
    </div><!--//docs-wrapper-->
    
   
       
    <!-- Javascript -->          
    <script src="assets/plugins/jquery-3.4.1.min.js"></script>
    <script src="assets/plugins/popper.min.js"></script>
    <script src="assets/plugins/bootstrap/js/bootstrap.min.js"></script>  
    
    
    <!-- Page Specific JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.8/highlight.min.js"></script>
    <script src="assets/js/highlight-custom.js"></script> 
    <script src="assets/plugins/jquery.scrollTo.min.js"></script>
    <script src="assets/plugins/lightbox/dist/ekko-lightbox.min.js"></script> 
    <script src="assets/js/docs.js"></script> 

</body>
</html> 

