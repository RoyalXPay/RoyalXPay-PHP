<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
    <!-- right side menu area start -->
    <div id="st-2" class=" st-sticky-share-buttons st-right st-toggleable st-has-labels st-show-total">
        <div class="st-total">
            <span class="st-label">2.8k</span>
            <span class="st-shares">Shares</span>
        </div>
        <div class="st-btn st-first" data-network="facebook" style="display: inline-block;">
            <span class="st-labels text-center">3rd Floor</span>
            <span class="st-label">:&nbsp; "Grow your wealth"</span>
        </div>
        <div class="st-btn" data-network="twitter" style="display: inline-block;">
            <span class="st-labels text-center">2nd Floor</span>
            <span class="st-label">:&nbsp; "The floor of commerce"</span>
        </div>
        <div class="st-btn" data-network="email" style="display: inline-block;">
            <span class="st-labels text-center">1st Floor</span>
            <span class="st-label">:&nbsp; "International Remittance"</span>
        </div>
        <div class="st-btn" data-network="sharethis" style="display: inline-block;">
            <span class="st-labels text-center">Ground Floor</span>
            <span class="st-label">:&nbsp; "Everyday Essentials"</span>
        </div>
        <div class="st-btn st-last" data-network="linkedin" style="display: inline-block;">
            <span class="st-labels text-center">RoyalXPay Tower</span>
            <span class="st-label">:&nbsp; "Every Diaspora service"</span>
        </div>
        <!-- <div class="st-toggle">
            <div class="st-left">
                <img alt="arrow_left sharing button" src="https://platform-cdn.sharethis.com/img/arrow_left.svg">
            </div>
            <div class="st-right">
                <img alt="arrow_right sharing button" src="https://platform-cdn.sharethis.com/img/arrow_right.svg">
            </div>
        </div> -->
    </div>
     <!-- right side menu area start -->



     <!-- search box area start -->
      <section class="search-container py-4">
        <div class="container">
            <div class="d-flex align-items-center justify-content-center">
                <div class="search-box">
                    <div class="form-group" style="position: relative;">
                        <input type="search" class="form-control search-input" id="searchInput" placeholder="Ask anything...">
                        <a href="javascript:void();" class="d-flex">
                            <i class="fa fa-search search-icon"></i>
                            <i class="fas fa-microphone mic-icon"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
      </section>
      <!-- search box area end -->
    
    <!-- banner Area end --> 
    <section class="partner pb-5">
        <div class="container">
            <div class="row d-flex align-items-center justify-content-between">
                <div class="col-lg-6 col-sm-12 col-md-12" data-aos="fade-left" data-aos-easing="ease-in-out">
                    <h5 class="partner-title">Complete Basic Banking and Financial Services right in your hands</h5>
                    <h1 class="partner-large-head mt-3">Simplify Life With India's 1st Hybrid Neo Bank</h1>
                    <p class="partner-text mt-3 mb-4">Accounts (Savings/Current/Demat), Deposits (Bank/Non-Bank/Corporate), Credit Cards, Loans, Insurance, Investments – all in one Digital Platform</p>
                    <div class="d-flex align-items-center mt-4">
                        <a href="javascript:void(0);" class="btn btn-lg btn-warning rounded-pill px-5"><i class="fab fa-apple pe-1"></i>App Store</a>
                        <a href="javascript:void(0);" class="btn btn-lg btn-warning rounded-pill px-5 ms-4"><i class="fab fa-google-play pe-1"></i>Play Store</a>
                    </div>
                </div>
                <div class="col-1"></div>
                <div class="col-lg-5 col-sm-12 col-md-12 py-5" data-aos="fade-right" data-aos-easing="ease-in-out">
                    <img src="assets_frontend/images/banner-4.webp" style="height: auto; width: 100%;" class="banner-img">
                </div>
            </div>
        </div>
    </section>
    <!-- banner Area end -->

            <section class="serach-content pb-5">
                <div class="container pb-4">
                    <h2 class="welcome-heading-title-large text-center"  style="text-transform: capitalize;font-size: 42px;">Recharge and bill payments</h2>
                    <div class="d-flex justify-content-center align-items-center pt-2">
                        <p class="welcome-text text-center pt-3" style="width: 80%; font-size: 16px;">The Royal Pay search box is your gateway to quickly finding everything you need on the platform. Whether you're looking to recharge your mobile, pay utility bills, book movie or travel tickets, shop online, or explore financial services, the search bar makes your experience seamless and efficient. </p>
                    </div>
                    <div class="row pt-5">
                        <div class="col-lg-12 col-sm-12 col-md-12">
                            <div class="container-body">
                                <div class="container-box mb-0">
                                    <a href="javascript:void();" class="container-box-menu bg-light" data-aos="fade-up" data-aos-easing="ease-in-out"  rel="noreferrer">
                                        <img src="assets_frontend/images/1725624139422.avif" alt="Mobile Recharge">
                                        <span>Mobile Recharge</span>
                                    </a>
                                    <a href="javascript:void();" class="container-box-menu bg-light" data-aos="fade-up" data-aos-easing="ease-in-out"  rel="noreferrer" data-aos-delay="100">
                                        <img src="assets_frontend/images/14679690687944115.avif" alt="FASTag Recharge">
                                        <span>FASTag Recharge</span>
                                    </a>
                                    <a href="javascript:void();" class="container-box-menu bg-light" data-aos="fade-up" data-aos-easing="ease-in-out"  data-aos-delay="100" rel="noreferrer">
                                        <img src="assets_frontend/images/1725624172567.png" alt="DTH Recharge">
                                        <span>DTH Recharge</span>
                                    </a>
                                    <a href="<?php echo base_url('api/aadc-payment');?>" class="container-box-menu bg-light" data-aos="fade-up" data-aos-easing="ease-in-out"  data-aos-delay="200" rel="noreferrer">
                                            <img src="assets_frontend/images/1725624413059.avif" alt="Electricity Bill">
                                            <span>Electricity Bill</span>
                                        </a>
                                        <a href="javascript:void();" class="container-box-menu bg-light" data-aos="fade-up" data-aos-easing="ease-in-out"  data-aos-delay="200" rel="noreferrer">
                                            <img src="assets_frontend/images/21592552986104912.avif" alt="LIC / Insurance">
                                            <span>LIC / Insurance</span>
                                        </a>
                                        <a href="javascript:void();" class="container-box-menu bg-light" data-aos="fade-up" data-aos-easing="ease-in-out"  data-aos-delay="300" rel="noreferrer">
                                            <img src="assets_frontend/images/14504117837041035.png" alt="Pay Loan EMI">
                                            <span>Pay Loan EMI</span>
                                        </a>
                                    <!-- <a href="javascript:void();" class="container-box-menu bg-light" data-aos="fade-up" data-aos-easing="ease-in-out"  data-aos-delay="400" rel="noreferrer">
                                        <img src="assets_frontend/images/1725624245350.avif" alt="View All Products">
                                        <span>View All</span>
                                    </a> -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row collapse" id="demo">
                        <div class="col-lg-12 col-sm-12 col-md-12">
                            <div class="container-body">
                                <div class="container-box mb-0">
                                        <a href="javascript:void();" class="container-box-menu bg-light" data-aos="fade-up" data-aos-easing="ease-in-out"  rel="noreferrer">
                                            <img src="assets_frontend/images/21592552986104912.avif" alt="LIC / Insurance">
                                            <span>LIC / Insurance</span>
                                        </a>
                                        <a href="javascript:void();" class="container-box-menu bg-light" data-aos="fade-up" data-aos-easing="ease-in-out"  data-aos-delay="100" rel="noreferrer">
                                            <img src="assets_frontend/images/14504117837041035.png" alt="Pay Loan EMI">
                                            <span>Pay Loan EMI</span>
                                        </a>
                                        <a href="javascript:void();" class="container-box-menu bg-light" data-aos="fade-up" data-aos-easing="ease-in-out"  rel="noreferrer" data-aos-delay="100">
                                        <img src="assets_frontend/images/1725624139422.avif" alt="Mobile Recharge">
                                        <span>Mobile Recharge</span>
                                    </a>
                                    <a href="javascript:void();" class="container-box-menu bg-light" data-aos="fade-up" data-aos-easing="ease-in-out"  rel="noreferrer" data-aos-delay="200">
                                        <img src="assets_frontend/images/14679690687944115.avif" alt="FASTag Recharge">
                                        <span>FASTag Recharge</span>
                                    </a>
                                    <a href="javascript:void();" class="container-box-menu bg-light" data-aos="fade-up" data-aos-easing="ease-in-out"  data-aos-delay="200" rel="noreferrer">
                                        <img src="assets_frontend/images/1725624172567.png" alt="DTH Recharge">
                                        <span>DTH Recharge</span>
                                    </a>
                                    <a href="javascript:void();" class="container-box-menu bg-light" data-aos="fade-up" data-aos-easing="ease-in-out"  data-aos-delay="300" rel="noreferrer">
                                            <img src="assets_frontend/images/1725624413059.avif" alt="Electricity Bill">
                                            <span>Electricity Bill</span>
                                        </a>
                                    <!-- <a href="javascript:void();" class="container-box-menu bg-light" data-aos="fade-up" data-aos-easing="ease-in-out"  data-aos-delay="400" rel="noreferrer">
                                        <img src="assets_frontend/images/1725624245350.avif" alt="View All Products">
                                        <span>View All</span>
                                    </a> -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-center mt-5">
                        <a href="#demo" class="btn btn-md btn-warning rounded-pill px-4" data-bs-toggle="collapse">View All <i class="fas fa-long-arrow-alt-right ps-1"></i></a>
                    </div>
                </div>
            </section>


    <!-- about us area start -->    
     <section class="py-5">
            <div class="container how-its-work">
                <h5 class="welcome-heading-title mb-3 text-center">How It Works</h5>
                <h2 class="welcome-heading-title-large mb-4 text-center" style="text-transform: capitalize;">control over your finances on your fingertips</h2>
                <p class="welcome-text text-center">We will help you find your perfect match with just a few steps. You focus on what is most important to you, we do all the work.</p>
                <div class="row px-0 py-5 mt-4" style="position: relative;">
                    <!-- <img src="assets_frontend/images/placeholder.png" style="width: 235px; height: auto;" class="arrow" id="1">
                    <img src="assets_frontend/images/placeholder2.png" style="width: 235px; height: auto;" class="arrow1"> -->
                    <div class="col-lg-4 col-sm-12 col-md-6" data-aos="fade-up" data-aos-easing="ease-in-out" data-aos-delay="100">
                        <div class="elementor-icon-box-ico d-flex align-items-center justify-content-center">
                            <span class="elementor-icon elementor-animation-">
                            <i aria-hidden="true" class="fa fa-plus-square"></i></span>
                        </div>
                        <h6 class="elementor-icon-box-title text-center my-3 py-2" style="font-size: 22px; font-weight: 600;">Register</h6>
                        <p class="elementor-icon-box-description text-center px-4" style="font-size: 16px;">Register to our website, fill up your profile completely, and put a beautiful image on your profile.</p>
                    </div>
                    <div class="col-lg-4 col-sm-12 col-md-6" data-aos="fade-up" data-aos-easing="ease-in-out" data-aos-delay="200">
                        <div class="elementor-icon-box-ico d-flex align-items-center justify-content-center">
                            <span class="elementor-icon elementor-animation-">
                            <i aria-hidden="true" class="fa fa-search"></i></span>
                        </div>
                        <h6 class="elementor-icon-box-title text-center my-3 py-2" style="font-size: 22px; font-weight: 600;">Find Your Partner</h6>
                        <p class="elementor-icon-box-description text-center px-4" style="font-size: 16px;">Search your interests that you like the phase. You'll also be recommended users based on your preferences.</p>
                    </div>
                    <div class="col-lg-4 col-sm-12 col-md-6" data-aos="fade-up" data-aos-easing="ease-in-out" data-aos-delay="300">
                        <div class="elementor-icon-box-ico d-flex align-items-center justify-content-center">
                            <span class="elementor-icon elementor-animation-">
                            <i aria-hidden="true" class="fab fa-superpowers"></i></span>
                        </div>
                        <h6 class="elementor-icon-box-title text-center my-3 py-2" style="font-size: 22px; font-weight: 600;">Connects</h6>
                        <p class="elementor-icon-box-description text-center px-4" style="font-size: 16px;">Add friends, approach them, and chat with them. Be sure to share your audio, photo, and video too take.</p>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-center mb-5">
                    <a href="javascript:void(0);" class="btn btn-lg btn-warning rounded-pill px-5">Learn more <i class="fas fa-long-arrow-alt-right ps-1"></i></a>
                </div>
            </div>
        </section>
    <!-- about us area end -->
<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="<?= base_url('customer-auth/login') ?>" method="post" id="login_form" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Login</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <!-- ADDED name attributes below -->
        <input type="email" name="loginEmail" id="loginEmail" class="form-control mb-3" placeholder="Email" required>
        <input type="password" name="loginPassword" id="loginPassword" class="form-control" placeholder="Password" required>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-warning">Login</button>
      </div>
    </form>
  </div>
</div>


<!-- Register Modal -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="registerForm" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Register</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="text" class="form-control mb-3" id="regName" placeholder="Full Name" required>
        <input type="email" class="form-control mb-3" id="regEmail" placeholder="Email" required>
        <input type="password" class="form-control" id="regPassword" placeholder="Password" required>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-warning">Register</button>
      </div>
    </form>
  </div>
</div>






<!-- product area start -->
    <section class="product py-5">
        <h2 class="welcome-heading-title-large pb-5 text-center"  style="text-transform: capitalize;font-size: 42px;">Platform & Products</h2>
        <div class="container pt-4">
            <div class="row d-flex align-items-center justify-content-between">
                <div class="col-lg-3 col-sm-12 col-md-5">
                    <div class="intagrate-item active" data-aos="fade-right" data-aos-easing="ease-in-out">
						<div class="intagrate-item__icon">
							<svg width="40" height="38" viewBox="0 0 40 38" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path fill-rule="evenodd" clip-rule="evenodd" d="M0.657321 37.3508H39.3432V35.0992H0.657321V37.3508ZM39.6713 38H0.32866C0.147139 38 0 37.8547 0 37.6754V34.7742C0 34.5949 0.147139 34.4496 0.32866 34.4496H39.6713C39.8529 34.4496 40 34.5949 40 34.7742V37.6754C40 37.8547 39.8529 38 39.6713 38Z" fill="#F9D4A1"></path>
										<path fill-rule="evenodd" clip-rule="evenodd" d="M3.24082 34.4551H36.7556V32.203H3.24082V34.4551ZM37.0842 35.1047H2.91216C2.73063 35.1047 2.5835 34.9594 2.5835 34.7797V31.8784C2.5835 31.6986 2.73063 31.5533 2.91216 31.5533H37.0842C37.2658 31.5533 37.4129 31.6986 37.4129 31.8784V34.7797C37.4129 34.9594 37.2658 35.1047 37.0842 35.1047Z" fill="#F9D4A1"></path>
										<path fill-rule="evenodd" clip-rule="evenodd" d="M2.08917 10.875H37.9081L19.9996 0.699099L2.08917 10.875ZM39.1489 11.5246C39.1454 11.5241 39.1423 11.5241 39.1388 11.5246H0.858967C0.709806 11.5246 0.579353 11.4258 0.54143 11.2835C0.503002 11.1416 0.566206 10.9918 0.694637 10.9184L19.8353 0.043444C19.9375 -0.0144813 20.0623 -0.0144813 20.1635 0.043444L39.2597 10.894C39.3866 10.9394 39.4776 11.0587 39.4776 11.2001C39.4776 11.3793 39.3304 11.5246 39.1489 11.5246Z" fill="#F9D4A1"></path>
										<path fill-rule="evenodd" clip-rule="evenodd" d="M18.7686 31.5533H21.5987V13.9186H18.7686V31.5533ZM21.9273 32.203H18.44C18.2585 32.203 18.1113 32.0577 18.1113 31.8784V13.5935C18.1113 13.4142 18.2585 13.2689 18.44 13.2689H21.9273C22.1089 13.2689 22.256 13.4142 22.256 13.5935V31.8784C22.256 32.0577 22.1089 32.203 21.9273 32.203Z" fill="#F9D4A1"></path>
										<path fill-rule="evenodd" clip-rule="evenodd" d="M5.88437 31.5533H8.71641V13.9186H5.88437V31.5533ZM9.04507 32.203H5.55571C5.37419 32.203 5.22705 32.0577 5.22705 31.8784V13.5935C5.22705 13.4142 5.37419 13.2689 5.55571 13.2689H9.04507C9.2266 13.2689 9.37373 13.4142 9.37373 13.5935V31.8784C9.37373 32.0577 9.2266 32.203 9.04507 32.203Z" fill="#F9D4A1"></path>
										<path fill-rule="evenodd" clip-rule="evenodd" d="M31.6495 31.5533H34.48V13.9186H31.6495V31.5533ZM34.8087 32.203H31.3208C31.1393 32.203 30.9922 32.0577 30.9922 31.8784V13.5935C30.9922 13.4142 31.1393 13.2689 31.3208 13.2689H34.8087C34.9902 13.2689 35.1374 13.4142 35.1374 13.5935V31.8784C35.1374 32.0577 34.9902 32.203 34.8087 32.203Z" fill="#F9D4A1"></path>
										<path fill-rule="evenodd" clip-rule="evenodd" d="M4.65486 13.2689H9.94377V11.5246H4.65486V13.2689ZM10.2724 13.9186H4.32671C4.14519 13.9186 3.99805 13.7732 3.99805 13.5935V11.2001C3.99805 11.0203 4.14519 10.875 4.32671 10.875H10.2724C10.4539 10.875 10.6011 11.0203 10.6011 11.2001V13.5935C10.6011 13.7732 10.4539 13.9186 10.2724 13.9186Z" fill="#F9D4A1"></path>
										<path fill-rule="evenodd" clip-rule="evenodd" d="M17.5362 13.2689H22.8277V11.5246H17.5362V13.2689ZM23.1563 13.9186H17.2076C17.026 13.9186 16.8789 13.7732 16.8789 13.5935V11.2001C16.8789 11.0203 17.026 10.875 17.2076 10.875H23.1563C23.3378 10.875 23.485 11.0203 23.485 11.2001V13.5935C23.485 13.7732 23.3378 13.9186 23.1563 13.9186Z" fill="#F9D4A1"></path>
										<path fill-rule="evenodd" clip-rule="evenodd" d="M30.4205 13.2689H35.7094V11.5246H30.4205V13.2689ZM36.0381 13.9186H30.0918C29.9103 13.9186 29.7632 13.7732 29.7632 13.5935V11.2001C29.7632 11.0203 29.9103 10.875 30.0918 10.875H36.0381C36.2196 10.875 36.3667 11.0203 36.3667 11.2001V13.5935C36.3667 13.7732 36.2196 13.9186 36.0381 13.9186Z" fill="#F9D4A1"></path>
										<path fill-rule="evenodd" clip-rule="evenodd" d="M19.9997 5.43749C18.9864 5.43749 18.1622 6.25094 18.1622 7.25115C18.1622 8.25386 18.9864 9.06981 19.9997 9.06981C21.0119 9.06981 21.8356 8.25386 21.8356 7.25115C21.8356 6.25094 21.0119 5.43749 19.9997 5.43749ZM19.9997 9.71947C18.6238 9.71947 17.5049 8.6119 17.5049 7.25115C17.5049 5.8934 18.6238 4.78833 19.9997 4.78833C21.374 4.78833 22.4924 5.8934 22.4924 7.25115C22.4924 8.6119 21.374 9.71947 19.9997 9.71947Z" fill="#F9D4A1"></path>
							</svg>
						</div>
						<p class="intagrate-item__title">Banking as-a Service</p>
					</div>
                    <div class="intagrate-item" data-aos="fade-right" data-aos-easing="ease-in-out" data-aos-delay="100">
						<div class="intagrate-item__icon">
									<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path fill-rule="evenodd" clip-rule="evenodd" d="M29.1432 40H5.32899C5.15184 40 5 39.8484 5 39.6715V0.32849C5 0.151611 5.15184 0 5.32899 0H29.1432C29.3203 0 29.4722 0.151611 29.4722 0.32849V13.8471C29.4722 14.024 29.3203 14.1756 29.1432 14.1756H21.9559C21.6016 14.1756 21.3232 14.4536 21.3232 14.8073V25.1927C21.3232 25.5464 21.6016 25.8244 21.9559 25.8244H29.1432C29.3203 25.8244 29.4722 25.976 29.4722 26.1529V39.6715C29.4722 39.8484 29.3203 40 29.1432 40ZM5.65799 39.343H28.8142V26.4814H21.9559C21.2473 26.4814 20.6652 25.9002 20.6652 25.1927V14.8073C20.6652 14.0998 21.2473 13.5186 21.9559 13.5186H28.8142V0.65698H5.65799V39.343Z" fill="#F9D4A0"></path>
										<path d="M29.1432 6.73595H5.32899C5.15184 6.73595 5 6.50518 5 6.23595C5 5.96672 5.15184 5.73595 5.32899 5.73595H29.1432C29.3203 5.73595 29.4722 5.96672 29.4722 6.23595C29.4722 6.50518 29.3203 6.73595 29.1432 6.73595Z" fill="#F9D4A0"></path>
										<path d="M29.1432 34.6071H5.32899C5.15184 34.6071 5 34.4148 5 34.1071C5 33.8378 5.15184 33.6071 5.32899 33.6071H29.1432C29.3203 33.6071 29.4722 33.8378 29.4722 34.1071C29.4722 34.4148 29.3203 34.6071 29.1432 34.6071Z" fill="#F9D4A0"></path>
										<path fill-rule="evenodd" clip-rule="evenodd" d="M17.2236 38.7618C16.1607 38.7618 15.2749 37.8774 15.2749 36.8162C15.2749 35.7296 16.1607 34.8705 17.2236 34.8705C18.3118 34.8705 19.1722 35.7296 19.1722 36.8162C19.1722 37.8774 18.3118 38.7618 17.2236 38.7618ZM17.2236 35.5275C16.515 35.5275 15.9329 36.1087 15.9329 36.8162C15.9329 37.5237 16.515 38.1049 17.2236 38.1049C17.9575 38.1049 18.5142 37.5237 18.5142 36.8162C18.5142 36.1087 17.9575 35.5275 17.2236 35.5275Z" fill="#F9D4A0"></path>
										<path d="M19.2227 3.85534H15.2494C15.0469 3.85534 14.9204 3.66303 14.9204 3.35534C14.9204 3.08611 15.0469 2.85534 15.2494 2.85534H19.2227C19.3998 2.85534 19.5516 3.08611 19.5516 3.35534C19.5516 3.66303 19.3998 3.85534 19.2227 3.85534Z" fill="#F9D4A0"></path>
										<path fill-rule="evenodd" clip-rule="evenodd" d="M38.7091 26.4814H21.9557C21.2471 26.4814 20.665 25.9002 20.665 25.1927V14.8073C20.665 14.0998 21.2471 13.5186 21.9557 13.5186H38.7091C39.4178 13.5186 39.9998 14.0998 39.9998 14.8073V25.1927C39.9998 25.9002 39.4178 26.4814 38.7091 26.4814ZM21.9557 14.1756C21.6014 14.1756 21.323 14.4536 21.323 14.8073V25.1927C21.323 25.5464 21.6014 25.8244 21.9557 25.8244H38.7091C39.0634 25.8244 39.3418 25.5464 39.3418 25.1927V14.8073C39.3418 14.4536 39.0634 14.1756 38.7091 14.1756H21.9557Z" fill="#F9D4A0"></path>
										<path d="M39.6708 18.3594H20.994C20.8169 18.3594 20.665 18.1287 20.665 17.8594C20.665 17.5902 20.8169 17.3594 20.994 17.3594H39.6708C39.848 17.3594 39.9998 17.5902 39.9998 17.8594C39.9998 18.1287 39.848 18.3594 39.6708 18.3594Z" fill="#F9D4A0"></path>
										<path d="M39.6708 21.139H20.994C20.8169 21.139 20.665 20.9082 20.665 20.639C20.665 20.3313 20.8169 20.139 20.994 20.139H39.6708C39.848 20.139 39.9998 20.3313 39.9998 20.639C39.9998 20.9082 39.848 21.139 39.6708 21.139Z" fill="#F9D4A0"></path>
										<path fill-rule="evenodd" clip-rule="evenodd" d="M35.7482 24.6873C35.5626 24.6873 35.377 24.6536 35.1914 24.5862C35.0227 24.6536 34.8456 24.6873 34.66 24.6873C33.8754 24.6873 33.2681 24.0809 33.2681 23.2975C33.2681 22.5395 33.8754 21.933 34.66 21.933C34.8456 21.933 35.0227 21.9667 35.1914 22.0341C35.377 21.9667 35.5626 21.933 35.7482 21.933C36.5074 21.933 37.1401 22.5395 37.1401 23.2975C37.1401 24.0809 36.5074 24.6873 35.7482 24.6873ZM35.1914 23.8787C35.2589 23.8787 35.318 23.8956 35.3686 23.9293C35.4867 23.9966 35.6132 24.0303 35.7482 24.0303C36.1531 24.0303 36.4821 23.7018 36.4821 23.2975C36.4821 22.9185 36.1531 22.59 35.7482 22.59C35.6132 22.59 35.4867 22.6237 35.3686 22.6911C35.2673 22.7416 35.1408 22.7416 35.0143 22.6911C34.8962 22.6237 34.7781 22.59 34.66 22.59C34.2551 22.59 33.9261 22.9185 33.9261 23.2975C33.9261 23.7018 34.2551 24.0303 34.66 24.0303C34.7781 24.0303 34.8962 23.9966 35.0143 23.9293C35.0818 23.8956 35.1408 23.8787 35.1914 23.8787Z" fill="#F9D4A0"></path>
										<path d="M25.0686 23.969H23.8539C23.6767 23.969 23.5249 23.7767 23.5249 23.469C23.5249 23.1998 23.6767 22.969 23.8539 22.969H25.0686C25.2711 22.969 25.3976 23.1998 25.3976 23.469C25.3976 23.7767 25.2711 23.969 25.0686 23.969Z" fill="#F9D4A0"></path>
										<path d="M27.8777 23.969H26.663C26.4858 23.969 26.334 23.7767 26.334 23.469C26.334 23.1998 26.4858 22.969 26.663 22.969H27.8777C28.0802 22.969 28.2067 23.1998 28.2067 23.469C28.2067 23.7767 28.0802 23.969 27.8777 23.969Z" fill="#F9D4A0"></path>
										<path d="M30.6868 23.969H29.4721C29.2949 23.969 29.1431 23.7767 29.1431 23.469C29.1431 23.1998 29.2949 22.969 29.4721 22.969H30.6868C30.8893 22.969 31.0158 23.1998 31.0158 23.469C31.0158 23.7767 30.8893 23.969 30.6868 23.969Z" fill="#F9D4A0"></path>
									</svg>
						</div>
						<p class="intagrate-item__title">Cards as-a Service</p>
					</div>
                    <div class="intagrate-item" data-aos="fade-right" data-aos-easing="ease-in-out" data-aos-delay="200">
								<div class="intagrate-item__icon">
									<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path fill-rule="evenodd" clip-rule="evenodd" d="M20.3486 23.9307C16.129 23.9307 12.6965 27.3877 12.6965 31.6365C12.6965 35.8857 16.129 39.3427 20.3486 39.3427C24.5672 39.3427 27.9993 35.8857 27.9993 31.6365C27.9993 27.3877 24.5672 23.9307 20.3486 23.9307ZM20.3486 40C15.7691 40 12.0435 36.2478 12.0435 31.6365C12.0435 27.0252 15.7691 23.2729 20.3486 23.2729C24.9272 23.2729 28.6523 27.0252 28.6523 31.6365C28.6523 36.2478 24.9272 40 20.3486 40Z" fill="#F9D4A0"></path>
										<path fill-rule="evenodd" clip-rule="evenodd" d="M20.3795 37.302C19.7314 37.3025 19.0914 37.1518 18.5734 36.8838C18.1768 36.6755 17.8365 36.3459 17.5599 35.9024C17.2803 35.4565 17.1222 34.9316 17.077 34.2981C17.0644 34.1176 17.1995 33.9598 17.3797 33.9472C17.5569 33.9325 17.7155 34.0705 17.728 34.2516C17.7652 34.7739 17.8907 35.1991 18.1121 35.552C18.3269 35.8958 18.5824 36.1476 18.8725 36.3003C19.8685 36.8146 21.4768 36.808 22.3352 35.9024C22.7603 35.4499 22.9672 34.8977 22.9672 34.2151C22.9672 33.9158 22.916 33.6306 22.8146 33.3667C22.7127 33.1038 22.5751 32.8894 22.3954 32.7119C22.2042 32.5183 21.9602 32.357 21.6655 32.2285C21.2384 32.0415 20.8217 31.9297 20.381 31.8124C19.7756 31.6511 19.1496 31.4838 18.479 31.1081C18.0769 30.8826 17.7632 30.5655 17.5473 30.1656C17.333 29.7717 17.2241 29.3197 17.2241 28.8232C17.2241 27.9474 17.5393 27.2239 18.1607 26.6702C18.6597 26.2303 19.4353 25.976 20.2891 25.9709C21.1279 25.9709 21.8894 26.2131 22.3814 26.6348C22.9185 27.0874 23.2463 27.7128 23.3542 28.4915C23.3793 28.671 23.2548 28.8368 23.0761 28.8621C22.9019 28.8924 22.7327 28.7625 22.7081 28.582C22.6218 27.9616 22.3773 27.4888 21.9602 27.1374C21.5827 26.8133 20.9748 26.6288 20.2911 26.6288C19.6039 26.6323 18.9674 26.8325 18.5914 27.1642C18.111 27.5915 17.8771 28.135 17.8771 28.8232C17.8771 29.2135 17.9564 29.5497 18.1196 29.8511C18.2787 30.1439 18.4996 30.3673 18.7967 30.5337C19.3951 30.8694 19.9553 31.0191 20.5481 31.1768C20.9934 31.2957 21.4532 31.419 21.9256 31.6258C22.296 31.7871 22.6088 31.9955 22.8547 32.2452C23.0957 32.4824 23.2879 32.7807 23.423 33.1286C23.554 33.4694 23.6197 33.8344 23.6197 34.2151C23.6197 35.0621 23.3467 35.7821 22.8075 36.355C22.1811 37.0163 21.273 37.302 20.3795 37.302Z" fill="#F9D4A0"></path>
										<path fill-rule="evenodd" clip-rule="evenodd" d="M20.3444 38.4083C20.1641 38.4083 20.0181 38.2611 20.0181 38.0791V25.2373C20.0181 25.0557 20.1641 24.9086 20.3444 24.9086C20.5246 24.9086 20.6706 25.0557 20.6706 25.2373V38.0791C20.6706 38.2611 20.5246 38.4083 20.3444 38.4083Z" fill="#F9D4A0"></path>
										<path fill-rule="evenodd" clip-rule="evenodd" d="M20.3486 0.657818C16.1295 0.657818 12.6975 4.11478 12.6975 8.36354C12.6975 12.6123 16.1295 16.0693 20.3486 16.0693C24.5672 16.0693 27.9992 12.6123 27.9992 8.36354C27.9992 4.11478 24.5672 0.657818 20.3486 0.657818ZM20.3486 16.7271C15.7696 16.7271 12.0444 12.9748 12.0444 8.36354C12.0444 3.75224 15.7696 0 20.3486 0C24.9271 0 28.6523 3.75224 28.6523 8.36354C28.6523 12.9748 24.9271 16.7271 20.3486 16.7271Z" fill="#F9D4A0"></path>
										<path fill-rule="evenodd" clip-rule="evenodd" d="M23.6328 7.86804H15.6925C15.5118 7.86804 15.3657 7.7209 15.3657 7.53938C15.3657 7.35786 15.5118 7.21072 15.6925 7.21072H23.6328C23.813 7.21072 23.959 7.35786 23.959 7.53938C23.959 7.7209 23.813 7.86804 23.6328 7.86804Z" fill="#F9D4A0"></path>
										<path fill-rule="evenodd" clip-rule="evenodd" d="M23.6328 9.64277H15.6925C15.5118 9.64277 15.3657 9.49564 15.3657 9.31412C15.3657 9.13209 15.5118 8.98495 15.6925 8.98495H23.6328C23.813 8.98495 23.959 9.13209 23.959 9.31412C23.959 9.49564 23.813 9.64277 23.6328 9.64277Z" fill="#F9D4A0"></path>
										<path fill-rule="evenodd" clip-rule="evenodd" d="M22.6236 13.2772H21.1874C18.5345 13.2772 16.3755 11.1015 16.3755 8.42726C16.3755 5.75301 18.5345 3.5773 21.1874 3.5773H22.6236C22.8043 3.5773 22.9504 3.72444 22.9504 3.90646C22.9504 4.08798 22.8043 4.23512 22.6236 4.23512H21.1874C18.8939 4.23512 17.0286 6.11554 17.0286 8.42726C17.0286 10.739 18.8939 12.6194 21.1874 12.6194H22.6236C22.8043 12.6194 22.9504 12.7665 22.9504 12.9481C22.9504 13.1301 22.8043 13.2772 22.6236 13.2772Z" fill="#F9D4A0"></path>
										<path fill-rule="evenodd" clip-rule="evenodd" d="M11.3861 7.99242C11.2837 7.99242 11.1833 7.94388 11.1196 7.85337C11.0152 7.70523 11.0498 7.50045 11.1969 7.39528C11.5689 7.13033 11.9488 6.88207 12.3283 6.65909C12.4845 6.56808 12.6837 6.62015 12.7751 6.7769C12.866 6.93415 12.8132 7.13488 12.6581 7.2269C12.2947 7.44028 11.9303 7.67742 11.5744 7.93124C11.5171 7.9727 11.4514 7.99242 11.3861 7.99242ZM9.20254 9.92189C9.12323 9.92189 9.04341 9.89256 8.98016 9.83391C8.84865 9.71054 8.84061 9.50272 8.9631 9.36924C9.31046 8.99255 9.68745 8.62648 10.1161 8.2508C10.2517 8.13147 10.459 8.14563 10.5764 8.28316C10.6944 8.42018 10.6804 8.62749 10.5448 8.74681C10.1332 9.10682 9.77328 9.45671 9.44148 9.81672C9.37723 9.88649 9.29038 9.92189 9.20254 9.92189ZM7.11181 12.8191C7.0581 12.8191 7.00338 12.806 6.95319 12.7772C6.79607 12.6892 6.73985 12.4889 6.82719 12.3307C7.12938 11.7856 7.45366 11.2704 7.79199 10.8007C7.8969 10.654 8.1012 10.6212 8.24778 10.7279C8.39335 10.8345 8.42648 11.0398 8.32057 11.187C7.9973 11.636 7.68657 12.1279 7.39693 12.6502C7.3372 12.7584 7.22626 12.8191 7.11181 12.8191ZM5.74995 16.3216C5.72335 16.3216 5.69674 16.318 5.67064 16.312C5.49595 16.268 5.38903 16.0895 5.4327 15.9135C5.59434 15.2583 5.80216 14.609 6.05063 13.9841C6.1179 13.8147 6.30865 13.7353 6.4748 13.801C6.64246 13.8688 6.72378 14.0599 6.65702 14.2283C6.41959 14.8254 6.2203 15.4458 6.0662 16.0723C6.02955 16.2215 5.89603 16.3216 5.74995 16.3216ZM5.32628 19.824C5.14607 19.824 5 19.6769 5 19.4954C5 18.966 5.02861 18.4331 5.08483 17.9113C5.10441 17.7303 5.26203 17.6059 5.44425 17.62C5.62345 17.6397 5.75296 17.8015 5.73389 17.9821C5.67967 18.4806 5.65307 18.9898 5.65307 19.4954C5.65307 19.6769 5.507 19.824 5.32628 19.824ZM5.57024 22.5018C5.41614 22.5018 5.2786 22.3906 5.24998 22.2318C5.15812 21.7252 5.09387 21.2398 5.05371 20.7473C5.03865 20.5668 5.17218 20.4075 5.35188 20.3929C5.52657 20.3737 5.68871 20.5112 5.70377 20.6932C5.74242 21.1634 5.80416 21.6286 5.89201 22.114C5.92464 22.2925 5.80667 22.4639 5.62948 22.4963C5.6099 22.4998 5.59032 22.5018 5.57024 22.5018ZM6.6766 25.9598C6.55361 25.9598 6.43665 25.8895 6.38093 25.7707C6.10334 25.1751 5.8644 24.5542 5.67114 23.9257C5.61743 23.7528 5.71431 23.5682 5.88599 23.5146C6.05716 23.4605 6.24088 23.5576 6.29459 23.7315C6.47932 24.3307 6.70672 24.9233 6.97176 25.4911C7.04856 25.6554 6.97829 25.8511 6.81514 25.9285C6.76997 25.9492 6.72278 25.9598 6.6766 25.9598ZM8.62978 29.0724C8.53642 29.0724 8.44255 29.0315 8.37829 28.9531C7.9993 28.491 7.65294 28.0126 7.34724 27.5318C7.24985 27.3786 7.29453 27.1753 7.44613 27.0777C7.59823 26.9791 7.80002 27.0252 7.8969 27.1774C8.18805 27.636 8.51935 28.093 8.88127 28.5345C8.99623 28.6745 8.97665 28.8813 8.8376 28.9971C8.77686 29.0477 8.70358 29.0724 8.62978 29.0724ZM12.3765 32.3074C12.3198 32.3074 12.2621 32.2923 12.2094 32.2609C11.4042 31.7755 10.6457 31.2153 9.9545 30.5964C9.81947 30.4755 9.80742 30.2677 9.92739 30.1327C10.0474 29.9967 10.2542 29.9856 10.3877 30.1054C11.0488 30.697 11.7742 31.232 12.5447 31.6966C12.6993 31.7902 12.7495 31.9914 12.6571 32.1476C12.5959 32.2503 12.488 32.3074 12.3765 32.3074Z" fill="#F9D4A0"></path>
										<path fill-rule="evenodd" clip-rule="evenodd" d="M29.6148 7.99241C29.5501 7.99241 29.4843 7.97269 29.4271 7.93174C29.0631 7.67286 28.6982 7.43572 28.3423 7.2269C28.1867 7.13538 28.134 6.93465 28.2243 6.7774C28.3142 6.62166 28.513 6.56554 28.6706 6.65908C29.0431 6.87751 29.4241 7.12527 29.8035 7.39476C29.9506 7.49993 29.9858 7.70471 29.8819 7.85286C29.8181 7.94387 29.7172 7.99241 29.6148 7.99241ZM31.7979 9.92188C31.7101 9.92188 31.6227 9.88648 31.5585 9.81671C31.2251 9.45468 30.8642 9.10479 30.4556 8.74681C30.3196 8.62748 30.3055 8.42017 30.4235 8.28315C30.542 8.14612 30.7483 8.13146 30.8838 8.25079C31.31 8.62444 31.6875 8.99001 32.0368 9.36923C32.1593 9.50272 32.1518 9.71053 32.0198 9.8339C31.957 9.89255 31.8772 9.92188 31.7979 9.92188ZM33.8871 12.8191C33.7717 12.8191 33.6602 12.7579 33.601 12.6497C33.3204 12.1385 33.0102 11.6461 32.6789 11.187C32.5729 11.0398 32.6061 10.8345 32.7516 10.7278C32.8977 10.6212 33.102 10.654 33.2074 10.8007C33.5538 11.2815 33.8781 11.7967 34.1717 12.3317C34.2591 12.4899 34.2024 12.6902 34.0442 12.7776C33.994 12.806 33.9403 12.8191 33.8871 12.8191ZM35.2495 16.3216C35.1034 16.3216 34.9704 16.2215 34.9337 16.0723C34.7766 15.4372 34.5783 14.8173 34.3434 14.2288C34.2761 14.0604 34.3575 13.8693 34.5246 13.801C34.6908 13.7353 34.8815 13.8152 34.9493 13.9836C35.1948 14.5999 35.4026 15.2491 35.5667 15.9135C35.6104 16.0895 35.5035 16.268 35.3293 16.312C35.3027 16.318 35.2761 16.3216 35.2495 16.3216ZM35.6731 19.824C35.4929 19.824 35.3469 19.6769 35.3469 19.4954C35.3469 19.0034 35.3198 18.4942 35.266 17.981C35.247 17.8005 35.377 17.6392 35.5562 17.62C35.7319 17.6028 35.896 17.7308 35.9151 17.9123C35.9713 18.4477 35.9999 18.9807 35.9999 19.4954C35.9999 19.6769 35.8539 19.824 35.6731 19.824ZM35.4277 22.5018C35.4076 22.5018 35.3875 22.4998 35.3674 22.4958C35.1902 22.4629 35.0733 22.2915 35.1064 22.1125C35.1973 21.622 35.2585 21.1584 35.2942 20.6947C35.3087 20.5127 35.4633 20.3823 35.6455 20.3924C35.8247 20.4065 35.9593 20.5648 35.9452 20.7458C35.9076 21.2327 35.8428 21.7191 35.7479 22.2333C35.7188 22.3911 35.5818 22.5018 35.4277 22.5018ZM34.3263 25.9598C34.2797 25.9598 34.233 25.9497 34.1878 25.9285C34.0247 25.8521 33.9539 25.6564 34.0307 25.4916C34.2907 24.9298 34.5186 24.3372 34.7068 23.7305C34.7611 23.5576 34.9423 23.461 35.116 23.5151C35.2876 23.5687 35.3835 23.7533 35.3298 23.9267C35.133 24.5607 34.8946 25.1811 34.6215 25.7702C34.5663 25.8895 34.4488 25.9598 34.3263 25.9598ZM32.3681 29.0724C32.2949 29.0724 32.2206 29.0471 32.1593 28.9966C32.0213 28.8798 32.0022 28.673 32.1172 28.5334C32.4726 28.1031 32.8044 27.6471 33.1035 27.1773C33.1999 27.0246 33.4022 26.9781 33.5543 27.0782C33.7059 27.1758 33.7501 27.3791 33.6527 27.5318C33.3395 28.0248 32.9911 28.5031 32.6191 28.9541C32.5544 29.032 32.4615 29.0724 32.3681 29.0724ZM28.6214 32.3074C28.5105 32.3074 28.402 32.2503 28.3413 32.1471C28.2484 31.9914 28.2991 31.7897 28.4537 31.6966C29.2177 31.237 29.9436 30.7016 30.6107 30.1049C30.7458 29.9851 30.9516 29.9967 31.0715 30.1327C31.191 30.2682 31.179 30.476 31.0444 30.5969C30.3462 31.2203 29.5872 31.7801 28.7881 32.2609C28.7358 32.2922 28.6786 32.3074 28.6214 32.3074Z" fill="#F9D4A0"></path>
									</svg>
								</div>
								<p class="intagrate-item__title">Remittance Platform</p>
					</div>
                    <div class="intagrate-item" data-aos="fade-right" data-aos-easing="ease-in-out" data-aos-delay="300">
								<div class="intagrate-item__icon">
									<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M19.0002 28.0262C14.5787 28.0262 10.981 24.4257 10.981 19.9998C10.981 15.5739 14.5787 11.9733 19.0002 11.9733C20.5312 11.9733 22.02 12.4065 23.3052 13.2268C23.502 13.3523 23.5594 13.6134 23.4348 13.81C23.3094 14.0065 23.0481 14.0652 22.8522 13.9393C21.702 13.2056 20.3703 12.8181 19.0006 12.8181C15.0444 12.8181 11.8258 16.0396 11.8258 19.9994C11.8258 23.9591 15.044 27.181 19.0002 27.181C22.9564 27.181 26.175 23.9596 26.175 19.9998C26.175 18.3259 25.5873 16.6967 24.5208 15.412C24.3717 15.2328 24.3958 14.9662 24.5752 14.817C24.7555 14.6674 25.0211 14.6928 25.1697 14.8719C26.3625 16.3079 27.0195 18.1289 27.0195 19.9994C27.0195 24.4253 23.4217 28.0262 19.0002 28.0262Z" fill="#F9D4A0"></path>
										<path fill-rule="evenodd" clip-rule="evenodd" d="M8.44873 19.9998C8.44873 25.8232 13.1818 30.5606 19.0001 30.5606C24.8183 30.5606 29.5514 25.8232 29.5514 19.9998C29.5514 14.1763 24.8183 9.439 19.0001 9.439C13.1818 9.439 8.44873 14.1763 8.44873 19.9998ZM9.29275 19.9998C9.29275 14.6425 13.6476 10.2838 19.0001 10.2838C24.353 10.2838 28.7074 14.6425 28.7074 19.9998C28.7074 25.3575 24.353 29.7158 19.0001 29.7158C13.6476 29.7158 9.29275 25.3575 9.29275 19.9998Z" fill="#F9D4A0"></path>
										<path d="M19.3748 23.7812C17.2915 23.7812 15.5972 22.0849 15.5972 19.9998C15.5972 17.9147 17.292 16.2188 19.3748 16.2188C20.3142 16.2188 21.2148 16.5662 21.9107 17.1971C22.0838 17.3539 22.0969 17.621 21.9402 17.7938C21.7836 17.9671 21.5163 17.9793 21.344 17.8234C20.8036 17.3336 20.1044 17.0636 19.3744 17.0636C17.7568 17.0636 16.4408 18.3808 16.4408 19.9998C16.4408 21.6192 17.7568 22.936 19.3744 22.936C20.1035 22.936 20.8032 22.6664 21.344 22.1762C21.5167 22.0198 21.784 22.0325 21.9402 22.2058C22.0969 22.3786 22.0838 22.6457 21.9107 22.8025C21.2148 23.4338 20.3142 23.7812 19.3748 23.7812Z" fill="#F9D4A0"></path>
										<path d="M19.3749 17.0636C19.1418 17.0636 18.9526 16.8742 18.9526 16.641V15.3529C18.9526 15.1196 19.1418 14.9303 19.3749 14.9303C19.6079 14.9303 19.7971 15.1196 19.7971 15.3529V16.641C19.7971 16.8742 19.6079 17.0636 19.3749 17.0636Z" fill="#F9D4A0"></path>
										<path d="M19.3749 25.0689C19.1418 25.0689 18.9526 24.88 18.9526 24.6463V23.3582C18.9526 23.1245 19.1418 22.9356 19.3749 22.9356C19.6079 22.9356 19.7971 23.1245 19.7971 23.3582V24.6463C19.7971 24.88 19.6079 25.0689 19.3749 25.0689Z" fill="#F9D4A0"></path>
										<path d="M13.9678 11.6116C13.8065 11.6116 13.6524 11.5182 13.5823 11.3614L11.7997 7.37291H8.46373C8.23066 7.37291 8.0415 7.18359 8.0415 6.95031C8.0415 6.71704 8.23066 6.52771 8.46373 6.52771H12.0733C12.2397 6.52771 12.3908 6.62575 12.4584 6.77789L14.3529 11.0162C14.4479 11.2291 14.3529 11.4789 14.1401 11.574C14.0839 11.5998 14.0252 11.6116 13.9678 11.6116Z" fill="#F9D4A0"></path>
										<path d="M24.0322 11.6116C23.9748 11.6116 23.9165 11.5998 23.8603 11.5744C23.6475 11.4793 23.5521 11.2291 23.6471 11.0166L25.5416 6.77832C25.6096 6.62618 25.7603 6.52814 25.9271 6.52814H29.5367C29.7702 6.52814 29.9589 6.71746 29.9589 6.95074C29.9589 7.18401 29.7702 7.37334 29.5367 7.37334H26.2007L24.4181 11.3618C24.348 11.5182 24.1939 11.6116 24.0322 11.6116Z" fill="#F9D4A0"></path>
										<path d="M12.0728 33.4714H8.46324C8.23017 33.4714 8.04102 33.2825 8.04102 33.0488C8.04102 32.8151 8.23017 32.6262 8.46324 32.6262H11.7992L13.5818 28.6377C13.6768 28.4243 13.9276 28.3309 14.1392 28.4243C14.352 28.5198 14.4474 28.7696 14.352 28.9826L12.4575 33.2208C12.3903 33.3738 12.2396 33.4714 12.0728 33.4714Z" fill="#F9D4A0"></path>
										<path d="M29.5367 33.4714H25.9271C25.7608 33.4714 25.6096 33.3738 25.5416 33.2213L23.6471 28.983C23.5521 28.77 23.6475 28.5203 23.8603 28.4247C24.0744 28.3297 24.3231 28.4252 24.4181 28.6382L26.2007 32.6267H29.5367C29.7702 32.6267 29.9589 32.8156 29.9589 33.0493C29.9589 33.283 29.7702 33.4714 29.5367 33.4714Z" fill="#F9D4A0"></path>
										<path fill-rule="evenodd" clip-rule="evenodd" d="M4.98779 6.95072C4.98779 8.02624 5.86222 8.90145 6.93677 8.90145C8.01133 8.90145 8.88575 8.02624 8.88575 6.95072C8.88575 5.87521 8.01133 5 6.93677 5C5.86222 5 4.98779 5.87521 4.98779 6.95072ZM5.83182 6.9503C5.83182 6.34049 6.3275 5.84436 6.93677 5.84436C7.54604 5.84436 8.04173 6.34049 8.04173 6.9503C8.04173 7.56011 7.54604 8.05625 6.93677 8.05625C6.3275 8.05625 5.83182 7.56011 5.83182 6.9503Z" fill="#F9D4A0"></path>
										<path fill-rule="evenodd" clip-rule="evenodd" d="M4.98779 33.0493C4.98779 34.1252 5.86222 35 6.93677 35C8.01133 35 8.88575 34.1252 8.88575 33.0493C8.88575 31.9733 8.01133 31.0985 6.93677 31.0985C5.86222 31.0985 4.98779 31.9733 4.98779 33.0493ZM5.83182 33.0488C5.83182 32.439 6.3275 31.9429 6.93677 31.9429C7.54604 31.9429 8.04173 32.439 8.04173 33.0488C8.04173 33.6587 7.54604 34.1548 6.93677 34.1548C6.3275 34.1548 5.83182 33.6587 5.83182 33.0488Z" fill="#F9D4A0"></path>
										<path fill-rule="evenodd" clip-rule="evenodd" d="M29.1147 33.0493C29.1147 34.1252 29.9887 35 31.0633 35C32.1383 35 33.0123 34.1252 33.0123 33.0493C33.0123 31.9733 32.1383 31.0985 31.0633 31.0985C29.9887 31.0985 29.1147 31.9733 29.1147 33.0493ZM29.9588 33.0488C29.9588 32.439 30.454 31.9429 31.0633 31.9429C31.6726 31.9429 32.1683 32.439 32.1683 33.0488C32.1683 33.6587 31.6726 34.1548 31.0633 34.1548C30.454 34.1548 29.9588 33.6587 29.9588 33.0488Z" fill="#F9D4A0"></path>
										<path fill-rule="evenodd" clip-rule="evenodd" d="M29.1147 6.95072C29.1147 8.02624 29.9887 8.90145 31.0633 8.90145C32.1383 8.90145 33.0123 8.02624 33.0123 6.95072C33.0123 5.87521 32.1383 5 31.0633 5C29.9887 5 29.1147 5.87521 29.1147 6.95072ZM29.9588 6.9503C29.9588 6.34049 30.454 5.84436 31.0633 5.84436C31.6726 5.84436 32.1683 6.34049 32.1683 6.9503C32.1683 7.56011 31.6726 8.05625 31.0633 8.05625C30.454 8.05625 29.9588 7.56011 29.9588 6.9503Z" fill="#F9D4A0"></path>
										<path d="M8.87024 20.4224H3.47593C3.24287 20.4224 3.05371 20.2331 3.05371 19.9998C3.05371 19.7665 3.24287 19.5772 3.47593 19.5772H8.87024C9.10331 19.5772 9.29247 19.7665 9.29247 19.9998C9.29247 20.2331 9.10331 20.4224 8.87024 20.4224Z" fill="#F9D4A0"></path>
										<path fill-rule="evenodd" clip-rule="evenodd" d="M0 19.9998C0 21.0757 0.874422 21.9505 1.94898 21.9505C3.02396 21.9505 3.89838 21.0757 3.89838 19.9998C3.89838 18.9243 3.02353 18.0491 1.94898 18.0491C0.874422 18.0491 0 18.9243 0 19.9998ZM0.844022 19.9998C0.844022 19.39 1.33971 18.8939 1.94898 18.8939C2.55867 18.8939 3.05436 19.39 3.05436 19.9998C3.05436 20.6096 2.55824 21.1057 1.94898 21.1057C1.33971 21.1057 0.844022 20.6096 0.844022 19.9998Z" fill="#F9D4A0"></path>
										<path d="M34.5241 20.4224H29.1297C28.8963 20.4224 28.7075 20.2331 28.7075 19.9998C28.7075 19.7665 28.8963 19.5772 29.1297 19.5772H34.5241C34.7575 19.5772 34.9463 19.7665 34.9463 19.9998C34.9463 20.2331 34.7571 20.4224 34.5241 20.4224Z" fill="#F9D4A0"></path>
										<path fill-rule="evenodd" clip-rule="evenodd" d="M34.1021 19.9998C34.1021 21.0757 34.9761 21.9505 36.051 21.9505C37.126 21.9505 38 21.0757 38 19.9998C38 18.9243 37.126 18.0491 36.051 18.0491C34.9761 18.0491 34.1021 18.9243 34.1021 19.9998ZM34.9461 19.9998C34.9461 19.39 35.4418 18.8939 36.051 18.8939C36.6599 18.8939 37.156 19.39 37.156 19.9998C37.156 20.6096 36.6603 21.1057 36.051 21.1057C35.4418 21.1057 34.9461 20.6096 34.9461 19.9998Z" fill="#F9D4A0"></path>
									</svg>
								</div>
								<!-- <p class="intagrate-item__title">Crypto Platform</p> -->
								<p class="intagrate-item__title">Analytics Platform</p>
					</div>
                </div>
                <div class="col-1"></div>
                <div class="col-lg-8 col-sm-12 col-md-7">
                    <div class="intagrate-block active">
								<div class="intagrate-block__title">
									Integrate IBAN <br> <span class="gold">In Your Product Range </span>
								</div>
								<div class="intagrate-block__text">
									Build Your Banking Product And Offer Named Bank Account To Individuals And
									Corporates.
									<br>
									Give Your Users The Flexibility To Send And Receive Funds Worldwide.<br>
									IBANs Can Be Issued Globally.<br>
									Variety of Corporate and Consumer/Retail Card Programs.
								</div>
								<div class="intagrate-block__buttons">
									<a href="#" target="_blank" rel="noopener noreferrer" class="intagrate-block__button">
										<span class="icon"><img src="assets_frontend/images/access.svg" alt="access"></span>
										Get Sandbox Access
									</a>
									<a href="digital-banking.html" class="intagrate-block__button">
										<span class="icon"><img src="assets_frontend/images/go_to.svg" alt="go_to"></span>
										Learn More
									</a>
								</div>

					</div>
                </div>
            </div>
        </div>
    </section>
<!-- product area end -->

<!-- product area start -->
    <section class="product py-5 my-5">
        <h2 class="welcome-heading-title-large text-center pb-4 mt-3"  style="text-transform: capitalize;font-size: 42px;">Benefits with Tata Neu HDFC Bank Credit Card</h2>
        <div class="container" style="padding: 0px 60px;">
            <div class="row d-flex align-items-center justify-content-between">
                <div class="col-lg-6 col-sm-12 col-md-12" data-aos="fade-up" data-aos-easing="ease-in-out"  data-aos-delay="100">
                    <div class="MuiStack-root css-7e3b0w">
                        <div class="MuiBox-root css-79elbk">
                            <div class="MuiStack-root css-f7furk">
                                <img src="assets_frontend/images/flight_logo.svg" width="80px" height="80px" alt="logo">
                            </div>
                        </div>
                        <h2 class="sc-kOHTFB ezKFuM">Complimentary Lounge Access</h2>
                        <h2 class="sc-dtInlm iUJjBQ">Enjoy up to 12 Airport Lounge visits</h2>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-12 col-md-12" data-aos="fade-up" data-aos-easing="ease-in-out"  data-aos-delay="200">
                    <div class="MuiStack-root css-7e3b0w">
                        <div class="MuiBox-root css-79elbk">
                            <div class="MuiStack-root css-f7furk">
                                <img src="assets_frontend/images/wallet_logo.svg" width="80px" height="80px" alt="logo">
                            </div>
                        </div>
                        <h2 class="sc-kOHTFB ezKFuM">Available on RuPay &amp; Visa Networks</h2>
                        <h2 class="sc-dtInlm iUJjBQ">Use your card with a wide variety of merchants and services</h2>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-12 col-md-12" data-aos="fade-up" data-aos-easing="ease-in-out"  data-aos-delay="100">
                    <div class="MuiStack-root css-7e3b0w">
                        <div class="MuiBox-root css-79elbk">
                            <div class="MuiStack-root css-f7furk">
                                <img src="assets_frontend/images/money_logo.svg" width="80px" height="80px" alt="logo">
                            </div>
                        </div>
                        <h2 class="sc-kOHTFB ezKFuM">Does Your Card Give you Up to 10% Savings Bowchers?</h2>
                        <h2 class="sc-dtInlm iUJjBQ">Enjoy savings of up to 10% on Tata Neu app</h2>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-12 col-md-12" data-aos="fade-up" data-aos-easing="ease-in-out"  data-aos-delay="200">
                    <div class="MuiStack-root css-7e3b0w">
                        <div class="MuiBox-root css-79elbk">
                            <div class="MuiStack-root css-f7furk">
                                <img src="assets_frontend/images/gift_logo.svg" width="80px" height="80px" alt="logo">
                            </div>
                        </div>
                        <h2 class="sc-kOHTFB ezKFuM">One Card that Rewards you for every Transaction</h2>
                        <h2 class="sc-dtInlm iUJjBQ">Enjoy savings of up to 1.5% on your everyday spends</h2>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-12 col-md-12" data-aos="fade-up" data-aos-easing="ease-in-out"  data-aos-delay="100">
                    <div class="MuiStack-root css-7e3b0w">
                        <div class="MuiBox-root css-79elbk">
                            <div class="MuiStack-root css-f7furk">
                                <img src="assets_frontend/images/category_logo.svg" width="80px" height="80px" alt="logo">
                            </div>
                        </div>
                        <h2 class="sc-kOHTFB ezKFuM">Categories - E-commerce, Bill Payments, Insurance, UPI Payments</h2>
                        <h2 class="sc-dtInlm iUJjBQ">Benefits across various categories including, e-commerce, bill payments, insurance, &amp; UPI payments</h2>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-12 col-md-12" data-aos="fade-up" data-aos-easing="ease-in-out"  data-aos-delay="200">
                    <div class="MuiStack-root css-7e3b0w">
                        <div class="MuiBox-root css-79elbk">
                            <div class="MuiStack-root css-f7furk">
                                <img src="assets_frontend/images/award_logo.svg" width="80px" height="80px" alt="logo">
                            </div>
                        </div>
                        <h2 class="sc-kOHTFB ezKFuM">Most Transparent Rewards Structure where 1 NeuCoin = 1 Rupee</h2>
                        <h2 class="sc-dtInlm iUJjBQ">Benefit from the most transparent rewards system where 1 NeuCoin equals 1 Rupee</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>
<!-- product area end -->
<?= $this->endSection() ?>