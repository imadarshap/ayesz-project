 <!DOCTYPE html>
<html lang="en">
<head>
		<meta charset="utf-8" />
		<meta name="author" content="www.frebsite.nl" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		
        <title>AyesZ - All Your Essential Services within Zone</title>
		 
        <!-- Custom CSS -->
        <link href="{{url('assets/assets/css/styles.css')}}" rel="stylesheet">
		<style>
		    .downloadLink{
		        border: 1px solid #626a70;
                padding: 8px 10px;
                width: 220px;
                display: inline-block;
                border-radius: 4px;
                text-align: center;
		    }
		    
		    .downloadLink:hover{
		        border-color: #fff;
		    }
		    .heading_h1{
		        font-size:18px;
		    }
		    .card_hyt{
            	/*height: 200px;*/
            	margin-bottom: 32px;
            	border: none!important;
            	position: relative;
            }
            .card_bg_grn{
            	background: #51CA88;
            }
            .card_bg_lytGrn{
            	background: #8CE984;
            }
            .card_bg_pink{
            	background: #F676A3;
            }
            .card_bg_blue{
            	background: #4E9ED8;
            }
            .card_bg_yelw{
            	background: #F8C549;
            }
            .card_bg_red{
            	background: #F75C61;
            }
            .essentials_card_h3{
            	/*margin-bottom: 0;*/
                font-size: 18px;
                color: #fff;
                height: 50px;
            }
            .wht_crcl{
            	background: #fff;
            	height: 50px;
            	width: 50px;
            	border-radius: 50%;
            	position: absolute;
            	right: 16px;
            	bottom: 0;
            	padding: 10px;
            }
            .ryt_arw_img{
            	
            }
            .essntl_dv_lft{
             /*width: 40%;*/
             /*height: 100px;*/
            }
            .essntl_dv_lft img{
            	width: 60%;
            	height: 100%;
            }
            .essntl_dv_ryt{
            	
            }
		</style>
    </head>
	
    <body class="grocery-theme">
	
        <!-- ============================================================== -->
        <!-- Preloader - style you can find in spinners.css -->
        <!-- ============================================================== -->
        <div id="preloader"><div class="preloader"><span></span><span></span></div></div>
		
		
        <!-- ============================================================== -->
        <!-- Main wrapper - style you can find in pages.scss -->
        <!-- ============================================================== -->
        <div id="main-wrapper">
		
            <!-- ============================================================== -->
            <!-- Top header  -->
            <!-- ============================================================== -->
            <!-- Start Navigation -->
			<div class="header">
				<!-- Topbar -->
				<!-- <div class="header_topbar dark">
					<div class="container">
						<div class="row">
						
							<div class="col-lg-6 col-md-6 col-sm-6 col-4">
								<ul class="tp-list nbr ml-2">
									<li class="dropdown dropdown-currency hidden-xs hidden-sm">
										<a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Eng<i class="ml-1 fa fa-angle-down"></i></a>
										<ul class="dropdown-menu mlix-wrap">
											<li><a href="#">English</a></li>
											<li><a href="#">French</a></li>
											<li><a href="#">Spainish</a></li>
											<li><a href="#">Italy</a></li>
										</ul>
									</li>
									<li class="dropdown dropdown-currency hidden-xs hidden-sm">
										<a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">USD<i class="ml-1 fa fa-angle-down"></i></a>
										<ul class="dropdown-menu mlix-wrap">
											<li><a href="#">EUR</a></li>
											<li><a href="#">AUD</a></li>
											<li><a href="#">GBP</a></li>
										</ul>
									</li>
								</ul>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-8">
								<div class="topbar_menu">
									<ul>
										<li><a href="#"><i class="ti-bag"></i>My Account</a></li>
										<li><a href="#"><i class="ti-location-pin"></i>Track Order</a></li>
										<li class="hide-m"><a href="#"><i class="ti-heart"></i>Favourites</a></li>
									</ul>
								</div>
							</div>
						
						</div>
					</div>
				</div> -->
				
				<!-- Main header -->
				<div class="general_header">
					<div class="container">
						<div class="row align-items-center">
							<div class="col-lg-6 col-md-6 col-sm-6 col-6">
								<a class="nav-brand" href="#">
									<img src="{{url('assets/assets/img/logo-web.png')}}" class="logo" alt="" />
								</a>
							</div>
							<!-- <div class="col-lg-7 col-md-7 col-sm-4 col-3">
								<nav id="navigation" class="navigation navigation-landscape">
									<div class="nav-header">
										<div class="nav-toggle"></div>
									</div>
									<div class="nav-menus-wrapper" style="transition-property: none;">
										<ul class="nav-menu">
										
											<li class="active"><a href="#">Home<span class="submenu-indicator"></span></a>
												<ul class="nav-dropdown nav-submenu">
													<li><a href="#">Grocery<span class="submenu-indicator"></span></a>
														<ul class="nav-dropdown nav-submenu">
															<li><a href="grocery.html">Grocery 1</a></li>
															<li><a href="grocery-2.html">Grocery 2</a></li>
															<li><a href="grocery-3.html">Grocery 3</a></li>
														</ul>
													</li>
													<li><a href="#">Retail Design<span class="submenu-indicator"></span></a>
														<ul class="nav-dropdown nav-submenu">
															<li><a href="woocommerce.html">Woocommerce</a></li>
															<li><a href="woocommerce-2.html">Woocommerce 2</a></li>
															<li><a href="beauty.html">Cosmetics</a></li>
														</ul>
													</li>
													<li><a href="organic.html">Organic</a></li>
													<li><a href="electronics.html">Electronic</a></li>
													<li><a href="digital.html">Digital</a></li>
													<li><a href="furniture.html">Furniture</a></li>
												</ul>
											</li>
											
											<li><a href="#">Category<span class="submenu-indicator"></span></a>
												<ul class="nav-dropdown nav-submenu">
													<li><a href="#">VARIATIONS 1<span class="submenu-indicator"></span></a>
														<ul class="nav-dropdown nav-submenu">
															<li><a href="search-full-width.html">Full Width Banner</a></li>
															<li><a href="search-list-layout.html">With List Layouts</a></li>
															<li><a href="4-column.html">4 Column Products</a></li>
															<li><a href="5-column.html">5 Column Products</a></li>
															<li><a href="6-column.html">6 Column Products</a></li>
														</ul>
													</li>
													<li><a href="#">VARIATIONS 2<span class="submenu-indicator"></span></a>
														<ul class="nav-dropdown nav-submenu">
															<li><a href="grocery.html">Header Style 1</a></li>
															<li><a href="grocery-2.html">Header Style 2</a></li>
															<li><a href="grocery-3.html">Header Style 3</a></li>
															<li><a href="#">7 Columns Products</a></li>
															<li><a href="#">9 Columns Products</a></li>
															<li><a href="#">10 Columns Products</a></li>
														</ul>
													</li>
												</ul>
											</li>
											
											<li><a href="#">Products<span class="submenu-indicator"></span></a>
												<ul class="nav-dropdown nav-submenu">
													<li><a href="#">VARIATIONS 1<span class="submenu-indicator"></span></a>
														<ul class="nav-dropdown nav-submenu">
															<li><a href="search-sidebar.html">With Sidebar 1</a></li>
															<li><a href="search-sidebar-2.html">With Sidebar 2</a></li>
															<li><a href="search-sidebar-3.html">With Sidebar 3</a></li>
															<li><a href="search-sidebar-4.html">With Sidebar 4</a></li>
															<li><a href="search-sidebar-5.html">With Sidebar 5</a></li>
															<li><a href="search-sidebar-6.html">With Sidebar 6</a></li>
															<li><a href="search-sidebar-7.html">With Sidebar 7</a></li>
															<li><a href="search-sidebar-8.html">With Sidebar 8</a></li>
														</ul>
													</li>
													<li><a href="#">VARIATIONS 2<span class="submenu-indicator"></span></a>
														<ul class="nav-dropdown nav-submenu">
															<li><a href="detail-1.html">Default Product</a></li>
															<li><a href="detail-1.html">Default Product 2</a></li>
															<li><a href="detail-3.html">Grouped products</a></li>
															<li><a href="detail-4.html">Simple Product</a></li>
															<li><a href="detail-5.html">Grouped Product</a></li>
															<li><a href="detail-6.html">Digital Product</a></li>
															<li><a href="vendor.html">Vendor Detail</a></li>
														</ul>
													</li>
												</ul>
											</li>
											
											<li><a href="#">Shop Pages<span class="submenu-indicator"></span></a>
												<ul class="nav-dropdown nav-submenu">
													<li><a href="#">User Dashboard<span class="submenu-indicator"></span></a>
														<ul class="nav-dropdown nav-submenu">
															<li><a href="order.html">My Order</a></li>
															<li><a href="order-history.html">Order History</a></li>
															<li><a href="order-tracking.html">Order Tracking</a></li>
															<li><a href="wishlist.html">Wishlist</a></li>
															<li><a href="account-info.html">Account Settings</a></li>
															<li><a href="payment-methode.html">Payment Methods</a></li>
														</ul>
													</li>
													<li><a href="add-to-cart.html">Add To Cart</a></li>
													<li><a href="billing.html">Billing Page</a></li>
													<li><a href="checkout-complete.html">Checkout Confirmation</a></li>
													<li><a href="admin-login.html">Admin Login</a></li>
												</ul>
											</li>
											
											<li><a href="#">Pages<span class="submenu-indicator"></span></a>
												<ul class="nav-dropdown nav-submenu">
													<li><a href="blog.html">Blog Page</a></li>
													<li><a href="blog-detail.html">Blog Detail</a></li>
													<li><a href="about.html">About Us</a></li>
													<li><a href="contact.html">Contact</a></li>
													<li><a href="login-signup.html">Login/SignUp</a></li>
													<li><a href="faq.html">FAQ's</a></li>
													<li><a href="404.html">404</a></li>
												</ul>
											</li>
											
										</ul>

									</div>
								</nav>
							</div> -->
							
							<div class="col-lg-6 col-md-6 col-sm-6 col-6">
								<div class="general_head_right">
									<ul>
										<li><a style="font-size:14px;" href="http://ayesz.in/ayesz_delivery.apk" download>Download  Delivery Boy App</a></li>
										<li><a style="font-size:14px;" href="http://ayesz.in/ayesz_vendor.apk" download>Download Vendor App</a></li>
										<li><a style="font-size:14px;" href="https://play.google.com/store/apps/details?id=com.ayesz.user" target="_blank">Download User App</a></li>
									</ul>
								</div>
							</div>
							
						</div>
					</div>
				</div>
				
			</div>
			<!-- End Navigation -->
			<div class="clearfix"></div>
			<!-- ============================================================== -->
			<!-- Top header  -->
			<!-- ============================================================== -->
			
			
			<!-- ======================== Banner Start ==================== -->
			<div class="short_banner">
				<div class="container">
					<div class="short_banner_wrap">
						<div class="row align-items-center">
							
							<div class="col-lg-7 col-md-6 col-sm-12">
								<div class="short_banner_caption mb-3">
									<h2>Home Delivery in<br> Stipulated time</h2>
									<p>All Your Essential Services within Zone like: Vegetables, Food and Groceries at your door step.</p>
								</div>
								<div class="blocks search_blocks shor_banner_form">
									<div class="input-group">
										<input type="text" class="form-control" placeholder="Search Products / Items...">
										<div class="input-group-append">
										<button class="btn search_btn" type="button"><i class="ti-search"></i></button>
										</div>
									</div>
								</div>
							</div>
							
							<div class="col-lg-5 col-md-6 col-sm-12">
								<div class="short_banner_thumb">
									<img src="{{url('assets/assets/img/home1.jpg')}}" class="img-fluid" alt="" />
								</div>
							</div>
							
						</div>
					</div>
				</div>
			</div>
			<!-- ======================== Banner End ==================== -->
			
			<!-- ======================== Choose Category Start ==================== -->
			<section class="pt-5 pb-0" style="display:none;">
				<div class="container">
					
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12">
							<div class="owl-carousel category-slider owl-theme">
							
								<!-- Single Item -->
								<div class="item">
									<div class="woo_category_box border_style rounded">
										<div class="woo_cat_thumb">
											<a href="#"><img src="{{url('assets/assets/img/c-1.png')}}" class="img-fluid" alt="" /></a>
										</div>
										<div class="woo_cat_caption">
											<h4><a href="#">Fresh Vegetables</a></h4>
										</div>
									</div>
								</div>
								
								<!-- Single Item -->
								<div class="item">
									<div class="woo_category_box border_style rounded">
										<div class="woo_cat_thumb">
											<a href="#"><img src="{{url('assets/assets/img/c-3.png')}}" class="img-fluid" alt="" /></a>
										</div>
										<div class="woo_cat_caption">
											<h4><a href="#">Dairy & Eggs</a></h4>
										</div>
									</div>
								</div>
								
								<!-- Single Item -->
								<div class="item">
									<div class="woo_category_box border_style rounded">
										<div class="woo_cat_thumb">
											<a href="#"><img src="{{url('assets/assets/img/c-12.png')}}" class="img-fluid" alt="" /></a>
										</div>
										<div class="woo_cat_caption">
											<h4><a href="#">Beverages</a></h4>
										</div>
									</div>
								</div>
								
								<!-- Single Item -->
								<div class="item">
									<div class="woo_category_box border_style rounded">
										<div class="woo_cat_thumb">
											<a href="#"><img src="{{url('assets/assets/img/c-4.png')}}" class="img-fluid" alt="" /></a>
										</div>
										<div class="woo_cat_caption">
											<h4><a href="#">Meat & Seafood</a></h4>
										</div>
									</div>
								</div>
								
								<!-- Single Item -->
								<div class="item">
									<div class="woo_category_box border_style rounded">
										<div class="woo_cat_thumb">
											<a href="#"><img src="{{url('assets/assets/img/c-5.png')}}" class="img-fluid" alt="" /></a>
										</div>
										<div class="woo_cat_caption">
											<h4><a href="#">Fruits</a></h4>
										</div>
									</div>
								</div>
								
								<!-- Single Item -->
								<div class="item">
									<div class="woo_category_box border_style rounded">
										<div class="woo_cat_thumb">
											<a href="#"><img src="{{url('assets/assets/img/c-6.png')}}" class="img-fluid" alt="" /></a>
										</div>
										<div class="woo_cat_caption">
											<h4><a href="#">Grocery & Staples</a></h4>
										</div>
									</div>
								</div>
								
								<!-- Single Item -->
								<div class="item">
									<div class="woo_category_box border_style rounded">
										<div class="woo_cat_thumb">
											<a href="#"><img src="{{url('assets/assets/img/c-7.png')}}" class="img-fluid" alt="" /></a>
										</div>
										<div class="woo_cat_caption">
											<h4><a href="#">Snacks</a></h4>
										</div>
									</div>
								</div>
								
								<!-- Single Item -->
								<div class="item">
									<div class="woo_category_box border_style rounded">
										<div class="woo_cat_thumb">
											<a href="#"><img src="{{url('assets/assets/img/c-8.png')}}" class="img-fluid" alt="" /></a>
										</div>
										<div class="woo_cat_caption">
											<h4><a href="#">Pets care</a></h4>
										</div>
									</div>
								</div>
								
								<!-- Single Item -->
								<div class="item">
									<div class="woo_category_box border_style rounded">
										<div class="woo_cat_thumb">
											<a href="#"><img src="{{url('assets/assets/img/c-9.png')}}" class="img-fluid" alt="" /></a>
										</div>
										<div class="woo_cat_caption">
											<h4><a href="#">Electornics</a></h4>
										</div>
									</div>
								</div>
								
								<!-- Single Item -->
								<div class="item">
									<div class="woo_category_box border_style rounded">
										<div class="woo_cat_thumb">
											<a href="#"><img src="{{url('assets/assets/img/c-10.png')}}" class="img-fluid" alt="" /></a>
										</div>
										<div class="woo_cat_caption">
											<h4><a href="#">Home Care</a></h4>
										</div>
									</div>
								</div>
								
								<!-- Single Item -->
								<div class="item">
									<div class="woo_category_box border_style rounded">
										<div class="woo_cat_thumb">
											<a href="#"><img src="{{url('assets/assets/img/c-12.png')}}" class="img-fluid" alt="" /></a>
										</div>
										<div class="woo_cat_caption">
											<h4><a href="#">Noodles & Sauces</a></h4>
										</div>
									</div>
								</div>
								
								<!-- Single Item -->
								<div class="item">
									<div class="woo_category_box border_style rounded">
										<div class="woo_cat_thumb">
											<a href="#"><img src="{{url('assets/assets/img/c-11.png')}}" class="img-fluid" alt="" /></a>
										</div>
										<div class="woo_cat_caption">
											<h4><a href="#">Dry Snacks</a></h4>
										</div>
									</div>
								</div>
							
							</div>
						</div>
					</div>
					
				</div>
			</section>
			<div class="clearfix"></div>
			<!-- ======================== Choose Category End ==================== -->
			
			<!-- ======================== Fresh Vegetables Start ==================== -->
			<section class="cen-mid" style="display:none;">
				<div class="container">
					
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12">
							<div class="sec-heading center">
								<span class="theme-cl">Fresh Fruits on Your Home</span>
								<h2 class="grocery_title">Fresh Online Store</h2>
							</div>
						</div>
					</div>
					
					<div class="row">
					
						<div class="col-lg-12 col-md-12">
							<div class="multi-tab">
								<ul>
									<li><a href="javascript:void(0);" class="active">All</a></li>
									<li><a href="javascript:void(0);">Winter</a></li>
									<li><a href="javascript:void(0);">Summer</a></li>
									<li><a href="javascript:void(0);">Dry Fruits</a></li>
									<li><a href="javascript:void(0);">Liquid</a></li>
								</ul>
							</div>
						</div>
						
						<div class="col-lg-12 col-md-12">
							<div class="row">
							
								<!-- Single Item -->
								<div class="col-lg-3 col-md-4 col-sm-6">
									<div class="woo_product_grid">
										<span class="woo_pr_tag hot">Hot</span>
										<div class="woo_product_thumb">
											<img src="{{url('assets/assets/img/vegetables/1.png')}}" class="img-fluid" alt="" />
										</div>
										<div class="woo_product_caption center">
											<div class="woo_rate">
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star"></i>
											</div>
											<div class="woo_title">
												<h4 class="woo_pro_title"><a href="detail-1.html">Accumsan Tree Fusce</a></h4>
											</div>
											<div class="woo_price">
												<h6>$72.47<span class="less_price">$112.10</span></h6>
											</div>
										</div>
										<div class="woo_product_cart hover">
											<!-- <ul>
												<li><a href="javascript:void(0);" data-toggle="modal" data-target="#viewproduct-over" class="woo_cart_btn btn_cart"><i class="ti-eye"></i></a></li>
												<li><a href="add-to-cart.html" class="woo_cart_btn btn_view"><i class="ti-shopping-cart"></i></a></li>
												<li><a href="wishlist.html" class="woo_cart_btn btn_save"><i class="ti-heart"></i></a></li>
											</ul> -->
										</div>								
									</div>
								</div>
								
								<!-- Single Item -->
								<div class="col-lg-3 col-md-4 col-sm-6">
									<div class="woo_product_grid">
										<div class="woo_product_thumb">
											<img src="{{url('assets/assets/img/vegetables/2.png')}}" class="img-fluid" alt="" />
										</div>
										<div class="woo_product_caption center">
											<div class="woo_rate">
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star"></i>
											</div>
											<div class="woo_title">
												<h4 class="woo_pro_title"><a href="detail-1.html">Accumsan Tree Fusce</a></h4>
											</div>
											<div class="woo_price">
												<h6>$72.47<span class="less_price">$112.10</span></h6>
											</div>
										</div>
										<div class="woo_product_cart hover">
											<!-- <ul>
												<li><a href="javascript:void(0);" data-toggle="modal" data-target="#viewproduct-over" class="woo_cart_btn btn_cart"><i class="ti-eye"></i></a></li>
												<li><a href="add-to-cart.html" class="woo_cart_btn btn_view"><i class="ti-shopping-cart"></i></a></li>
												<li><a href="wishlist.html" class="woo_cart_btn btn_save"><i class="ti-heart"></i></a></li>
											</ul> -->
										</div>								
									</div>
								</div>
								
								<!-- Single Item -->
								<div class="col-lg-3 col-md-4 col-sm-6">
									<div class="woo_product_grid">
										<span class="woo_pr_tag new">New</span>
										<div class="woo_product_thumb">
											<img src="{{url('assets/assets/img/vegetables/3.png')}}" class="img-fluid" alt="" />
										</div>
										<div class="woo_product_caption center">
											<div class="woo_rate">
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star"></i>
											</div>
											<div class="woo_title">
												<h4 class="woo_pro_title"><a href="detail-1.html">Accumsan Tree Fusce</a></h4>
											</div>
											<div class="woo_price">
												<h6>$72.47<span class="less_price">$112.10</span></h6>
											</div>
										</div>
										<div class="woo_product_cart hover">
											<!-- <ul>
												<li><a href="javascript:void(0);" data-toggle="modal" data-target="#viewproduct-over" class="woo_cart_btn btn_cart"><i class="ti-eye"></i></a></li>
												<li><a href="add-to-cart.html" class="woo_cart_btn btn_view"><i class="ti-shopping-cart"></i></a></li>
												<li><a href="wishlist.html" class="woo_cart_btn btn_save"><i class="ti-heart"></i></a></li>
											</ul> -->
										</div>								
									</div>
								</div>
								
								<!-- Single Item -->
								<div class="col-lg-3 col-md-4 col-sm-6">
									<div class="woo_product_grid">
										<span class="woo_offer_sell">Save 20% Off</span>
										<div class="woo_product_thumb">
											<img src="{{url('assets/assets/img/vegetables/4.png')}}" class="img-fluid" alt="" />
										</div>
										<div class="woo_product_caption center">
											<div class="woo_rate">
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star"></i>
											</div>
											<div class="woo_title">
												<h4 class="woo_pro_title"><a href="detail-1.html">Accumsan Tree Fusce</a></h4>
											</div>
											<div class="woo_price">
												<h6>$72.47<span class="less_price">$112.10</span></h6>
											</div>
										</div>
										<div class="woo_product_cart hover">
											<!-- <ul>
												<li><a href="javascript:void(0);" data-toggle="modal" data-target="#viewproduct-over" class="woo_cart_btn btn_cart"><i class="ti-eye"></i></a></li>
												<li><a href="add-to-cart.html" class="woo_cart_btn btn_view"><i class="ti-shopping-cart"></i></a></li>
												<li><a href="wishlist.html" class="woo_cart_btn btn_save"><i class="ti-heart"></i></a></li>
											</ul> -->
										</div>								
									</div>
								</div>
								
								<!-- Single Item -->
								<div class="col-lg-3 col-md-4 col-sm-6">
									<div class="woo_product_grid">
										<span class="woo_pr_tag hot">Hot</span>
										<div class="woo_product_thumb">
											<img src="{{url('assets/assets/img/vegetables/5.png')}}" class="img-fluid" alt="" />
										</div>
										<div class="woo_product_caption center">
											<div class="woo_rate">
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star"></i>
											</div>
											<div class="woo_title">
												<h4 class="woo_pro_title"><a href="detail-1.html">Accumsan Tree Fusce</a></h4>
											</div>
											<div class="woo_price">
												<h6>$72.47<span class="less_price">$112.10</span></h6>
											</div>
										</div>
										<div class="woo_product_cart hover">
											<!-- <ul>
												<li><a href="javascript:void(0);" data-toggle="modal" data-target="#viewproduct-over" class="woo_cart_btn btn_cart"><i class="ti-eye"></i></a></li>
												<li><a href="add-to-cart.html" class="woo_cart_btn btn_view"><i class="ti-shopping-cart"></i></a></li>
												<li><a href="wishlist.html" class="woo_cart_btn btn_save"><i class="ti-heart"></i></a></li>
											</ul> -->
										</div>								
									</div>
								</div>
								
								<!-- Single Item -->
								<div class="col-lg-3 col-md-4 col-sm-6">
									<div class="woo_product_grid">
										<div class="woo_product_thumb">
											<img src="{{url('assets/assets/img/vegetables/6.png')}}" class="img-fluid" alt="" />
										</div>
										<div class="woo_product_caption center">
											<div class="woo_rate">
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star"></i>
											</div>
											<div class="woo_title">
												<h4 class="woo_pro_title"><a href="detail-1.html">Accumsan Tree Fusce</a></h4>
											</div>
											<div class="woo_price">
												<h6>$72.47<span class="less_price">$112.10</span></h6>
											</div>
										</div>
										<div class="woo_product_cart hover">
											<!-- <ul>
												<li><a href="javascript:void(0);" data-toggle="modal" data-target="#viewproduct-over" class="woo_cart_btn btn_cart"><i class="ti-eye"></i></a></li>
												<li><a href="add-to-cart.html" class="woo_cart_btn btn_view"><i class="ti-shopping-cart"></i></a></li>
												<li><a href="wishlist.html" class="woo_cart_btn btn_save"><i class="ti-heart"></i></a></li>
											</ul> -->
										</div>								
									</div>
								</div>
								
								<!-- Single Item -->
								<div class="col-lg-3 col-md-4 col-sm-6">
									<div class="woo_product_grid">
										<span class="woo_pr_tag hot">Hot</span>
										<div class="woo_product_thumb">
											<img src="{{url('assets/assets/img/vegetables/7.png')}}" class="img-fluid" alt="" />
										</div>
										<div class="woo_product_caption center">
											<div class="woo_rate">
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star"></i>
											</div>
											<div class="woo_title">
												<h4 class="woo_pro_title"><a href="detail-1.html">Accumsan Tree Fusce</a></h4>
											</div>
											<div class="woo_price">
												<h6>$72.47<span class="less_price">$112.10</span></h6>
											</div>
										</div>
										<div class="woo_product_cart hover">
											<!-- <ul>
												<li><a href="javascript:void(0);" data-toggle="modal" data-target="#viewproduct-over" class="woo_cart_btn btn_cart"><i class="ti-eye"></i></a></li>
												<li><a href="add-to-cart.html" class="woo_cart_btn btn_view"><i class="ti-shopping-cart"></i></a></li>
												<li><a href="wishlist.html" class="woo_cart_btn btn_save"><i class="ti-heart"></i></a></li>
											</ul> -->
										</div>								
									</div>
								</div>
								
								<!-- Single Item -->
								<div class="col-lg-3 col-md-4 col-sm-6">
									<div class="woo_product_grid">
										<span class="woo_pr_tag new">New</span>
										<div class="woo_product_thumb">
											<img src="{{url('assets/assets/img/vegetables/8.png')}}" class="img-fluid" alt="" />
										</div>
										<div class="woo_product_caption center">
											<div class="woo_rate">
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star"></i>
											</div>
											<div class="woo_title">
												<h4 class="woo_pro_title"><a href="detail-1.html">Accumsan Tree Fusce</a></h4>
											</div>
											<div class="woo_price">
												<h6>$72.47<span class="less_price">$112.10</span></h6>
											</div>
										</div>
										<div class="woo_product_cart hover">
											<!-- <ul>
												<li><a href="javascript:void(0);" data-toggle="modal" data-target="#viewproduct-over" class="woo_cart_btn btn_cart"><i class="ti-eye"></i></a></li>
												<li><a href="add-to-cart.html" class="woo_cart_btn btn_view"><i class="ti-shopping-cart"></i></a></li>
												<li><a href="wishlist.html" class="woo_cart_btn btn_save"><i class="ti-heart"></i></a></li>
											</ul> -->
										</div>								
									</div>
								</div>
							</div>	
						</div>
					</div>
				</div>
			</section>
			<div class="clearfix"></div>
			<!-- ======================== Fresh Vegetables End ==================== -->
			<!-- ======================== New Section Essential at your door start ==================== -->
			<section>
        		<div class="container">
        			<div class="row">
        				<div class="col-12">
        					<h1 class="heading_h1">All Your  Essentials at Your Door Steps</h1>
        				</div>
        			</div>
        			<div class="row">
        				<div class="col-12 col-md-6">
        					<div class="card card_hyt card_bg_grn">
        						<div class="card-body">
        							<h3 class="essentials_card_h3">Groceries & Essentials</h3>  
        							<div class="row">
        								<div class="col-6">
        									<div class="essntl_dv_lft">
        										<img src="{{url('assets/assets/img/grocery/groc_img.png')}}">
        									</div>
        								</div>
        								<div class="col-6" style="display:none">
        									<a href="#">
        										<div class="essntl_dv_ryt">
        											<div class="wht_crcl">
        												<img class="ryt_arw_img" src="{{url('assets/assets/img/grocery/rytArw.svg')}}">
        											</div>
        										</div>
        									</a>
        								</div>
        							</div>
        						</div>
        					</div>
        				</div>
        				<div class="col-12 col-md-6">
        					<div class="card card_hyt card_bg_lytGrn">
        						<div class="card-body">
        							<h3 class="essentials_card_h3"> Fruits & Vegetables</h3>  
        							<div class="row">
        								<div class="col-6">
        									<div class="essntl_dv_lft">
        										<img src="{{url('assets/assets/img/grocery/fruit_img.png')}}">
        									</div>
        								</div>
        								<div class="col-6" style="display:none">
        									<a href="#">
        										<div class="essntl_dv_ryt">
        											<div class="wht_crcl">
        												<img class="ryt_arw_img" src="{{url('assets/assets/img/grocery/rytArw.svg')}}">
        											</div>
        										</div>
        									</a>
        								</div>
        							</div>
        						</div>
        					</div>
        				</div>
        				<div class="col-12 col-md-6">
        					<div class="card card_hyt card_bg_pink">
        						<div class="card-body">
        							<h3 class="essentials_card_h3">Food Delivery</h3>  
        							<div class="row">
        								<div class="col-6">
        									<div class="essntl_dv_lft">
        										<img src="{{url('assets/assets/img/grocery/food_img.png')}}">
        									</div>
        								</div>
        								<div class="col-6" style="display:none">
        									<a href="#">
        										<div class="essntl_dv_ryt">
        											<div class="wht_crcl">
        												<img class="ryt_arw_img" src="{{url('assets/assets/img/grocery/rytArw.svg')}}">
        											</div>
        										</div>
        									</a>
        								</div>
        							</div>
        						</div>
        					</div>
        				</div>
        				<div class="col-12 col-md-6">
        					<div class="card card_hyt card_bg_yelw">
        						<div class="card-body">
        							<h3 class="essentials_card_h3">Beverages</h3>  
        							<div class="row">
        								<div class="col-6">
        									<div class="essntl_dv_lft">
        										<img src="{{url('assets/assets/img/grocery/beve_img.png')}}">
        									</div>
        								</div>
        								<div class="col-6" style="display:none">
        									<a href="#">
        										<div class="essntl_dv_ryt">
        											<div class="wht_crcl">
        												<img class="ryt_arw_img" src="{{url('assets/assets/img/grocery/rytArw.svg')}}">
        											</div>
        										</div>
        									</a>
        								</div>
        							</div>
        						</div>
        					</div>
        				</div>
        				<div class="col-12 col-md-6">
        					<div class="card card_hyt card_bg_red">
        						<div class="card-body">
        							<h3 class="essentials_card_h3">Meat</h3>  
        							<div class="row">
        								<div class="col-6">
        									<div class="essntl_dv_lft">
        										<img src="{{url('assets/assets/img/grocery/meat_img.png')}}">
        									</div>
        								</div>
        								<div class="col-6" style="display:none">
        									<a href="#">
        										<div class="essntl_dv_ryt">
        											<div class="wht_crcl">
        												<img class="ryt_arw_img" src="{{url('assets/assets/img/grocery/rytArw.svg')}}">
        											</div>
        										</div>
        									</a>
        								</div>
        							</div>
        						</div>
        					</div>
        				</div>
        				<div class="col-12 col-md-6">
        					<div class="card card_hyt card_bg_blue">
        						<div class="card-body">
        							<h3 class="essentials_card_h3">Other</h3>  
        							<div class="row">
        								<div class="col-6">
        									<div class="essntl_dv_lft">
        										<img src="{{url('assets/assets/img/grocery/oth_img.png')}}">
        									</div>
        								</div>
        								<div class="col-6" style="display:none">
        									<a href="#">
        										<div class="essntl_dv_ryt">
        											<div class="wht_crcl">
        												<img class="ryt_arw_img" src="{{url('assets/assets/img/grocery/rytArw.svg')}}">
        											</div>
        										</div>
        									</a>
        								</div>
        							</div>
        						</div>
        					</div>
        				</div>
        			</div>
        		</div>
        	</section>
			<!-- ======================== New Section Essential at your door end ==================== -->
			<!-- =========================== Customer Reviews =================================== -->
			<section class="bg-cover" style="background:#eff8e7 url(assets/img/gro-trans.) no-repeat">
				<div class="container">
					
					<div class="row justify-content-center">
						<div class="col-lg-10 col-md-10 col-sm-12">
							<div class="sec-heading">
								<div class="sec-heading center">
									<span class="theme-cl">In a free hour, when our power of choice mettal</span>
									<h2 class="grocery_title">Loving Customers</h2>
								</div>
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-lg-12 col-md-12">
							
							<div class="customers-carousel owl-carousel owl-theme products-slider " id="customers-carousel">
								
								<!-- single testimonial -->
								<div class="single_customers_box">
									<div class="row justify-content-center">
										<div class="col-lg-7 col-md-7">
											<div class="single_customers_wraps">
												<div class="single_customers_caption">
													<div class="quote_icon_2"><i class="fas fa-quote-right"></i></div>
													<p class="customers_description">My favorite things about ayesz are is its leadership, culture, and the leadership’s commitment to following and implementing culture. Employees hold our top management in very high regard. Due to this and our guiding values, we all work together towards known, measurable and common goals. We work hard, and we all get to share in the successes and rewards. The scope in my role is endless due to regular interactions with multiple functions of Digital Marketing, Web designing, Technology, Supply Chain Management & Packaging, Logic & Algorithm, Backward Integration, etc. At ayesz, “complacency” is taken away from a person’s job or role as the concept of ‘Kaizen’ is always followed. Unlimited innovations help in an exponential learning curve.</p>
													 <div class="review_author_box">
														<div class="reviews_img">
															<img src="{{url('assets/assets/img/grocery/pfl.png')}}" class="img-fluid" alt="" />	
														</div>
														<div class="reviews_caption">
															<h4 class="testi2_title">Prasad</h4>
															<span>Director</span>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								
								<!-- single testimonial -->
								<div class="single_customers_box">								
									<div class="row justify-content-center">
										<div class="col-lg-7 col-md-7">
											<div class="single_customers_wraps">
												<div class="single_customers_caption">
													<div class="quote_icon_2"><i class="fas fa-quote-right"></i></div>
													<p class="customers_description">One of the many things I love about ayesz is that each day is “New”. In the yearsI’ve been here, not a single day has been the same. The opportunities to take onmore and learn new things are endless. I have worked in large retail groups, MNCs,Large Indian FMCG Company for years. I have never felt so great about coming tooffice every day. I don’t even remember what Monday blues is anymore & all of thisis because of culture that cares about you & makes you feel valued. Where all aretreated as equal. The senior management is so down to earth & modest. You dowhatever it takes to deliver ethically. It also gives you freedom to create. Freedomto do/try new things. Here I “Create” rather than just “Consume”. If you have thepassion to create & an eye for detail, come join my team.</p>
													<div class="review_author_box">
														<div class="reviews_img">
															<img src="{{url('assets/assets/img/grocery/pfl.png')}}" class="img-fluid" alt="" />	
														</div>
														<div class="reviews_caption">
															<h4 class="testi2_title">Sudarshan</h4>
															<span>Director (Head – Marketing)</span>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="single_customers_box">								
									<div class="row justify-content-center">
										<div class="col-lg-7 col-md-7">
											<div class="single_customers_wraps">
												<div class="single_customers_caption">
													<div class="quote_icon_2"><i class="fas fa-quote-right"></i></div>
													<p class="customers_description">“Chaos is a Ladder” they say. AyesZ provides you with an opportunity to ride that ladder to the top. AyesZ provides an ideal work environment for people who like working without a water tight compartmentalization of their JDs. It believes in providing people with freedom to work as long as we exhibit a sense of responsibility. There are so many processes to ensure that the task goes smoothly but still no process is so sacrosanct that it can’t be questioned for improvement. There is a constant flux of ideas which leads to a continuous improvement in processes. To summarize, life @ ayesz is one of structured chaos and helps in bringing out the best of professional talent of every individual.</p>
													<div class="review_author_box">
														<div class="reviews_img">
															<img src="{{url('assets/assets/img/grocery/pfl.png')}}" class="img-fluid" alt="" />	
														</div>
														<div class="reviews_caption">
															<h4 class="testi2_title">Sudheer</h4>
															<span>HR & Training</span>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<!-- single testimonial -->
								<div class="single_customers_box">
									<div class="row justify-content-center">
										<div class="col-lg-7 col-md-7">
											<div class="single_customers_wraps">
												<div class="single_customers_caption">
													<div class="quote_icon_2"><i class="fas fa-quote-right"></i></div>
													<p class="customers_description">I wasn't completely sure, what to expect when I accepted this role, but having beenhere for some time now, the journey has been a revelation. This company has beenbuilt on one passion - "customer delight" and that permeates through everythingthat we do, be it technology or supply chain. The pace is furious and relentless,every decision is debated vociferously, but is made fast and executed upon withzest. Engineering challenges are complex and multi-layered and have the potentialto stimulate every thinking technologist. We are at the cusp of something large andrevolutionary, come help create the technology backbone that will connect India'sfarmers with professionals and homemakers as they go about their daily life.</p>
													<div class="review_author_box">
														<div class="reviews_img">
															<img src="{{url('assets/assets/img/grocery/pfl.png')}}" class="img-fluid" alt="" />	
														</div>
														<div class="reviews_caption">
															<h4 class="testi2_title">Mahesh</h4>
															<span>Vice President – Engineering</span>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								
							</div>
							
						</div>						
					</div>
				</div>
			</section>
			<!-- =========================== Customer Reviews =================================== -->
			
			<!-- ======================== Fresh & Fast Fruits Start ==================== -->
			<section style="display:none;">
				<div class="container">
					
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12">
							<div class="sec-heading-flex pl-2 pr-2">
								<div class="sec-heading-flex-one">

									<h2>Fresh & Fast Fruits</h2>
								</div>
								<div class="sec-heading-flex-last">
									<a href="#" class="btn btn-theme">View More<i class="ti-arrow-right ml-2"></i></a>
								</div>
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-lg-12 col-md-12">
							<div class="owl-carousel products-slider owl-theme">
							
								<!-- Single Item -->
								<div class="item">
									<div class="woo_product_grid">
										<div class="woo_product_thumb">
											<img src="{{url('assets/assets/img/fruits/1.png')}}" class="img-fluid" alt="" />
										</div>
										<div class="woo_product_caption center">
											<div class="woo_rate">
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star"></i>
											</div>
											<div class="woo_title">
												<h4 class="woo_pro_title"><a href="detail-1.html">Accumsan Tree Fusce</a></h4>
											</div>
											<div class="woo_price">
												<h6>$72.47<span class="less_price">$112.10</span></h6>
											</div>
										</div>
										<div class="woo_product_cart hover">
											<!-- <ul>
												<li><a href="javascript:void(0);" data-toggle="modal" data-target="#viewproduct-over" class="woo_cart_btn btn_cart"><i class="ti-eye"></i></a></li>
												<li><a href="add-to-cart.html" class="woo_cart_btn btn_view"><i class="ti-shopping-cart"></i></a></li>
												<li><a href="wishlist.html" class="woo_cart_btn btn_save"><i class="ti-heart"></i></a></li>
											</ul> -->
										</div>								
									</div>
								</div>
								
								<!-- Single Item -->
								<div class="item">
									<div class="woo_product_grid">
										<span class="woo_pr_tag hot">Hot</span>
										<div class="woo_product_thumb">
											<img src="{{url('assets/assets/img/fruits/2.png')}}" class="img-fluid" alt="" />
										</div>
										<div class="woo_product_caption center">
											<div class="woo_rate">
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star"></i>
											</div>
											<div class="woo_title">
												<h4 class="woo_pro_title"><a href="detail-1.html">Accumsan Tree Fusce</a></h4>
											</div>
											<div class="woo_price">
												<h6>$72.47<span class="less_price">$112.10</span></h6>
											</div>
										</div>
										<div class="woo_product_cart hover">
											<!-- <ul>
												<li><a href="javascript:void(0);" data-toggle="modal" data-target="#viewproduct-over" class="woo_cart_btn btn_cart"><i class="ti-eye"></i></a></li>
												<li><a href="add-to-cart.html" class="woo_cart_btn btn_view"><i class="ti-shopping-cart"></i></a></li>
												<li><a href="wishlist.html" class="woo_cart_btn btn_save"><i class="ti-heart"></i></a></li>
											</ul> -->
										</div>								
									</div>
								</div>
								
								<!-- Single Item -->
								<div class="item">
									<div class="woo_product_grid">
										<div class="woo_product_thumb">
											<img src="{{url('assets/assets/img/fruits/3.png')}}" class="img-fluid" alt="" />
										</div>
										<div class="woo_product_caption center">
											<div class="woo_rate">
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star"></i>
											</div>
											<div class="woo_title">
												<h4 class="woo_pro_title"><a href="detail-1.html">Accumsan Tree Fusce</a></h4>
											</div>
											<div class="woo_price">
												<h6>$72.47<span class="less_price">$112.10</span></h6>
											</div>
										</div>
										<div class="woo_product_cart hover">
											<!-- <ul>
												<li><a href="javascript:void(0);" data-toggle="modal" data-target="#viewproduct-over" class="woo_cart_btn btn_cart"><i class="ti-eye"></i></a></li>
												<li><a href="add-to-cart.html" class="woo_cart_btn btn_view"><i class="ti-shopping-cart"></i></a></li>
												<li><a href="wishlist.html" class="woo_cart_btn btn_save"><i class="ti-heart"></i></a></li>
											</ul> -->
										</div>								
									</div>
								</div>
								
								<!-- Single Item -->
								<div class="item">
									<div class="woo_product_grid">
										<span class="woo_pr_tag new">New</span>
										<div class="woo_product_thumb">
											<img src="{{url('assets/assets/img/fruits/4.png')}}" class="img-fluid" alt="" />
										</div>
										<div class="woo_product_caption center">
											<div class="woo_rate">
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star"></i>
											</div>
											<div class="woo_title">
												<h4 class="woo_pro_title"><a href="detail-1.html">Accumsan Tree Fusce</a></h4>
											</div>
											<div class="woo_price">
												<h6>$72.47<span class="less_price">$112.10</span></h6>
											</div>
										</div>
										<div class="woo_product_cart hover">
											<!-- <ul>
												<li><a href="javascript:void(0);" data-toggle="modal" data-target="#viewproduct-over" class="woo_cart_btn btn_cart"><i class="ti-eye"></i></a></li>
												<li><a href="add-to-cart.html" class="woo_cart_btn btn_view"><i class="ti-shopping-cart"></i></a></li>
												<li><a href="wishlist.html" class="woo_cart_btn btn_save"><i class="ti-heart"></i></a></li>
											</ul> -->
										</div>								
									</div>
								</div>
								
								<!-- Single Item -->
								<div class="item">
									<div class="woo_product_grid">
										<div class="woo_product_thumb">
											<img src="{{url('assets/assets/img/fruits/5.png')}}" class="img-fluid" alt="" />
										</div>
										<div class="woo_product_caption center">
											<div class="woo_rate">
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star"></i>
											</div>
											<div class="woo_title">
												<h4 class="woo_pro_title"><a href="detail-1.html">Accumsan Tree Fusce</a></h4>
											</div>
											<div class="woo_price">
												<h6>$72.47<span class="less_price">$112.10</span></h6>
											</div>
										</div>
										<div class="woo_product_cart hover">
											<!-- <ul>
												<li><a href="javascript:void(0);" data-toggle="modal" data-target="#viewproduct-over" class="woo_cart_btn btn_cart"><i class="ti-eye"></i></a></li>
												<li><a href="add-to-cart.html" class="woo_cart_btn btn_view"><i class="ti-shopping-cart"></i></a></li>
												<li><a href="wishlist.html" class="woo_cart_btn btn_save"><i class="ti-heart"></i></a></li>
											</ul> -->
										</div>								
									</div>
								</div>
								
								<!-- Single Item -->
								<div class="item">
									<div class="woo_product_grid">
										<span class="woo_pr_tag hot">Hot</span>
										<div class="woo_product_thumb">
											<img src="{{url('assets/assets/img/fruits/6.png')}}" class="img-fluid" alt="" />
										</div>
										<div class="woo_product_caption center">
											<div class="woo_rate">
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star"></i>
											</div>
											<div class="woo_title">
												<h4 class="woo_pro_title"><a href="detail-1.html">Accumsan Tree Fusce</a></h4>
											</div>
											<div class="woo_price">
												<h6>$72.47<span class="less_price">$112.10</span></h6>
											</div>
										</div>
										<div class="woo_product_cart hover">
											<!-- <ul>
												<li><a href="javascript:void(0);" data-toggle="modal" data-target="#viewproduct-over" class="woo_cart_btn btn_cart"><i class="ti-eye"></i></a></li>
												<li><a href="add-to-cart.html" class="woo_cart_btn btn_view"><i class="ti-shopping-cart"></i></a></li>
												<li><a href="wishlist.html" class="woo_cart_btn btn_save"><i class="ti-heart"></i></a></li>
											</ul> -->
										</div>								
									</div>
								</div>
								
								<!-- Single Item -->
								<div class="item">
									<div class="woo_product_grid">
										<div class="woo_product_thumb">
											<img src="{{url('assets/assets/img/fruits/7.png')}}" class="img-fluid" alt="" />
										</div>
										<div class="woo_product_caption center">
											<div class="woo_rate">
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star"></i>
											</div>
											<div class="woo_title">
												<h4 class="woo_pro_title"><a href="detail-1.html">Accumsan Tree Fusce</a></h4>
											</div>
											<div class="woo_price">
												<h6>$72.47<span class="less_price">$112.10</span></h6>
											</div>
										</div>
										<div class="woo_product_cart hover">
											<!-- <ul>
												<li><a href="javascript:void(0);" data-toggle="modal" data-target="#viewproduct-over" class="woo_cart_btn btn_cart"><i class="ti-eye"></i></a></li>
												<li><a href="add-to-cart.html" class="woo_cart_btn btn_view"><i class="ti-shopping-cart"></i></a></li>
												<li><a href="wishlist.html" class="woo_cart_btn btn_save"><i class="ti-heart"></i></a></li>
											</ul> -->
										</div>								
									</div>
								</div>
								
								<!-- Single Item -->
								<div class="item">
									<div class="woo_product_grid">
										<span class="woo_offer_sell">Save 10% Off</span>
										<div class="woo_product_thumb">
											<img src="{{url('assets/assets/img/fruits/8.png')}}" class="img-fluid" alt="" />
										</div>
										<div class="woo_product_caption center">
											<div class="woo_rate">
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star"></i>
											</div>
											<div class="woo_title">
												<h4 class="woo_pro_title"><a href="detail-1.html">Accumsan Tree Fusce</a></h4>
											</div>
											<div class="woo_price">
												<h6>$72.47<span class="less_price">$112.10</span></h6>
											</div>
										</div>
										<div class="woo_product_cart hover">
											<!-- <ul>
												<li><a href="javascript:void(0);" data-toggle="modal" data-target="#viewproduct-over" class="woo_cart_btn btn_cart"><i class="ti-eye"></i></a></li>
												<li><a href="add-to-cart.html" class="woo_cart_btn btn_view"><i class="ti-shopping-cart"></i></a></li>
												<li><a href="wishlist.html" class="woo_cart_btn btn_save"><i class="ti-heart"></i></a></li>
											</ul> -->
										</div>								
									</div>
								</div>
							
							</div>
						</div>
					</div>
				</div>
			</section>
			<div class="clearfix"></div>
			<!-- ======================== Fresh & Fast Fruits End ==================== -->
			
			<!-- ======================== Offer Banner Start ==================== -->
			<section class="offer-banner-wrap gray" style="display:none;">
				<div class="container-fluid">
					<div class="row">
						<div class="col-lg-12 col-md-12">
							<div class="owl-carousel banner-offers owl-theme">
								
								<!-- Single Item -->
								<div class="item">
									<div class="offer_item">
										<div class="offer_item_thumb">
											<div class="offer-overlay"></div>
											<img src="{{url('assets/assets/img/offer-1.jpg')}}" alt="">
										</div>
										<div class="offer_caption">
											<div class="offer_bottom_caption">
												<p>10% Off</p>
												<div class="offer_title">Good Deals in Your City</div>
												<span>Save 10% on All Fruits</span>
											</div>
											<a href="#" class="btn offer_box_btn">Explore Now</a>
										</div>
									</div>
								</div>
								
								<!-- Single Item -->
								<div class="item">
									<div class="offer_item">
										<div class="offer_item_thumb">
											<div class="offer-overlay"></div>
											<img src="{{url('assets/assets/img/offer-2.jpg')}}" alt="">
										</div>
										<div class="offer_caption">
											<div class="offer_bottom_caption">
												<p>25% Off</p>
												<div class="offer_title">Good Offer on First Time</div>
												<span>Save 25% on Fresh Vegetables</span>
											</div>
											<a href="#" class="btn offer_box_btn">Explore Now</a>
										</div>
									</div>
								</div>
								
								<!-- Single Item -->
								<div class="item">
									<div class="offer_item">
										<div class="offer_item_thumb">
											<div class="offer-overlay"></div>
											<img src="{{url('assets/assets/img/offer-3.jpg')}}" alt="">
										</div>
										<div class="offer_caption">
											<div class="offer_bottom_caption">
												<p>30% Off</p>
												<div class="offer_title">Super Market Deals</div>
												<span>Save 30% on Eggs & Dairy</span>
											</div>
											<a href="#" class="btn offer_box_btn">Explore Now</a>
										</div>
									</div>
								</div>
								
								<!-- Single Item -->
								<div class="item">
									<div class="offer_item">
										<div class="offer_item_thumb">
											<div class="offer-overlay"></div>
											<img src="{{url('assets/assets/img/offer-4.jpg')}}" alt="">
										</div>
										<div class="offer_caption">
											<div class="offer_bottom_caption">
												<p>15% Off</p>
												<div class="offer_title">Better Offer for You</div>
												<span>Save More Thank 15%</span>
											</div>
											<a href="#" class="btn offer_box_btn">Explore Now</a>
										</div>
									</div>
								</div>
								
								<!-- Single Item -->
								<div class="item">
									<div class="offer_item">
										<div class="offer_item_thumb">
											<div class="offer-overlay"></div>
											<img src="{{url('assets/assets/img/offer-5.jpg')}}" alt="">
										</div>
										<div class="offer_caption">
											<div class="offer_bottom_caption">
												<p>40% Off</p>
												<div class="offer_title">Super Market Deals</div>
												<span>40% Off on All Dry Foods</span>
											</div>
											<a href="#" class="btn offer_box_btn">Explore Now</a>
										</div>
									</div>
								</div>
								
								<!-- Single Item -->
								<div class="item">
									<div class="offer_item">
										<div class="offer_item_thumb">
											<div class="offer-overlay"></div>
											<img src="{{url('assets/assets/img/offer-6.jpg')}}" alt="">
										</div>
										<div class="offer_caption">
											<div class="offer_bottom_caption">
												<p>15% Off</p>
												<div class="offer_title">Better Offer for You</div>
												<span>Drinking is Goodness for Health</span>
											</div>
											<a href="#" class="btn offer_box_btn">Explore Now</a>
										</div>
									</div>
								</div>
								
							</div>
						</div>
					</div>
				</div>
			</section>
			<div class="clearfix"></div>
			<!-- ======================== Offer Banner End ==================== -->
			
			<!-- ======================== Fresh Vegetables & Fruits Start ==================== -->
			<section class="" style="display:none;">
				<div class="container">
					
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12">
							<div class="sec-heading-flex pl-2 pr-2">
								<div class="sec-heading-flex-one">
									<h2>Added new Products</h2>
								</div>
								<div class="sec-heading-flex-last">
									<a href="#" class="btn btn-theme">View More<i class="ti-arrow-right ml-2"></i></a>
								</div>
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-lg-12 col-md-12">
							<div class="owl-carousel products-slider owl-theme">
							
								<!-- Single Item -->
								<div class="item">
									<div class="woo_product_grid">
										<span class="woo_pr_tag hot">Hot</span>
										<div class="woo_product_thumb">
											<img src="{{url('assets/assets/img/grocery/1.png')}}" class="img-fluid" alt="" />
										</div>
										<div class="woo_product_caption center">
											<div class="woo_rate">
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star"></i>
											</div>
											<div class="woo_title">
												<h4 class="woo_pro_title"><a href="detail-1.html">Accumsan Tree Fusce</a></h4>
											</div>
											<div class="woo_price">
												<h6>$72.47<span class="less_price">$112.10</span></h6>
											</div>
										</div>
										<div class="woo_product_cart hover">
											<!-- <ul>
												<li><a href="javascript:void(0);" data-toggle="modal" data-target="#viewproduct-over" class="woo_cart_btn btn_cart"><i class="ti-eye"></i></a></li>
												<li><a href="add-to-cart.html" class="woo_cart_btn btn_view"><i class="ti-shopping-cart"></i></a></li>
												<li><a href="wishlist.html" class="woo_cart_btn btn_save"><i class="ti-heart"></i></a></li>
											</ul> -->
										</div>								
									</div>
								</div>
								
								<!-- Single Item -->
								<div class="item">
									<div class="woo_product_grid">
										<div class="woo_product_thumb">
											<img src="{{url('assets/assets/img/grocery/2.png')}}" class="img-fluid" alt="" />
										</div>
										<div class="woo_product_caption center">
											<div class="woo_rate">
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star"></i>
											</div>
											<div class="woo_title">
												<h4 class="woo_pro_title"><a href="detail-1.html">Accumsan Tree Fusce</a></h4>
											</div>
											<div class="woo_price">
												<h6>$72.47<span class="less_price">$112.10</span></h6>
											</div>
										</div>
										<div class="woo_product_cart hover">
											<!-- <ul>
												<li><a href="javascript:void(0);" data-toggle="modal" data-target="#viewproduct-over" class="woo_cart_btn btn_cart"><i class="ti-eye"></i></a></li>
												<li><a href="add-to-cart.html" class="woo_cart_btn btn_view"><i class="ti-shopping-cart"></i></a></li>
												<li><a href="wishlist.html" class="woo_cart_btn btn_save"><i class="ti-heart"></i></a></li>
											</ul> -->
										</div>								
									</div>
								</div>
								
								<!-- Single Item -->
								<div class="item">
									<div class="woo_product_grid">
										<span class="woo_pr_tag new">New</span>
										<div class="woo_product_thumb">
											<img src="{{url('assets/assets/img/grocery/3.png')}}" class="img-fluid" alt="" />
										</div>
										<div class="woo_product_caption center">
											<div class="woo_rate">
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star"></i>
											</div>
											<div class="woo_title">
												<h4 class="woo_pro_title"><a href="detail-1.html">Accumsan Tree Fusce</a></h4>
											</div>
											<div class="woo_price">
												<h6>$72.47<span class="less_price">$112.10</span></h6>
											</div>
										</div>
										<div class="woo_product_cart hover">
											<!-- <ul>
												<li><a href="javascript:void(0);" data-toggle="modal" data-target="#viewproduct-over" class="woo_cart_btn btn_cart"><i class="ti-eye"></i></a></li>
												<li><a href="add-to-cart.html" class="woo_cart_btn btn_view"><i class="ti-shopping-cart"></i></a></li>
												<li><a href="wishlist.html" class="woo_cart_btn btn_save"><i class="ti-heart"></i></a></li>
											</ul> -->
										</div>								
									</div>
								</div>
								
								<!-- Single Item -->
								<div class="item">
									<div class="woo_product_grid">
										<div class="woo_product_thumb">
											<img src="{{url('assets/assets/img/grocery/4.png')}}" class="img-fluid" alt="" />
										</div>
										<div class="woo_product_caption center">
											<div class="woo_rate">
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star"></i>
											</div>
											<div class="woo_title">
												<h4 class="woo_pro_title"><a href="detail-1.html">Accumsan Tree Fusce</a></h4>
											</div>
											<div class="woo_price">
												<h6>$72.47<span class="less_price">$112.10</span></h6>
											</div>
										</div>
										<div class="woo_product_cart hover">
											<!-- <ul>
												<li><a href="javascript:void(0);" data-toggle="modal" data-target="#viewproduct-over" class="woo_cart_btn btn_cart"><i class="ti-eye"></i></a></li>
												<li><a href="add-to-cart.html" class="woo_cart_btn btn_view"><i class="ti-shopping-cart"></i></a></li>
												<li><a href="wishlist.html" class="woo_cart_btn btn_save"><i class="ti-heart"></i></a></li>
											</ul> -->
										</div>								
									</div>
								</div>
								
								<!-- Single Item -->
								<div class="item">
									<div class="woo_product_grid">
										<span class="woo_offer_sell">Save 10% Off</span>
										<div class="woo_product_thumb">
											<img src="{{url('assets/assets/img/grocery/5.png')}}" class="img-fluid" alt="" />
										</div>
										<div class="woo_product_caption center">
											<div class="woo_rate">
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star"></i>
											</div>
											<div class="woo_title">
												<h4 class="woo_pro_title"><a href="detail-1.html">Accumsan Tree Fusce</a></h4>
											</div>
											<div class="woo_price">
												<h6>$72.47<span class="less_price">$112.10</span></h6>
											</div>
										</div>
										<div class="woo_product_cart hover">
											<!-- <ul>
												<li><a href="javascript:void(0);" data-toggle="modal" data-target="#viewproduct-over" class="woo_cart_btn btn_cart"><i class="ti-eye"></i></a></li>
												<li><a href="add-to-cart.html" class="woo_cart_btn btn_view"><i class="ti-shopping-cart"></i></a></li>
												<li><a href="wishlist.html" class="woo_cart_btn btn_save"><i class="ti-heart"></i></a></li>
											</ul> -->
										</div>								
									</div>
								</div>
								
								<!-- Single Item -->
								<div class="item">
									<div class="woo_product_grid">
										<span class="woo_pr_tag hot">Hot</span>
										<div class="woo_product_thumb">
											<img src="{{url('assets/assets/img/grocery/6.png')}}" class="img-fluid" alt="" />
										</div>
										<div class="woo_product_caption center">
											<div class="woo_rate">
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star"></i>
											</div>
											<div class="woo_title">
												<h4 class="woo_pro_title"><a href="detail-1.html">Accumsan Tree Fusce</a></h4>
											</div>
											<div class="woo_price">
												<h6>$72.47<span class="less_price">$112.10</span></h6>
											</div>
										</div>
										<div class="woo_product_cart hover">
											<!-- <ul>
												<li><a href="javascript:void(0);" data-toggle="modal" data-target="#viewproduct-over" class="woo_cart_btn btn_cart"><i class="ti-eye"></i></a></li>
												<li><a href="add-to-cart.html" class="woo_cart_btn btn_view"><i class="ti-shopping-cart"></i></a></li>
												<li><a href="wishlist.html" class="woo_cart_btn btn_save"><i class="ti-heart"></i></a></li>
											</ul> -->
										</div>								
									</div>
								</div>
								
								<!-- Single Item -->
								<div class="item">
									<div class="woo_product_grid">
										<div class="woo_product_thumb">
											<img src="{{url('assets/assets/img/grocery/7.png')}}" class="img-fluid" alt="" />
										</div>
										<div class="woo_product_caption center">
											<div class="woo_rate">
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star"></i>
											</div>
											<div class="woo_title">
												<h4 class="woo_pro_title"><a href="detail-1.html">Accumsan Tree Fusce</a></h4>
											</div>
											<div class="woo_price">
												<h6>$72.47<span class="less_price">$112.10</span></h6>
											</div>
										</div>
										<div class="woo_product_cart hover">
											<!-- <ul>
												<li><a href="javascript:void(0);" data-toggle="modal" data-target="#viewproduct-over" class="woo_cart_btn btn_cart"><i class="ti-eye"></i></a></li>
												<li><a href="add-to-cart.html" class="woo_cart_btn btn_view"><i class="ti-shopping-cart"></i></a></li>
												<li><a href="wishlist.html" class="woo_cart_btn btn_save"><i class="ti-heart"></i></a></li>
											</ul> -->
										</div>								
									</div>
								</div>
								
								<!-- Single Item -->
								<div class="item">
									<div class="woo_product_grid">
										<span class="woo_pr_tag new">New</span>
										<div class="woo_product_thumb">
											<img src="{{url('assets/assets/img/grocery/8.png')}}" class="img-fluid" alt="" />
										</div>
										<div class="woo_product_caption center">
											<div class="woo_rate">
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star filled"></i>
												<i class="fa fa-star"></i>
											</div>
											<div class="woo_title">
												<h4 class="woo_pro_title"><a href="detail-1.html">Accumsan Tree Fusce</a></h4>
											</div>
											<div class="woo_price">
												<h6>$72.47<span class="less_price">$112.10</span></h6>
											</div>
										</div>
										<div class="woo_product_cart hover">
											<!-- <ul>
												<li><a href="javascript:void(0);" data-toggle="modal" data-target="#viewproduct-over" class="woo_cart_btn btn_cart"><i class="ti-eye"></i></a></li>
												<li><a href="add-to-cart.html" class="woo_cart_btn btn_view"><i class="ti-shopping-cart"></i></a></li>
												<li><a href="wishlist.html" class="woo_cart_btn btn_save"><i class="ti-heart"></i></a></li>
											</ul> -->
										</div>								
									</div>
								</div>
							
							</div>
						</div>
					</div>
				</div>
			</section>
			<div class="clearfix"></div>
			<!-- ======================== Fresh Vegetables & Fruits End ==================== -->
			
			
			<!-- ============================ Call To Action ================================== -->
			<section class="theme-bg call_action_wrap-wrap">
				<div class="container">
					<div class="row">
						<div class="col-lg-12">
							
							<div class="call_action_wrap">
								<div class="call_action_wrap-head">
									<h3>Do You Have Questions ?</h3>
									<span>We'll help you to grow your career and growth.</span>
								</div>
								<div class="newsletter_box">
									<div class="input-group">
										<input type="text" class="form-control" placeholder="Subscribe here...">
										<div class="input-group-append">
										<button class="btn search_btn" type="button"><i class="fas fa-arrow-alt-circle-right"></i></button>
										</div>
									</div>
								</div>
							</div>
							
						</div>
					</div>
				</div>
			</section>
			<!-- ============================ Call To Action End ================================== -->
			
			<!-- ============================ Footer Start ================================== -->
			<footer class="dark-footer skin-dark-footer style-2">
				<div class="before-footer">
					<div class="container">
						<div class="row">
					
							<div class="col-lg-4 col-md-4">
								<div class="single_facts">
									<div class="facts_icon">
										<i class="ti-shopping-cart"></i>
									</div>
									<div class="facts_caption">
										<h4>Home Delivery</h4>
										<p>we will deliver at your Door Step</p>
									</div>
								</div>
							</div>
							
							<div class="col-lg-4 col-md-4">
								<div class="single_facts">
									<div class="facts_icon">
										<i class="ti-money"></i>
									</div>
									<div class="facts_caption">
										<h4>Money Back Guarantee</h4>
										
										<p>Quick Refunds if any issues</p>
									</div>
								</div>
							</div>
							
							<div class="col-lg-4 col-md-4">
								<div class="single_facts last">
									<div class="facts_icon">
										<i class="ti-headphone-alt"></i>
									</div>
									<div class="facts_caption">
										<h4>Tele Support</h4>
										<p>Reach out to us anytime</p>
									</div>
								</div>
							</div>
							
						</div>
					</div>
				</div>
				
				<div class="footer-middle">
					<div class="container">
						<div class="row">
							
							<div class="col-lg-4 col-md-4">
								<div class="footer_widget">
									<h4 class="extream">Contact us</h4>
									<p>Let's here all about it! <a href="#" class="theme-cl">Get it touch</a></p>
									
									<div class="address_infos">
										<ul>
											<li><i class="ti-home theme-cl"></i>Reg. Office: - 5/106/73-4, Sai Nagar, Muddanur, Cuddapah Dist., Andhra Pradesh, India – 516380, www.ayesz.in</li>
											<li><i class="ti-home theme-cl"></i>Corp. Office: - 10/280-195, R.No.7, Bypass Road, Opp: CineHub, Kothapalli Panchayati,Proddatur, Kadapa (Dt) – 516360</li>
											<li><i class="ti-headphone-alt theme-cl"></i><a href="tel:+9133669268"> 9133669268</a></li>
											
											<li style="padding-left:0;display:none"><a href="http://ayesz.in/ayesz_vendor.apk" class="downloadLink" download>Download Vendor App</a></li>
											<li style="padding-left:0;display:none"><a href="http://ayesz.in/ayesz_delivery.apk" class="downloadLink" download>Download Delivery App</a></li>
										</ul>
									</div>
									
								</div>
							</div>
									
							<div class="col-lg-2 col-md-2">
								<div class="footer_widget">
									<h4 class="widget_title">Categories</h4>
									<ul class="footer-menu">
										<li><a href="#">Organic</a></li>
										<li><a href="#">Grocery</a></li>
										<li><a href="#">Education</a></li>
										<li><a href="#">Furniture</a></li>
									</ul>
								</div>
							</div>
									
							<div class="col-lg-2 col-md-2">
								<div class="footer_widget">
									<h4 class="widget_title">Our Company</h4>
									<ul class="footer-menu">
										<li><a href="http://ayesz.in/about-us">About Us</a></li>
										<li><a href="#">My company</a></li>
										<li><a href="http://ayesz.in/career-opportunities">Opportunities</a></li>
										<li><a href="#">Gallery</a></li>
									</ul>
								</div>
							</div>
							
							<div class="col-lg-2 col-md-2">
								<div class="footer_widget">
									<h4 class="widget_title">Latest News</h4>
									<ul class="footer-menu">
										<li><a href="#">Offers & Deals</a></li>
										<li><a href="#">My Account</a></li>
										<li><a href="#">My Products</a></li>
										<li><a href="#">Favorites</a></li>
									</ul>
								</div>
							</div>
							
							<div class="col-lg-2 col-md-2">
								<div class="footer_widget">
									<h4 class="widget_title">Customer Support</h4>
									<ul class="footer-menu">
										<li><a href="http://ayesz.in/terms-conditions">Terms & Conditions</a></li>
										<li><a href="http://ayesz.in/privacy-policy">Privacy Policy</a></li>
										<li><a href="#">Blog</a></li>
										<li><a href="#">FAQs</a></li>
									</ul>
								</div>
							</div>
							
						</div>
					</div>
				</div>
				
				<div class="footer-bottom">
					<div class="container">
						<div class="row align-items-center">
							
							<div class="col-lg-6 col-md-8">
								<p class="mb-0">©Copyright 2020 AyesZ. Designd By <a href="https://techrefic.com/">Techrefic</a>.</p>
							</div>
							
							<div class="col-lg-6 col-md-6 text-right">
								<ul class="footer_social_links">
									<li><a href="https://www.facebook.com/bmba06" target="_blank"><i class="ti-facebook"></i></a></li>
									<li><a href="#"><i class="ti-twitter"></i></a></li>
									<li><a href="#"><i class="ti-instagram"></i></a></li>
									<li><a href="#"><i class="ti-linkedin"></i></a></li>
								</ul>
							</div>
							
						</div>
					</div>
				</div>
			</footer>
			<!-- ============================ Footer End ================================== -->
			
			
			

		</div>
		<!-- ============================================================== -->
		<!-- End Wrapper -->
		<!-- ============================================================== -->

		<!-- ============================================================== -->
		<!-- All Jquery -->
		<!-- ============================================================== -->
		<script src="{{url('assets/assets/js/jquery.min.js')}}"></script>
		<script src="{{url('assets/assets/js/popper.min.js')}}"></script>
		<script src="{{url('assets/assets/js/bootstrap.min.js')}}"></script>
		<script src="{{url('assets/assets/js/metisMenu.min.js')}}"></script>
		<script src="{{url('assets/assets/js/owl-carousel.js')}}"></script>
		<script src="{{url('assets/assets/js/ion.rangeSlider.min.js')}}"></script>
		<script src="{{url('assets/assets/js/smoothproducts.js')}}"></script>
		<script src="{{url('assets/assets/js/jquery-rating.js')}}"></script>
		<script src="{{url('assets/assets/js/jQuery.style.switcher.js')}}"></script>
		<script src="{{url('assets/assets/js/custom.js')}}"></script>
		<!-- ============================================================== -->
		<!-- This page plugins -->
		<!-- ============================================================== -->
		
		<script>
			function openRightMenu() {
				document.getElementById("rightMenu").style.display = "block";
			}
			function closeRightMenu() {
				document.getElementById("rightMenu").style.display = "none";
			}
		</script>

	</body>
</html>