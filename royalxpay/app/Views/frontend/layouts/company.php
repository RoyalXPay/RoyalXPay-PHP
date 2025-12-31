
      <?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>  

        

     
    <!-- right side menu area start -->
        <div id="st-2" class=" st-sticky-share-buttons st-right st-toggleable st-has-labels st-show-total">
            <!-- <div class="st-total">
                <span class="st-label">2.8k</span>
                <span class="st-shares">Shares</span>
            </div> -->
            <a href="./recharge-bill"  class="st-btn border border-bottom-1" data-network="email" style="display: inline-block;">
                <span class="st-labels text-center digital" style="background-color: #7d7d7d; padding: 16px 16px;">
                    <span class="d-flex align-items-center justify-content-start">
                        <span class="pe-1">1.</span>  
                        <img src="assets_frontend/images/rechargeandbills.png"  style="width:30px;height:30px;">
                    </span>
                </span>
                <span class="st-label" data-network="email" style="padding: 15px 50px;"> Recharge & Bills</span>
            </a>
            <a href="./payment-services"  class="st-btn border border-bottom-1" data-network="twitter" style="display: inline-block;">
                <span class="st-labels text-center digital" style="background-color: #008AC3; padding: 16px 16px;">
                    <span class="d-flex align-items-center justify-content-start">
                        <span class="pe-1">2.</span> 
                        <img src="assets_frontend/images/payment-service.png"  style="width:30px;height:30px;">
                    </span>
                </span>
                <span class="st-label" data-network="twitter" style="padding: 15px 28px;"> Payments & services</span>
            </a>
            <a href="./discount-loyalities"  class="st-btn st-first border border-bottom-1" data-network="facebook" style="display: inline-block;">
                <span class="st-labels text-center digital" style="background-color: #4267B2; padding: 16px 16px;">
                    <span class="d-flex align-items-center justify-content-start">
                        <span class="pe-1">3.</span> 
                        <img src="assets_frontend/images/discount&loyalities.png"  style="width:30px;height:30px;">
                    </span>
                </span>
                <span class="st-label" data-network="facebook" style="padding: 15px 28px;"> Discounts & Loyalties</span>
            </a>
            <a href="./wealth"  class="st-btn border border-bottom-1" data-network="sharethis" style="display: inline-block;">
                <span class="st-labels text-center digital" style="background-color: #95D03A; padding: 16px 16px;">
                    <span class="d-flex align-items-center justify-content-start">
                        <span class="pe-1">4.</span>  
                        <img src="assets_frontend/images/wealthmanagement.png"  style="width:30px;height:30px;">
                    </span>
                </span>
                <span class="st-label" data-network="sharethis" style="padding: 15px 28px;"> Wealth Management</span>
            </a>
            <a href="./company"  class="st-btn st-last border border-bottom-1" data-network="linkedin" style="display: inline-block;">
                <span class="st-labels text-center digital" style="background-color: #0077b5; padding: 16px 16px;">
                    <span class="d-flex align-items-center justify-content-start">
                        <span class="pe-1">5.</span> 
                        <img src="assets_frontend/images/company.png"  style="width:30px;height:30px;">
                    </span>
                </span>
                <span class="st-label" data-network="linkedin" style="padding: 15px 73px;"> Company</span>
            </a>
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

    <!-- right side call & video call menu area start -->
     <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<!-- Chat Trigger Button -->
<button onclick="openChatBox()" style="position:fixed; bottom:20px;left: 5px; background:transparent; border:none; cursor:pointer; z-index:99999;">
     <img src="assets_frontend/images/audio-call.png" style="width: 55px; height: 150px;">
</button>

<!-- Chat Box UI -->
<div id="chatBox" style="display:none; position:fixed;     bottom: 187px;left: -9px; width:320px; background:#002B5B; border:2px solid white; border-radius:10px; padding:0; z-index:100000; box-shadow:0 4px 12px rgba(0,0,0,0.3); font-family:sans-serif;">

    <!-- Header with Title and Close Icon -->
 <div style="background:#008CCE; color:white; padding:10px 15px; border-top-left-radius:10px; border-top-right-radius:10px; display:flex; justify-content:space-between; align-items:center;">
    <span style="font-weight:bold; font-size:16px; display:flex; align-items:center; gap:6px;">
        <img src="assets_frontend/images/robot.png" style="width:34px; height:auto; display:block; margin-top: -5px;">
        A! Assistance
    </span>
    <span onclick="closeChatBox()" 
          style="cursor:pointer; font-size:12px; background:red; color:white; width:22px; height:22px; display:flex; align-items:center; justify-content:center; border-radius:50%;">
        &#10006;
    </span>
</div>

    <!-- Chat Messages Area -->
    <div id="chatMessages" style="max-height:260px; overflow-y:auto; font-size:14px; padding:10px 15px; display:flex; flex-direction:column; gap:8px;"></div>

    <!-- Input Field -->
    <div style="padding:10px 15px 15px;">
        <input type="text" id="chatInput" placeholder="Type your number..." onkeypress="handleKeyPress(event)" style="width:100%; padding:10px; border:none; border-radius:20px; font-size:14px;">
    </div>
</div>

<script>
function openChatBox() {
    document.getElementById('chatBox').style.display = 'block';
    loadInitialMessages();
}

function closeChatBox() {
    document.getElementById('chatBox').style.display = 'none';
}

function loadInitialMessages() {
    const chatArea = document.getElementById('chatMessages');
    chatArea.innerHTML = '';
    const messages = [
        "👋 Hi there! I'm your assistant.",
        "How can I help you today?",
        "Please enter your mobile number to receive a call back."
    ];

    let i = 0;
    const interval = setInterval(() => {
        if (i < messages.length) {
            appendChatBubble(messages[i], "left");
            i++;
        } else {
            clearInterval(interval);
        }
    }, 900);
}

function handleKeyPress(e) {
    if (e.key === 'Enter') {
        const input = document.getElementById('chatInput');
        const phone = input.value.trim();

        if (!/^\d{10}$/.test(phone)) {
            appendChatBubble("❗ Please enter a valid 10-digit number.", "left");
            return;
        }

        appendChatBubble("📱 " + phone, "right");
        input.value = "";

        initiateTaskCall(phone);
    }
}

function appendChatBubble(text, side = "left") {
    const chatArea = document.getElementById('chatMessages');
    const bubble = document.createElement('div');
    bubble.textContent = text;
    bubble.style.maxWidth = '80%';
    bubble.style.padding = '8px 12px';
    bubble.style.borderRadius = '16px';
    bubble.style.lineHeight = '1.4';
    bubble.style.wordWrap = 'break-word';
    bubble.style.background = side === "left" ? "#e5e5ea" : "#007aff";
    bubble.style.color = side === "left" ? "#000" : "#fff";
    bubble.style.alignSelf = side === "left" ? "flex-start" : "flex-end";

    chatArea.appendChild(bubble);
    chatArea.scrollTop = chatArea.scrollHeight;
}

function initiateTaskCall(phone) {
    const apiKey = "ash_id_3f327144a7b188931f1289390a28f33d4e48e684157c49fbf5ea37a28383d672";
    const username = "royalxpay";
    const code = "CLT000020";
    const agentId = "AGN0E5383A807";

    const headers = {
        'Authorization': 'Bearer ' + apiKey,
        'Content-Type': 'application/json'
    };

    const body = {
        credentials: { username, code },
        agentId: agentId,
        userData: {
            name: "Website User",
            phoneNumber: phone,
            countryCode: "+91",
            details: "Website Call Request",
            specialNotes: "User submitted from chat widget",
            location: "N/A",
            email: "",
            language: "English"
        }
    };

    appendChatBubble("📞 Connecting your call. Please wait...", "left");

    axios.post('https://api-eaicall.eaicall.com/api/ashtask/initiate/', body, { headers })
        .then(res => {
            if (res.data.status === "success") {
                appendChatBubble("✅ Call created! Task ID: " + res.data.taskId, "left");
            } else {
                appendChatBubble("❌ Error: " + res.data.message, "left");
            }
        })
        .catch(err => {
            appendChatBubble("❌ Failed to initiate call. Please try again later.", "left");
            console.error(err);
        });
}
</script>
    <!-- right side call & video call menu area end -->

    <!-- banner Area end --> 
        <section class="partner py-5">
            <div class="container py-5">
                <div class="row d-flex align-items-center justify-content-between">
                    <div class="col-lg-5 col-sm-12 col-md-12" data-aos="fade-left" data-aos-easing="ease-in-out">
                        <h1 class="partner-large-head mt-2">Company Overview</h1>
                        <p class="partner-text mt-3 mb-2" style="font-size: 17px; text-align: justify;">Diaspora Banking App is a flagship initiative by DBA Payment Services. Designed to unify the global diaspora under one compliant banking roof, we are building a full-service ecosystem with strong partnership and secure banking technology</p>
                    </div>
                    <div class="col-2"></div>
                    <div class="col-lg-5 col-sm-12 col-md-12 d-flex justify-content-end align-items-center" data-aos="fade-right" data-aos-easing="ease-in-out">
                        <img src="assets_frontend/images/banking-saving-money-management-account-concept.jpg" style="height: auto; width: 100%;" class="banner-img rounded">
                    </div>
                </div>
                <div class="row d-flex align-items-center justify-content-between">
                    <div class="col-lg-5 col-sm-12 col-md-12 d-flex justify-content-end align-items-center" data-aos="fade-right" data-aos-easing="ease-in-out">
                        <img src="assets_frontend/images/people-office-analyzing-checking-finance-graphs.jpg" style="height: auto; width: 100%;" class="banner-img rounded">
                    </div>
                    <div class="col-2"></div>
                    <div class="col-lg-5 col-sm-12 col-md-12 d-flex flex-column justify-content-end" data-aos="fade-left" data-aos-easing="ease-in-out">
                        <h1 class="partner-large-head mt-2">Mission</h1>
                        <p class="partner-text mt-3 mb-2" style="font-size: 17px;">To deliver a trusted inclusive financial ecosystem that enables diaspora communitiesto send, spend, and support their families through one seamless platform– combining banking, payments, and essential services under one digital roof. </p>
                    </div>
                </div>
                <div class="row d-flex align-items-center justify-content-between">
                    <div class="col-lg-5 col-sm-12 col-md-12" data-aos="fade-left" data-aos-easing="ease-in-out">
                        <h1 class="partner-large-head mt-2">Vision</h1>
                        <p class="partner-text mt-3 mb-2" style="font-size: 17px; text-align: justify;">To be the first Global Diaspora Bank by 2026  by shifting unbanked beneficiaries from informal Hawala networks to a secure and regulated platform. Through our B2C app, partnerships with local banks, financial literacy and  loyalty driven incentives, we aim to enable legal, transparent, and cross-border transactions.</p>
                    </div>
                    <div class="col-2"></div>
                    <div class="col-lg-5 col-sm-12 col-md-12 d-flex justify-content-end align-items-center" data-aos="fade-right" data-aos-easing="ease-in-out">
                        <img src="assets_frontend/images/wire-transfer-young-man-using-his-smartphone-laptop-banking-transaction-some-payments.jpg" style="height: auto; width: 100%;" class="banner-img rounded">
                    </div>
                </div>
            </div>
        </section>
    <!-- banner Area end --> 

<!-- The Modal -->
<div class="modal fade" id="myModal" style="background-color: rgba(0, 0, 0, 0.8);">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <!-- Modal body -->
        <div class="modal-body">
            <div class="row d-flex align-items-center">
                <div class="col-lg-6 col-sm-12 login-wrapper d-flex align-items-center">
                    <img src="assets_frontend/images/search.png" class="image-section" style="width: 90%;height: auto;">
                </div>
                <div class="col-lg-6 col-sm-12">
                    <div class="d-flex justify-content-end align-items-center p-2">
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="pe-5">
                        <h2 class="sc-kOHTFB ezKFuM text-dark mb-0" style="font-size: 22px;">Get started with Royal XPay</h2>
                        <div class="intagrate-block__text mt-0">Enter your mobile number to login and signup</div>
                        <!-- Nav tabs -->
                        <ul class="nav nav-pills flex-column flex-sm-row mt-4 email-list" role="tablist">
                            <li class="nav-item flex-sm-fill text-sm-center">
                                <a class="nav-link active text-dark" data-bs-toggle="tab" href="#navpill-1111" role="tab">
                                    <span>Mobile</span>
                                </a>
                            </li>
                            <li class="nav-item flex-sm-fill text-sm-center">
                                <a class="nav-link text-dark" data-bs-toggle="tab" href="#navpill-2222" role="tab">
                                    <span>Email</span>
                                </a>
                            </li>
                        </ul>
                        <!-- Tab panes -->
                        <div class="tab-content border border-0 mt-2">
                            <div class="tab-pane active" id="navpill-1111" role="tabpanel">
                                <form class="mt-4" id="myForm">
                                    <!-- <label for="tel" class="text-dark">Mobile</label> -->
                                    <div class="form-group mb-3">
                                        <input type="tel" class="form-control" placeholder="Enter Mobile" id="MobileInput" required style="height:50px;box-shadow: none!important;">
                                    </div>
                                    <button type="button" class="btn btn-warning w-100 py-2 mt-4" id="proceedBtn">Proceed</button>
                                </form>
                            </div>
                            <div class="tab-pane" id="navpill-2222" role="tabpanel">
                                <form class="mt-4" id="myFormEmail">
                                    <div class="form-group mb-3">
                                        <input type="email" class="form-control" placeholder="Enter Email" id="emailInput" required style="height:50px;box-shadow: none!important;">
                                    </div>
                                    <button type="button" class="btn btn-warning w-100 py-2 mt-4" id="proceedBtnEmail">Proceed</button>
                                </form>
                            </div>
                        </div>
                        <div class="row py-4">
                            <div class="col-lg-8 col-sm-12 d-flex justify-content-center align-items-center flex-column">
                                <h3 class="heading">Scan to download our app</h3>
                                <p class="subheading">For smooth &amp; fast experience</p>
                            </div>
                            <div class="col-lg-4 col-sm-12 d-flex justify-content-center align-items-center">
                                <img src="assets_frontend/images/qr.jpeg" style="width: 81px;height: auto;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-center mt-3">
                <a href="http://185.188.127.32/index.php/login" target="_blank" class="btn btn-warning w-25 py-2 rounded-pill">Merchant Login</a>
            </div>
        </div>
    </div>
  </div>
</div>

<!-- The Modal for mobile otp -->
<div class="modal fade" id="myModal1" tabindex="-1" style="background-color: rgba(0, 0, 0, 0.8);">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <!-- Modal body -->
        <div class="modal-body">
            <div class="row d-flex align-items-center">
                <div class="col-lg-6 col-sm-12 login-wrapper d-flex align-items-center">
                    <img src="assets_frontend/images/search.png" class="image-section" style="width: 90%;height: auto;">
                </div>
                <div class="col-lg-6 col-sm-12">
                    <div class="d-flex justify-content-end align-items-center p-2">
                        <button type="button" class="btn-close" data-bs-toggle="modal" data-bs-target="#myModal1"></button>
                    </div>
                    <div class="pe-5">
                        <h2 class="sc-kOHTFB ezKFuM text-dark mb-0" style="font-size: 22px;">Enter Mobile OTP</h2>
                        <div class="intagrate-block__text mt-0">A one time password has been sent to <strong class="text-dark" style="font-weight: 500;" id="showMobileHere"></strong>&nbsp;<a href="javascript:void();" class="text-warning" data-bs-dismiss="modal" style="font-weight: 500;">Change</a></div>
                        <form class="mt-4">
                            <div class="otp-boxes d-flex justify-content-between">
                                <input type="tel"  inputmode="numeric" class="otp-box  layout-2 " maxlength="1" data-index="0" autocomplete="new-password" value="">
                                <input type="tel"  inputmode="numeric" class="otp-box  layout-2 " maxlength="1" data-index="1" autocomplete="new-password" value="">
                                <input type="tel"  inputmode="numeric" class="otp-box  layout-2 " maxlength="1" data-index="2" autocomplete="new-password" value="">
                                <input type="tel"  inputmode="numeric" class="otp-box  layout-2 " maxlength="1" data-index="3" autocomplete="new-password" value="">
                            </div>
                            <div class="intagrate-block__text mt-2">Didn’t receive the OTP yet? &nbsp;<a href="javascript:void();" class="text-warning" style="font-weight: 500;">Resend</a></div>
                            <button type="button" class="btn btn-warning w-100 py-2 mt-4" data-bs-toggle="modal" data-bs-target="#myModal1">Submit</button>
                        </form>
                        <div class="row py-4">
                            <div class="col-lg-8 col-sm-12 d-flex justify-content-center align-items-center flex-column">
                                <h3 class="heading">Scan to download our app</h3>
                                <p class="subheading">For smooth &amp; fast experience</p>
                            </div>
                            <div class="col-lg-4 col-sm-12 d-flex justify-content-center align-items-center">
                                <img src="assets_frontend/images/qr.jpeg" style="width: 81px;height: auto;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-center mt-3">
                <a href="http://185.188.127.32/index.php/login" target="_blank" class="btn btn-warning w-25 py-2 rounded-pill">Merchant Login</a>
            </div>
        </div>
    </div>
  </div>
</div>

<!-- The Modal for Email otp -->
<div class="modal fade" id="myModalEmail" tabindex="-1" style="background-color: rgba(0, 0, 0, 0.8);">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <!-- Modal body -->
        <div class="modal-body">
            <div class="row d-flex align-items-center">
                <div class="col-lg-6 col-sm-12 login-wrapper d-flex align-items-center">
                    <img src="assets_frontend/images/search.png" class="image-section" style="width: 90%;height: auto;">
                </div>
                <div class="col-lg-6 col-sm-12">
                    <div class="d-flex justify-content-end align-items-center p-2">
                        <button type="button" class="btn-close" data-bs-toggle="modal" data-bs-target="myModalEmail"></button>
                    </div>
                    <div class="pe-5">
                        <h2 class="sc-kOHTFB ezKFuM text-dark mb-0" style="font-size: 22px;">Enter Email OTP</h2>
                        <div class="intagrate-block__text mt-0">A one time password has been sent to <strong class="text-dark" style="font-weight: 500;" id="showEmailHere"></strong>&nbsp;<a href="javascript:void();" class="text-warning" data-bs-dismiss="modal" style="font-weight: 500;">Change</a></div>
                        <form class="mt-4">
                            <div class="otp-boxes d-flex justify-content-between">
                                <input type="tel"  inputmode="numeric" class="otp-box  layout-2 " maxlength="1" data-index="0" autocomplete="new-password" value="">
                                <input type="tel"  inputmode="numeric" class="otp-box  layout-2 " maxlength="1" data-index="1" autocomplete="new-password" value="">
                                <input type="tel"  inputmode="numeric" class="otp-box  layout-2 " maxlength="1" data-index="2" autocomplete="new-password" value="">
                                <input type="tel"  inputmode="numeric" class="otp-box  layout-2 " maxlength="1" data-index="3" autocomplete="new-password" value="">
                            </div>
                            <div class="intagrate-block__text mt-2">Didn’t receive the OTP yet? &nbsp;<a href="javascript:void();" class="text-warning" style="font-weight: 500;">Resend</a></div>
                            <button type="button" class="btn btn-warning w-100 py-2 mt-4" data-bs-toggle="modal" data-bs-target="#myModal1">Submit</button>
                        </form>
                        <div class="row py-4">
                            <div class="col-lg-8 col-sm-12 d-flex justify-content-center align-items-center flex-column">
                                <h3 class="heading">Scan to download our app</h3>
                                <p class="subheading">For smooth &amp; fast experience</p>
                            </div>
                            <div class="col-lg-4 col-sm-12 d-flex justify-content-center align-items-center">
                                <img src="assets_frontend/images/qr.jpeg" style="width: 81px;height: auto;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-center mt-3">
                <a href="http://185.188.127.32/index.php/login" target="_blank" class="btn btn-warning w-25 py-2 rounded-pill">Merchant Login</a>
            </div>
        </div>
    </div>
  </div>
</div>

 <!-- Coming Soon Modal -->
  <div class="modal fade" id="comingSoonModal" tabindex="-1" aria-labelledby="comingSoonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 shadow-lg">
        <div class="modal-header border-0">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <h1 class="partner-large-head w-100 text-center text-dark" id="comingSoonModalLabel" style="font-size: 40px;">🚀 Coming Soon!</h1>
          <p class="fs-5 text-danger mt-3 px-3" style="line-height: 26px;">We’re working hard to add multiple languages for a better experience. Stay tuned!</p>
          <!-- <img src="https://cdn-icons-png.flaticon.com/512/753/753345.png" alt="Coming Soon" class="img-fluid my-3" style="max-width: 50px;"> -->
        </div>
        <div class="modal-footer border-0 justify-content-center">
          <button type="button" class="btn btn-success px-4 rounded-pill" data-bs-dismiss="modal">Got it</button>
        </div>
      </div>
    </div>
  </div>

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
            window.location.href = 'search-page.php';
        }

        // Optional: Also redirect if user clicks inside the text input
        document.getElementById("searchInput").addEventListener("click", redirectToIndex);
  </script>
<script>
    document.getElementById('proceedBtn').addEventListener('click', function(){
        var form = document.getElementById('myForm');
        var mobile = document.getElementById('MobileInput').value;
        if(form.checkValidity()){
            // Set email in modal
        document.getElementById('showMobileHere').innerText = mobile;

            // Open modal programmatically
            var myModal = new bootstrap.Modal(document.getElementById('myModal1'));
            myModal.show();
        } else {
            // Trigger native HTML5 validation UI
            form.reportValidity();
        }
    });

    document.getElementById('proceedBtnEmail').addEventListener('click', function(){
    var form = document.getElementById('myFormEmail');
    var email = document.getElementById('emailInput').value;

    if(form.checkValidity()){
        // Set email in modal
        document.getElementById('showEmailHere').innerText = email;

        // Open modal programmatically
        var myModal = new bootstrap.Modal(document.getElementById('myModalEmail'));
        myModal.show();
    } else {
        // Trigger native HTML5 validation UI
        form.reportValidity();
    }
});

    document.addEventListener("DOMContentLoaded", function() {
  const inputs = document.querySelectorAll(".otp-box");

  inputs.forEach((input, index) => {
    input.addEventListener("input", function(e) {
      const value = e.target.value;
      if (value.length === 1) {
        if (index < inputs.length - 1) {
          inputs[index + 1].focus();
        }
      }
    });

    input.addEventListener("keydown", function(e) {
      if (e.key === "Backspace" && !input.value && index > 0) {
        inputs[index - 1].focus();
      }
    });
  });
});

</script>


 <?= $this->endSection() ?>