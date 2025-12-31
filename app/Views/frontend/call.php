<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>AI Call Assistant - RoyalXPay</title>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #002B5B;
      color: white;
      margin: 0;
      padding: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    #chatBox {
      width: 100%;
            max-width: 420px;

      background: #002B5B;
      border: 2px solid white;
      border-radius: 10px;
      display: flex;
      flex-direction: column;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }
    #chatHeader {
      background: #008CCE;
      padding: 10px 15px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-top-left-radius: 10px;
      border-top-right-radius: 10px;
    }
    #chatMessages {
      flex: 1;
      overflow-y: auto;
      background: #f4f4f4;
      color: #000;
      padding: 15px;
      display: flex;
      flex-direction: column;
      gap: 8px;
    }
    .bubble {
      max-width: 80%;
      padding: 8px 12px;
      border-radius: 16px;
      line-height: 1.4;
      word-wrap: break-word;
    }
    .left { background: #e5e5ea; color: #000; align-self: flex-start; }
    .right { background: #007aff; color: #fff; align-self: flex-end; text-align: right; }
    #chatInput {
      width: calc(95% - 30px);
      margin: 10px 15px 15px;
      padding: 10px;
      border: none;
      border-radius: 20px;
      font-size: 14px;
    }
    .close-btn {
      cursor: pointer;
      font-size: 14px;
      background: red;
      color: white;
      width: 25px;
      height: 25px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
    }
  </style>
</head>
<body>

  <div id="chatBox">
    <div id="chatHeader">
      <span><img src="assets_frontend/images/robot.png" style="width:30px;"> AI Call Assistant</span>
      <span class="close-btn" onclick="window.close()">×</span>
    </div>
    <div id="chatMessages"></div>
    <input type="text" id="chatInput" placeholder="Enter your mobile number..." onkeypress="handleKeyPress(event)">
  </div>

  <script>
   
    window.onload = function() {
      const chatArea = document.getElementById('chatMessages');
      const messages = [
        "👋 Hi there! I'm your call assistant.",
        "Please enter your 10-digit mobile number 📱"
      ];
      let i = 0;
      const interval = setInterval(() => {
        if (i < messages.length) {
          appendChatBubble(messages[i], "left");
          i++;
        } else {
          clearInterval(interval);
        }
      }, 800);
    };

    // Append message bubble
    function appendChatBubble(text, side="left") {
      const chatArea = document.getElementById('chatMessages');
      const bubble = document.createElement('div');
      bubble.classList.add('bubble', side);
      bubble.textContent = text;
      chatArea.appendChild(bubble);
      chatArea.scrollTop = chatArea.scrollHeight;
    }

    // Handle enter press
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

    // API call function
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
          specialNotes: "User submitted from call assistant",
          location: "N/A",
          email: "",
          language: "English"
        }
      };

      appendChatBubble("📞 Connecting your call... Please wait.", "left");

      axios.post('https://api-eaicall.eaicall.com/api/ashtask/initiate/', body, { headers })
        .then(res => {
          if (res.data.status === "success") {
            appendChatBubble("✅ Call created successfully! Task ID: " + res.data.taskId, "left");
          } else {
            appendChatBubble("❌ Error: " + res.data.message, "left");
          }
        })
        .catch(err => {
          appendChatBubble("❌ Failed to connect. Please try again later.", "left");
          console.error(err);
        });
    }
  </script>

</body>
</html>
