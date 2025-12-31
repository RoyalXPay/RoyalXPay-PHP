
      <?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>  

     <style>
/* Hide dropdown by default */
#searchResults {
  display: none;
  list-style: none;
  padding: 0;
  margin-top: 5px;
  background: #fff;
  border: 1px solid #ddd;
  border-radius: 10px;
  max-height: 250px;
  overflow-y: auto;
  position: absolute;
  width: 100%;
  z-index: 1000;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* Ensure visible text */
#searchResults li {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 15px;
  cursor: pointer;
  transition: background 0.2s;
  border-bottom: 1px solid #eee;
  color: #000;
  background: #fff;
}

#searchResults li:last-child {
  border-bottom: none;
}

#searchResults li:hover {
  background: #f5f5f5;
}

    </style>
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
      <!-- right side call & video call menu area start -->
     <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<!-- Chat Trigger Button -->
<button onclick="openChatBox()" style="position: fixed;
    bottom: 39px;
    left: -57px;
    background: transparent;
    border: none;
    cursor: pointer;
    z-index: 99999;
    margin: 0 53px;">
     <img src="assets_frontend/images/audio-call.png" style="width: 43px; height: 43px;">
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

    <!-- search box area start -->
      <section class="search-container py-3">
        <div class="container">
            <div class="d-flex align-items-center justify-content-center">
                <div class="search-box">
                    <div class="form-group" style="position: relative;">
                            
                            <input type="search" class="form-control search-input" id="searchSubmitInput" placeholder="Ask anything...">
                            <ul id="searchResults" style="list-style:none; padding:0; margin:0; background:#152458; border:1px solid #ccc; max-height:200px; overflow-y:auto; position:absolute; width:100%; z-index:999;"></ul>
<script>
// Map items to their URLs from the navbar
const itemsMap = {
  "Recharge & Bills": "./recharge-bill",
  "Payments & Services": "./payment-services",
  "Discounts & Loyalties": "./discount-loyalities",
  "Wealth Management": "./wealth",
  "Company": "./company",
   "Service": "./payment-services",
    "Contact Us": "./contact-us"
};

const items = Object.keys(itemsMap);

const searchInput = document.getElementById("searchSubmitInput");
const searchResults = document.getElementById("searchResults");

function renderList(filter = "") {
  searchResults.innerHTML = "";
  const filtered = items.filter(item =>
    item.toLowerCase().includes(filter.toLowerCase())
  );

  if (filtered.length > 0) {
    filtered.forEach(item => {
      const li = document.createElement("li");
      li.textContent = item;
      li.style.padding = "10px";
      li.style.cursor = "pointer";
      li.onclick = () => {
        searchInput.value = item;
        searchResults.style.display = "none";
        // Redirect to the corresponding page
        window.location.href = itemsMap[item];
      };
      searchResults.appendChild(li);
    });
    searchResults.style.display = "block";
  } else {
    searchResults.style.display = "none";
  }
}

// Show full list on focus
searchInput.addEventListener("focus", () => {
  renderList(); // no filter = show all
});

// Filter while typing
searchInput.addEventListener("input", () => {
  renderList(searchInput.value);
});

// Hide on outside click
document.addEventListener("click", (e) => {
  if (!e.target.closest(".form-group")) {
    searchResults.style.display = "none";
  }
});
</script>


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
                            <a href="tel:+911234567890" class="elementor-icon me-2"><i class="fas fa-mobile-alt"></i></a> +91-9625277617
                        </h5>
                    </div>
                    <div class="col-1"></div>
                    <div class="col-lg-5 col-sm-12 col-md-12" data-aos="fade-right" data-aos-easing="ease-in-out">
                        <img src="assets_frontend/images/contacts-1.png" style="height: auto; width: 100%;" class="banner-img">
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
                        <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d14528.76919793185!2d54.3956413!3d24.4441127!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e5e69a30c9c992d%3A0x2a41233389f31813!2sHadbat%20Al%20Za%E2%80%99faranah!5e0!3m2!1sen!2sin!4v1751370946098!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                    <div class="col-1"></div>
                    <div class="col-lg-5 col-sm-12 col-md-12" data-aos="fade-up" data-aos-easing="ease-in-out">
                        <h1 class="partner-large-head mt-3">Contact Information</h1>
                        <p class="partner-text mt-3 mb-4" style="font-size: 17px;">Get to know us, including how you can contact us <br> and how we keep your information secure…</p>
                        <div class="d-flex align-items-start mt-4">
                            <div class="pe-5">
                                <p class="partner-text mt-3 fw-bolder text-light">Registered Office:</p>
                                <p class="partner-text mt-2 mb-4" style="font-size: 15px;">Zone, Habit Al Za’faranahSector,<br> E29, Mezzanine FloorOffice #3,<br>
                                    Abu Dhabi, UAE” And <br>
                                    “Duqe, Quarter Deck, QE2 <br>
                                    Mina Rashid, P.O box 554789<br>
                                    Dubai - United Arab Emirates</p>
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
const pages = [
  { name: "Recharge & Bills", url: "./recharge-bill" },
  { name: "Payments & Services", url: "./payment-services" },
  { name: "Discounts & Loyalties", url: "./discount-loyalities" },
  { name: "Wealth Management", url: "./wealth" },
  { name: "Company", url: "./company" },
   { name: "Service", url: "./payment-services" },
     { name: "contact Us", url: "./contact-us" }
];

const input = document.getElementById("searchSubmitInput");
const results = document.getElementById("searchResults");

input.addEventListener("input", function () {
  const query = this.value.toLowerCase();
  results.innerHTML = "";

  if (query.length === 0) return;

  const filtered = pages.filter(p => p.name.toLowerCase().includes(query));

  filtered.forEach(p => {
    const li = document.createElement("li");
    li.textContent = p.name;
    li.style.padding = "8px";
    li.style.cursor = "pointer";

    li.onclick = () => {
      window.location.href = p.url; // redirect to page
    };

    results.appendChild(li);
  });
});

const searchInput = document.getElementById("searchSubmitInput");
const searchResults = document.getElementById("searchResults");

// Show results on focus if list has items
searchInput.addEventListener("focus", () => {
  if (searchResults.children.length > 0) {
    searchResults.style.display = "block";
  }
});

// Hide when clicked outside
document.addEventListener("click", (e) => {
  if (!e.target.closest(".form-group")) {
    searchResults.style.display = "none";
  }
});

// Example: populate results dynamically
searchInput.addEventListener("input", () => {
  if (searchInput.value.trim() !== "") {
    searchResults.style.display = "block";
  } else {
    searchResults.style.display = "none";
  }
});

</script>

<script>
// Example services list
const items = [
  "Recharge & Bills",
  "Payments & Services",
  "Discounts & Loyalties",
  "Wealth Management",
  "Company",
  "Service",
  "Contact Us"
];

const searchInput = document.getElementById("searchSubmitInput");
const searchResults = document.getElementById("searchResults");

// Populate dropdown
function renderList(filter = "") {
  searchResults.innerHTML = "";
  const filtered = items.filter(item =>
    item.toLowerCase().includes(filter.toLowerCase())
  );

  if (filtered.length > 0) {
    filtered.forEach(item => {
      const li = document.createElement("li");
      li.textContent = item;
      li.onclick = () => {
        searchInput.value = item;
        searchResults.style.display = "none";
      };
      searchResults.appendChild(li);
    });
    searchResults.style.display = "block";
  } else {
    searchResults.style.display = "none";
  }
}

// Show all on focus
searchInput.addEventListener("focus", () => {
  renderList();
});

// Filter on typing
searchInput.addEventListener("input", () => {
  renderList(searchInput.value);
});

// Hide when clicked outside
document.addEventListener("click", (e) => {
  if (!e.target.closest(".form-group")) {
    searchResults.style.display = "none";
  }
});
</script>

<!-- Results Area -->



    <?= $this->endSection() ?>