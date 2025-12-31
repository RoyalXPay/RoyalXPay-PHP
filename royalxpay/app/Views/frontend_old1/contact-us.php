<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
 
        <meta name="theme-color" content="#000000" />
        <!-- bootstrap css link  -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- aos css link -->
        <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
        <!-- slide css -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.css" integrity="sha512-6lLUdeQ5uheMFbWm3CP271l14RsX1xtx+J5x2yeIDkkiBpeVTNhTqijME7GgRKKi6hCqovwCoBTlRBEC20M8Mg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css" integrity="sha512-yHknP1/AwR+yx26cB1y0cjvQUMvEa2PFzt1c9LlS4pRQ5NOTZFWbhBig+X9G9eYW/8m0/4OXNx8pxJ6z57x0dw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <!-- external css -->
        <link rel="stylesheet" href="./assets/css/money.css"/>
        <!-- font awesome cdn -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

        <link href="https://fonts.cdnfonts.com/css/red-hat-text" rel="stylesheet">
        
        <title>Royal-Pay</title>
        <link rel="icon" type="image/x-icon" href="./assets/images/royalpaylogo.png">
    </head>

    <body>


    <!-- Navbar Area Started -->
        <nav class="navbar navbar-expand-sm navbar-dark bg-dark site-header py-2" style=" background-color: #000000!important;">
            <div class="container">
            <a class="navbar-brand d-flex align-items-center ps-0" href="index.html">
                <img src="./assets/images/logo.jpeg" alt="Royal pay" style="width: 250px; height: auto;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mynavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mynavbar">
                <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link pe-3" href="./index.html">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3" href="./service.html">Services</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3" href="./company.html">Company</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 active" href="./contact-us.html">Contact Us</a>
                </li>
                </ul>
                <div class="d-flex align-items-center justify-content-center">
                    <!-- <a href="./search-page.html" class="btn btn-md btn-warning rounded-circle me-3"><i class="fa fa-search"></i></a> -->
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#myModal" class="btn btn-md btn-warning rounded-pill px-4 py-2">Download The App</a>
                </div>
            </div>
            </div>
        </nav>
    <!-- Navbar Area end --> 
    
    <!-- search box area start -->
      <section class="search-container py-3">
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
    <section class="partner py-5">
        <div class="container">
            <div class="row d-flex align-items-center justify-content-between">
                <div class="col-lg-6 col-sm-12 col-md-12" data-aos="fade-left" data-aos-easing="ease-in-out">
                    <a href="javascript:void(0);" class="btn btn-md btn-danger rounded-pill px-4">Get in touch</a>
                    <h1 class="partner-large-head mt-3">Contact Us</h1>
                    <p class="partner-text mt-3 mb-4" style="font-size: 17px;">We know how strong our technology is to support you at every steps of your journey through our App. We also know that there can be some support that you may need which is beyond the technology. And that is why we are ever ready to help you with any support you may require. You may get in touch with us in more than just one way…</p>
                    <h5 class="partner-title">
                        <a href="tel:+911234567890" class="elementor-icon me-2"><i class="fas fa-mobile-alt"></i></a> +(91) 9320166735
                    </h5>
                </div>
                <div class="col-1"></div>
                <div class="col-lg-5 col-sm-12 col-md-12" data-aos="fade-right" data-aos-easing="ease-in-out">
                    <img src="./assets/images/contacts-1.png" style="height: auto; width: 100%;" class="banner-img">
                </div>
            </div>
        </div>
    </section>
    <!-- banner Area end --> 

     <!-- map Area end --> 
    <section class="partner py-5 my-5">
        <div class="container">
            <div class="row d-flex align-items-center justify-content-between">
                <div class="col-lg-6 col-sm-12 col-md-12">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3766.0889378175366!2d72.87712957498272!3d19.27849818196822!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3be7b1005023415f%3A0xf61b3b83a64fe615!2sSpace%20912!5e0!3m2!1sen!2sin!4v1748522072188!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
                <div class="col-1"></div>
                <div class="col-lg-5 col-sm-12 col-md-12" data-aos="fade-up" data-aos-easing="ease-in-out">
                    <h1 class="partner-large-head mt-3">Contact Information</h1>
                    <p class="partner-text mt-3 mb-4" style="font-size: 17px;">Get to know us, including how you can contact us <br> and how we keep your information secure…</p>
                    <div class="d-flex align-items-start mt-4">
                        <div class="pe-5">
                            <p class="partner-text mt-3 fw-bolder text-light">Registered Office:</p>
                            <p class="partner-text mt-2 mb-4" style="font-size: 15px;">703, Spaces 912, <br> Mira Bhayander Road, <br> Mira Road East, Thane, <br> Maharashtra, India, 401107 <br> support@geniemoney.in</p>
                        </div>
                        <div class="ps-5">
                            <p class="partner-text mt-3 fw-bolder text-light">Working Hours:</p>
                            <p class="partner-text mt-2 mb-4" style="font-size: 15px;">10 am till 6 pm <br> Monday to Friday</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- map Area end -->


    <!-- footer area start -->
         <footer class="site-footer pt-5" style="background-color: #32323E;">
            <div class="container">
                <div class="row pb-5">
                    <div class="col-lg-3 col-sm-12 col-md-6 pe-3 ps-3">
                        <a class="navbar-brand p-0 d-flex align-items-center mb-3 pb-2" href="index.html">
                            <img src="./assets/images/royalpaylogo.png" alt="Royal pay" style="width: 100px; height: auto;">
                        </a>
                        <p style="line-height: 1.6;"> Asia's First Hybrid Neo Bank. An all-in-one platform for all Banking & Financial services with over 75 financial institutions across the country</p>
                        <ul class="social-links no-dot d-flex mt-4">
                            <li>
                                <a class="d-i-flex align-center justify-center" target="_blank" href="#"><i class="fab fa-facebook-f"></i></a>
                             </li>
                             <li>
                                <a class="d-i-flex align-center justify-center" target="_blank" href="#"><i class="fab fa-twitter"></i></a> 
                            </li>
                            <li>
                                <a class="d-i-flex align-center justify-center" target="_blank" href="#"><i class="fab fa-youtube"></i></a>
                            </li>
                            <li>
                                <a class="d-i-flex align-center justify-center" target="_blank" href="#"><i class="fab fa-linkedin"></i></a> 
                            </li>
                        </ul>
                    </div>
                    <div class="col-lg-3 col-sm-12 col-md-6 ps-5 mt-2">
                        <h3 class="widget-title footer-title ps-5">Quick Links</h3>
                        <ul id="menu-quick-links" class="menu ps-5">
                            <li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-368"><a href="./testimonial.html">Home</a></li>
                            <li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-371"><a href="./blog.html">About Us</a></li>
                            <li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-372"><a href="./contact.html">Service</a></li>
                            <li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-371"><a href="./blog.html">Blogs</a></li>
                            <li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-372"><a href="./contact.html">Contacts Us</a></li>
                        </ul>
                        </ul>
                    </div>
                    <div class="col-lg-3 col-sm-12 col-md-6 pe-4 ps-3 mt-2">
                        <h3 class="widget-title footer-title">Contact Details</h3>
                        <ul class="detials-wrap no-dot">
                            <li class="d-flex align-items-start">
                                <i class="fa fa-map-marker mt-2"></i><div class="detail-content">703, Spaces 912, Mira Bhayander Road, Mira Road East, Thane, Maharashtra, India, 401107 support@geniemoney.in</div>
                            </li>
                            <li class="d-flex align-items-start">
                                <i class="fa fa-envelope mt-2"></i><div class="detail-content"><a href="mailto:presale@wpdating.com">info@geniemoney.com</a></div>
                            </li>
                            <li class="d-flex align-items-start">
                                <i class="fa fa-phone mt-2"></i><div class="detail-content"><a href="tel:+1 217 650 2736">+(91) 9320166735</a></div>
                            </li>
                        </ul>
                    </div>
                    <div class="col-lg-3 col-sm-12 col-md-6 ps-5 mt-2">
                        <h3 class="widget-title footer-title">Subscribe To Newsletter</h3>
                        <p>Be the first one to know about out new features, updates and many more.</p>
                        <div class="input-group mt-2">
                            <input type="text" class="form-control" placeholder="Email" aria-label="Username" aria-describedby="basic-addon1">
                            <a href="#" class="input-group-text bg-light"><i data-feather="file-text" class="fa fa-forward fill-white" style="color: #ffc107;"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="copyright-wrap">
                <div class="container d-flex align-items-center justify-content-between">
                    <div class="left-content">
                        <div class="copyright">
                            <p class="mb-0"> Copyright © 2024 Genie SoftSystem. All rights reserved.</p>
                        </div>
                    </div>
                    <div class="right-content">
                        <div class="menu-footer-bottom-menu-container">
                            <ul id="menu-footer-bottom-menu" class="menu d-flex align-items-center">
                                <li  class="menu-item menu-item-type-custom ps-3 mb-0 menu-item-object-custom menu-item-374"><a href="#">FAQ</a></li>
                                <li  class="menu-item menu-item-type-custom ps-3 mb-0 menu-item-object-custom menu-item-375"><a rel="privacy-policy" href="#">Privacy Policy</a></li>
                                <li  class="menu-item menu-item-type-custom ps-3 mb-0 menu-item-object-custom menu-item-376"><a href="#">Terms &amp; Conditions</a></li>
                            </ul>
                        </div>                
                    </div>
                </div>
            </div>
          </footer>
    <!-- footer area end -->


<!-- our js data -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@next/dist/aos.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js" integrity="sha512-XtmMtDEcNz2j7ekrtHvOVR4iwwaD6o/FUJe6+Zq+HgcCsk3kj4uSQQR8weQ2QVj1o0Pk6PwYLohm206ZzNfubg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script>
    AOS.init({
        offset: 200,
        duration: 1000,
        once: true, // The animation happens only once when scrolling in
        easing: 'ease-out' // Easing function for the animation
    });
  </script>
  <script type="text/javascript">
    $('.slider').slick({
  dots: true,
  infinite:true,
  speed: 300,
  autoplay: true,// Enable autoplay
  centerMode: true,
  autoplaySpeed: 2000, 
  slidesToShow: 3,
  slidesToScroll: 1,
  responsive: [
    {
      breakpoint: 1024,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 1,
        infinite: true,
        dots: true
      }
    },
    {
      breakpoint: 600,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1
      }
    },
    {
      breakpoint: 480,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1
      }
    }
    // You can unslick at a given breakpoint now by adding:
    // settings: "unslick"
    // instead of a settings object
  ]
});
  </script>
  <script>
    const placeholders = [
      "Search for bills",
      "Search for loans",
      "Search for insurance",
      "Search for contacts",
      "Search for employees"
    ];

    let index = 0;
    const input = document.getElementById("searchInput");

    function changePlaceholder() {
      input.setAttribute("placeholder", placeholders[index]);
      index = (index + 1) % placeholders.length;
    }

    // Change placeholder every 3 seconds
    setInterval(changePlaceholder, 3000);
  </script>
    <script>
        function redirectToIndex() {
            window.location.href = 'search-page.html';
        }

        // Optional: Also redirect if user clicks inside the text input
        document.getElementById("searchInput").addEventListener("click", redirectToIndex);
  </script>
    </body>
</html>