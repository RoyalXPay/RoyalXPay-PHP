
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

    <!-- tab area start -->
    <div class="container py-5">
        <!-- Nav tabs -->
            <ul class="nav nav-pills flex-column flex-sm-row mb-3 menu-tab" role="tablist">
                <li class="nav-item flex-sm-fill text-sm-center">
                    <a class="nav-link active text-light" data-bs-toggle="tab" href="#navpill-11" role="tab">
                        <span>Utility And Payment</span>
                    </a>
                </li>
                <li class="nav-item flex-sm-fill text-sm-center">
                    <a class="nav-link text-light" data-bs-toggle="tab" href="#navpill-22" role="tab">
                        <span>Topup</span>
                    </a>
                </li>
                <li class="nav-item flex-sm-fill text-sm-center">
                    <a class="nav-link text-light" data-bs-toggle="tab" href="#navpill-33" role="tab">
                        <span>Transportation</span>
                    </a>
                </li>
                <li class="nav-item flex-sm-fill text-sm-center">
                    <a class="nav-link text-light" data-bs-toggle="tab" href="#navpill-44" role="tab">
                        <span>International Topup</span>
                    </a>
                </li>
                <li class="nav-item flex-sm-fill text-sm-center">
                    <a class="nav-link text-light" data-bs-toggle="tab" href="#navpill-55" role="tab">
                        <span>Telcommunication</span>
                    </a>
                </li>
                <li class="nav-item flex-sm-fill text-sm-center">
                    <a class="nav-link text-light" data-bs-toggle="tab" href="#navpill-66" role="tab">
                        <span>Vouchers</span>
                    </a>
                </li>
            </ul>
        <!-- Tab panes -->
                    <div class="tab-content mt-4">
                        <div class="tab-pane active p-3" id="navpill-11" role="tabpanel">
                            <div class="row d-flex align-items-center">
                                <div class="col-lg-6 col-sm-12">
                                    <div class="container-box d-flex justify-content-start align-items-center flex-wrap pe-5">
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill  bg-warning" style="padding: 12px 0px; box-shadow: none!important;" rel="noreferrer">
                                            <img src="assets_frontend/images/sergas.png" alt="Pay Loan EMI" style="width: 100px; height: 100px;">
                                            <!-- <span>Pay Loan EMI</span> -->
                                        </a>
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill  bg-light" style="padding: 12px 0px;" rel="noreferrer">
                                            <img src="assets_frontend/images/fewa_direct.png" alt="Pay Loan EMI" style="width: 100px; height: 100px;">
                                            <!-- <span>Pay Loan EMI</span> -->
                                        </a>
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill bg-light"  style="padding: 12px 0px;">
                                            <img src="assets_frontend/images/etisalat_direct.png" alt="Electricity Bill" style="width: 100px; height: 100px;">
                                            <!-- <span>Electricity Bill</span> -->
                                        </a>
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill  bg-light" style="padding: 12px 0px;" >
                                            <img src="assets_frontend/images/sewaPay.png" alt="Pay Loan EMI" style="width: 100px; height: 100px;border-radius: 49%;">
                                            <!-- <span>Pay Loan EMI</span> -->
                                        </a>
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill  bg-light" style="padding: 12px 0px;">
                                            <img src="assets_frontend/images/noqodi.png" alt="FASTag Recharge" style="width: 100px; height: 100px;">
                                        </a>
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill  bg-light" style="padding: 12px 0px;"  rel="noreferrer">
                                            <img src="assets_frontend/images/salik.png" alt="Pay Loan EMI" style="width: 100px; height: 100px;">
                                            <!-- <span>Pay Loan EMI</span> -->
                                        </a>
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill bg-light"  style="padding: 12px 0px;" rel="noreferrer">
                                            <img src="assets_frontend/images/sharjah_tahseel_topup.png" alt="Pay Loan EMI" style="width: 100px; height: 100px;border-radius: 49%;">
                                            <!-- <span>Pay Loan EMI</span> -->
                                        </a>
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill bg-light"  style="padding: 12px 0px;">
                                            <img src="assets_frontend/images/national_bond.png" alt="Mobile Recharge" style="width: 100px; height: 100px;">
                                        <!-- <span>Mobile Recharge</span> -->
                                        </a>
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill bg-light"  style="padding: 12px 0px;"  rel="noreferrer">
                                            <img src="assets_frontend/images/nol.png" alt="LIC / Insurance" style="width: 100px; height: 100px;">
                                        
                                        </a>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-12">
                                    <div class="card border border-0 bg-light" style="padding: 0px 12px;">
                                        <div class="row py-3 px-2" style="background-color: rgba(255 , 193 , 7 , 1); border-radius: 6px 6px 0px 0px;">
                                            <div class="col-lg-4 col-sm-12 d-flex justify-content-start align-items-center">
                                                <h2 class="sc-kOHTFB ezKFuM text-dark mb-0" style="font-size: 22px;">Sergas</h2>
                                            </div>
                                            <div class="col-lg-8 col-sm-12 d-flex justify-content-end align-items-center">
                                                <button type="button" class="btn btn-warning py-2 px-4 border border-0" style="background-color: rgba(255 , 255 , 255, 0.3)!important;">Choose Safety. Choose SERGAS.</button>
                                            </div>
                                        </div>
                                        <form class="mt-4 px-2" id="myForm">
                                            <label for="tel" class="text-dark">Customer Id</label>
                                            <div class="form-group mb-3">
                                                <input type="tel" class="form-control" placeholder="GXXXXXX" required style="height:50px;box-shadow: none!important;">
                                            </div>
                                            <label for="tel" class="text-dark my-3">Emirates</label><br>
                                            <input type="radio" class="btn-check" name="options" id="option1" autocomplete="off">
                                            <label class="btn btn-outline-warning rounded-pill font-weight-medium text-dark px-4" for="option1">AbuDhabi</label>

                                            <input type="radio" class="btn-check" name="options" id="option2" autocomplete="off">
                                            <label class="btn btn-outline-warning rounded-pill font-weight-medium text-dark px-4" for="option2">Dubai</label>

                                            <div class="d-flex align-items-center justify-content-center my-3">
                                                <button type="button" class="btn btn-warning w-50 py-2 my-4">Check Balance</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane p-3" id="navpill-22" role="tabpanel">
                            <div class="row d-flex align-items-center">
                                <div class="col-lg-6 col-sm-12">
                                    <div class="container-box d-flex justify-content-start align-items-center flex-wrap pe-5">
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill  bg-warning" style="padding: 12px 0px; box-shadow: none!important;" rel="noreferrer">
                                            <img src="assets_frontend/images/etisalat_direct.png" alt="Pay Loan EMI" style="width: 100px; height: 100px;">
                                            <!-- <span>Pay Loan EMI</span> -->
                                        </a>
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill  bg-light" style="padding: 12px 0px;">
                                            <img src="assets_frontend/images/noqodi.png" alt="FASTag Recharge" style="width: 100px; height: 100px;">
                                        </a>
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill  bg-light" style="padding: 12px 0px;" rel="noreferrer">
                                            <img src="assets_frontend/images/fewa_direct.png" alt="Pay Loan EMI" style="width: 100px; height: 100px;">
                                            <!-- <span>Pay Loan EMI</span> -->
                                        </a>
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill  bg-light" style="padding: 12px 0px;" >
                                            <img src="assets_frontend/images/sewaPay.png" alt="Pay Loan EMI" style="width: 100px; height: 100px;border-radius: 49%;">
                                            <!-- <span>Pay Loan EMI</span> -->
                                        </a>
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill  bg-light" style="padding: 12px 0px;"  rel="noreferrer">
                                            <img src="assets_frontend/images/salik.png" alt="Pay Loan EMI" style="width: 100px; height: 100px;">
                                            <!-- <span>Pay Loan EMI</span> -->
                                        </a>
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill bg-light"  style="padding: 12px 0px;" rel="noreferrer">
                                            <img src="assets_frontend/images/sharjah_tahseel_topup.png" alt="Pay Loan EMI" style="width: 100px; height: 100px;border-radius: 49%;">
                                            <!-- <span>Pay Loan EMI</span> -->
                                        </a>
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill bg-light"  style="padding: 12px 0px;">
                                            <img src="assets_frontend/images/national_bond.png" alt="Mobile Recharge" style="width: 100px; height: 100px;">
                                        <!-- <span>Mobile Recharge</span> -->
                                        </a>
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill bg-light"  style="padding: 12px 0px;"  rel="noreferrer">
                                            <img src="assets_frontend/images/nol.png" alt="LIC / Insurance" style="width: 100px; height: 100px;">
                                        
                                        </a>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-12">
                                    <div class="card border border-0 bg-light" style="padding: 0px 12px;">
                                        <div class="row py-3 px-2" style="background-color: rgba(255 , 193 , 7 , 1); border-radius: 6px 6px 0px 0px;">
                                            <div class="col-lg-4 col-sm-12 d-flex justify-content-start align-items-center">
                                                <h2 class="sc-kOHTFB ezKFuM text-dark mb-0" style="font-size: 22px;">Etisalat</h2>
                                            </div>
                                            <div class="col-lg-8 col-sm-12 d-flex justify-content-end align-items-center">
                                                <button type="button" class="btn btn-warning py-2 px-4 border border-0" style="background-color: rgba(255 , 255 , 255, 0.3)!important;">Wasel Recharge</button>
                                            </div>
                                        </div>
                                        <form class="mt-4 px-2" id="myForm">
                                            <button type="button" class="btn btn-warning py-2 my-4 px-4 d-flex align-items-center rounded-pill">Prepaid Wasel Recharge <img class="slider_icon svelte-1jtkxul ps-2" src="assets_frontend/images/WaselRecharge.png" alt="" style="width:40px;"></button>
                                            <label for="tel" class="text-dark">Mobile Number</label>
                                            <div class="form-group mb-3">
                                                <input type="tel" class="form-control" placeholder="Number should start with 05 Eg: 05xxxxxxx" required style="height:50px;box-shadow: none!important;">
                                            </div>
                                            <div class="d-flex align-items-center justify-content-center my-3">
                                                <button type="button" class="btn btn-warning w-50 py-2 my-4">Check Balance</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane p-3" id="navpill-33" role="tabpanel">
                            <div class="row d-flex align-items-center">
                                <div class="col-lg-6 col-sm-12">
                                    <div class="container-box d-flex justify-content-start align-items-center flex-wrap pe-5">
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill  bg-warning" style="padding: 12px 0px; box-shadow: none!important;" rel="noreferrer">
                                            <img src="assets_frontend/images/salik.png" alt="Pay Loan EMI" style="width: 100px; height: 100px;">
                                            <!-- <span>Pay Loan EMI</span> -->
                                        </a>
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill  bg-light" style="padding: 12px 0px;" rel="noreferrer">
                                            <img src="assets_frontend/images/nol.png" alt="Pay Loan EMI" style="width: 100px; height: 100px;">
                                            <!-- <span>Pay Loan EMI</span> -->
                                        </a>
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill bg-light"  style="padding: 12px 0px;">
                                            <img src="assets_frontend/images/etisalat_direct.png" alt="Electricity Bill" style="width: 100px; height: 100px;">
                                            <!-- <span>Electricity Bill</span> -->
                                        </a>
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill  bg-light" style="padding: 12px 0px;" >
                                            <img src="assets_frontend/images/sewaPay.png" alt="Pay Loan EMI" style="width: 100px; height: 100px;border-radius: 49%;">
                                            <!-- <span>Pay Loan EMI</span> -->
                                        </a>
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill  bg-light" style="padding: 12px 0px;">
                                            <img src="assets_frontend/images/noqodi.png" alt="FASTag Recharge" style="width: 100px; height: 100px;">
                                        </a>
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill  bg-light" style="padding: 12px 0px;"  rel="noreferrer">
                                            <img src="assets_frontend/images/salik.png" alt="Pay Loan EMI" style="width: 100px; height: 100px;">
                                            <!-- <span>Pay Loan EMI</span> -->
                                        </a>
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill bg-light"  style="padding: 12px 0px;" rel="noreferrer">
                                            <img src="assets_frontend/images/sharjah_tahseel_topup.png" alt="Pay Loan EMI" style="width: 100px; height: 100px;border-radius: 49%;">
                                            <!-- <span>Pay Loan EMI</span> -->
                                        </a>
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill bg-light"  style="padding: 12px 0px;">
                                            <img src="assets_frontend/images/national_bond.png" alt="Mobile Recharge" style="width: 100px; height: 100px;">
                                        <!-- <span>Mobile Recharge</span> -->
                                        </a>
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill bg-light"  style="padding: 12px 0px;"  rel="noreferrer">
                                            <img src="assets_frontend/images/nol.png" alt="LIC / Insurance" style="width: 100px; height: 100px;">
                                        
                                        </a>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-12">
                                    <div class="card border border-0 bg-light" style="padding: 0px 12px;">
                                        <div class="row py-3 px-2" style="background-color: rgba(255 , 193 , 7 , 1); border-radius: 6px 6px 0px 0px;">
                                            <div class="col-lg-4 col-sm-12 d-flex justify-content-start align-items-center">
                                                <h2 class="sc-kOHTFB ezKFuM text-dark mb-0" style="font-size: 22px;">Salik</h2>
                                            </div>
                                            <div class="col-lg-8 col-sm-12 d-flex justify-content-end align-items-center">
                                                <button type="button" class="btn btn-warning py-2 px-4 border border-0" style="background-color: rgba(255 , 255 , 255, 0.3)!important;">The easiest way to drive around Dubai</button>
                                            </div>
                                        </div>
                                        <form class="mt-4 px-2" id="myForm">
                                            <label for="tel" class="text-dark">Account Number</label>
                                            <div class="form-group mb-3">
                                                <input type="tel" class="form-control" placeholder="Enter Account Number" required style="height:50px;box-shadow: none!important;">
                                            </div>
                                            <label for="tel" class="text-dark">Account Pin</label>
                                            <div class="form-group mb-3">
                                                <input type="password" class="form-control" placeholder="Enter Account Pin" required style="height:50px;box-shadow: none!important;">
                                            </div>

                                            <div class="d-flex align-items-center justify-content-center my-3">
                                                <button type="button" class="btn btn-warning w-50 py-2 my-4">Check Balance</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane p-3" id="navpill-44" role="tabpanel">
                            <div class="row d-flex align-items-center">
                                <div class="col-lg-6 col-sm-12">
                                    <div class="container-box d-flex justify-content-start align-items-center flex-wrap pe-5">
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill  bg-warning" style="padding: 12px 0px; box-shadow: none!important;" rel="noreferrer">
                                            <img src="assets_frontend/images/ding.png" alt="Pay Loan EMI" style="width: 100px; height: 100px;">
                                            <!-- <span>Pay Loan EMI</span> -->
                                        </a>
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill  bg-light" style="padding: 12px 0px;" rel="noreferrer">
                                            <img src="assets_frontend/images/fewa_direct.png" alt="Pay Loan EMI" style="width: 100px; height: 100px;">
                                            <!-- <span>Pay Loan EMI</span> -->
                                        </a>
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill bg-light"  style="padding: 12px 0px;">
                                            <img src="assets_frontend/images/etisalat_direct.png" alt="Electricity Bill" style="width: 100px; height: 100px;">
                                            <!-- <span>Electricity Bill</span> -->
                                        </a>
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill  bg-light" style="padding: 12px 0px;" >
                                            <img src="assets_frontend/images/sewaPay.png" alt="Pay Loan EMI" style="width: 100px; height: 100px;border-radius: 49%;">
                                            <!-- <span>Pay Loan EMI</span> -->
                                        </a>
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill  bg-light" style="padding: 12px 0px;">
                                            <img src="assets_frontend/images/noqodi.png" alt="FASTag Recharge" style="width: 100px; height: 100px;">
                                        </a>
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill  bg-light" style="padding: 12px 0px;"  rel="noreferrer">
                                            <img src="assets_frontend/images/salik.png" alt="Pay Loan EMI" style="width: 100px; height: 100px;">
                                            <!-- <span>Pay Loan EMI</span> -->
                                        </a>
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill bg-light"  style="padding: 12px 0px;" rel="noreferrer">
                                            <img src="assets_frontend/images/sharjah_tahseel_topup.png" alt="Pay Loan EMI" style="width: 100px; height: 100px;border-radius: 49%;">
                                            <!-- <span>Pay Loan EMI</span> -->
                                        </a>
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill bg-light"  style="padding: 12px 0px;">
                                            <img src="assets_frontend/images/national_bond.png" alt="Mobile Recharge" style="width: 100px; height: 100px;">
                                        <!-- <span>Mobile Recharge</span> -->
                                        </a>
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill bg-light"  style="padding: 12px 0px;"  rel="noreferrer">
                                            <img src="assets_frontend/images/nol.png" alt="LIC / Insurance" style="width: 100px; height: 100px;">
                                        
                                        </a>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-12">
                                    <div class="card border border-0 bg-light" style="padding: 0px 12px;">
                                        <div class="row py-3 px-2" style="background-color: rgba(255 , 193 , 7 , 1); border-radius: 6px 6px 0px 0px;">
                                            <div class="col-lg-6 col-sm-12 d-flex justify-content-start align-items-center">
                                                <h2 class="sc-kOHTFB ezKFuM text-dark mb-0" style="font-size: 22px;">International Recharge</h2>
                                            </div>
                                            <div class="col-lg-6 col-sm-12 d-flex justify-content-end align-items-center">
                                                <button type="button" class="btn btn-warning py-2 px-4 border border-0" style="background-color: rgba(255 , 255 , 255, 0.3)!important;">International Recharge</button>
                                            </div>
                                        </div>
                                        <form class="mt-4 px-2" id="myForm">
                                            <label for="tel" class="text-dark">Mobile number of the user</label>
                                            <div class="form-group mb-3">
                                                <input type="tel" class="form-control" placeholder="50 123 4567" required style="height:50px;box-shadow: none!important;">
                                            </div>
                                            <div class="d-flex align-items-center justify-content-center my-3">
                                                <button type="button" class="btn btn-warning w-50 py-2 my-4">Browse Plans</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane p-3" id="navpill-55" role="tabpanel">
                            <div class="row d-flex align-items-center">
                                <div class="col-lg-6 col-sm-12">
                                    <div class="container-box d-flex justify-content-start align-items-center flex-wrap pe-5">
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill  bg-warning" style="padding: 12px 0px; box-shadow: none!important;" rel="noreferrer">
                                            <img src="assets_frontend/images/etisalat_direct.png" alt="Pay Loan EMI" style="width: 100px; height: 100px;">
                                            <!-- <span>Pay Loan EMI</span> -->
                                        </a>
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill  bg-light" style="padding: 12px 0px;" rel="noreferrer">
                                            <img src="assets_frontend/images/fewa_direct.png" alt="Pay Loan EMI" style="width: 100px; height: 100px;">
                                            <!-- <span>Pay Loan EMI</span> -->
                                        </a>
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill  bg-light" style="padding: 12px 0px;" >
                                            <img src="assets_frontend/images/sewaPay.png" alt="Pay Loan EMI" style="width: 100px; height: 100px;border-radius: 49%;">
                                            <!-- <span>Pay Loan EMI</span> -->
                                        </a>
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill bg-light"  style="padding: 12px 0px;" rel="noreferrer">
                                            <img src="assets_frontend/images/sharjah_tahseel_topup.png" alt="Pay Loan EMI" style="width: 100px; height: 100px;border-radius: 49%;">
                                            <!-- <span>Pay Loan EMI</span> -->
                                        </a>
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill bg-light"  style="padding: 12px 0px;">
                                            <img src="assets_frontend/images/national_bond.png" alt="Mobile Recharge" style="width: 100px; height: 100px;">
                                        <!-- <span>Mobile Recharge</span> -->
                                        </a>
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill bg-light"  style="padding: 12px 0px;"  rel="noreferrer">
                                            <img src="assets_frontend/images/nol.png" alt="LIC / Insurance" style="width: 100px; height: 100px;">
                                        
                                        </a>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-12">
                                    <div class="card border border-0 bg-light" style="padding: 0px 12px;">
                                        <div class="row py-3 px-2" style="background-color: rgba(255 , 193 , 7 , 1); border-radius: 6px 6px 0px 0px;">
                                            <div class="col-lg-4 col-sm-12 d-flex justify-content-start align-items-center">
                                                <h2 class="sc-kOHTFB ezKFuM text-dark mb-0" style="font-size: 22px;">Etisalat</h2>
                                            </div>
                                            <div class="col-lg-8 col-sm-12 d-flex justify-content-end align-items-center">
                                                <button type="button" class="btn btn-warning py-2 px-4 border border-0" style="background-color: rgba(255 , 255 , 255, 0.3)!important;">Etisalat</button>
                                            </div>
                                        </div>
                                        <form class="mt-4 px-2" id="myForm">
                                            <label for="tel" class="text-dark my-3">Select a Service</label><br>
                                            <input type="radio" class="btn-check" name="options" id="option1" autocomplete="off">
                                            <label class="btn btn-outline-warning rounded-pill font-weight-medium text-dark px-4 my-1" for="option1">Postpaid GSM</label>

                                            <input type="radio" class="btn-check" name="options" id="option2" autocomplete="off">
                                            <label class="btn btn-outline-warning rounded-pill font-weight-medium text-dark px-4 my-1" for="option2">Landline Telephones</label>
                                            <input type="radio" class="btn-check" name="options" id="option1" autocomplete="off">
                                            <label class="btn btn-outline-warning rounded-pill font-weight-medium text-dark px-4 my-1" for="option3">Internet Dialup</label>

                                            <input type="radio" class="btn-check" name="options" id="option2" autocomplete="off">
                                            <label class="btn btn-outline-warning rounded-pill font-weight-medium text-dark px-4 my-1" for="option4">ALShamil</label>
                                            <input type="radio" class="btn-check" name="options" id="option1" autocomplete="off">
                                            <label class="btn btn-outline-warning rounded-pill font-weight-medium text-dark px-4 my-1" for="option5">eVision</label>

                                            <input type="radio" class="btn-check" name="options" id="option2" autocomplete="off">
                                            <label class="btn btn-outline-warning rounded-pill font-weight-medium text-dark px-4 my-1" for="option6">eLife</label>
                                            <input type="radio" class="btn-check" name="options" id="option1" autocomplete="off">
                                            <label class="btn btn-outline-warning rounded-pill font-weight-medium text-dark px-4 my-1" for="option7">Prepaid Wasel Recharge</label>

                                            <div class="form-group my-4">
                                                <input type="tel" class="form-control" placeholder="50 123 4567" required style="height:50px;box-shadow: none!important;">
                                            </div>
                                            <div class="d-flex align-items-center justify-content-center my-3">
                                                <button type="button" class="btn btn-warning w-50 py-2 my-4">Check Balance</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane p-3" id="navpill-66" role="tabpanel">
                            <div class="row d-flex align-items-center">
                                <div class="col-lg-6 col-sm-12">
                                    <div class="container-box d-flex justify-content-start align-items-center flex-wrap pe-5">
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill  bg-warning" style="padding: 12px 0px; box-shadow: none!important;" rel="noreferrer">
                                            <img src="assets_frontend/images/get_external_image (3).png" alt="Pay Loan EMI" style="width: 100px; height: 100px;">
                                            <!-- <span>Pay Loan EMI</span> -->
                                        </a>
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill  bg-light" style="padding: 12px 0px;" rel="noreferrer">
                                            <img src="assets_frontend/images/get_external_image (1).png" alt="Pay Loan EMI" style="width: 100px; height: 100px;">
                                            <!-- <span>Pay Loan EMI</span> -->
                                        </a>
                                        <a href="http://185.188.127.32/index.php/login" target="_blank" class="container-box-menu mb-3 me-4 reacharge-bill bg-light"  style="padding: 12px 0px;">
                                            <img src="assets_frontend/images/mint_route.png" alt="Electricity Bill" style="width: 100px; height: 100px;">
                                            <!-- <span>Electricity Bill</span> -->
                                        </a>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-12">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <div class="brand_img">
                                            <img src="assets_frontend/images/get_external_image (4).png" style="width: 100%;height: auto;">
                                        </div>
                                        <div class="brand_img">
                                            <img src="assets_frontend/images/get_external_image (5).png" style="width: 100%;height: auto;">
                                        </div>
                                        <div class="brand_img">
                                            <img src="assets_frontend/images/get_external_image (6).png" style="width: 100%;height: auto;">
                                        </div>
                                        <div class="brand_img">
                                            <img src="assets_frontend/images/get_external_image (7).png" style="width: 100%;height: auto;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
    </div>
    <!-- tab area end -->

    
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