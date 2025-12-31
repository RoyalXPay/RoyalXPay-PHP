<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>AI Chat - RoyalXPay</title>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <style>
    body { font-family: Arial, sans-serif; background:#002B5B; color:white; margin:0; padding:0; display:flex; justify-content:center; align-items:center; height:100vh; }
    #chatBoxAI {
      width: 100%;
      max-width: 420px;
      background: #002B5B;
      border: 2px solid white;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.3);
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }
    #chatHeader {
      background: #008CCE;
      padding: 10px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-top-left-radius:10px;
      border-top-right-radius:10px;
    }
    #chatMessagesAI { flex:1; overflow-y:auto; padding:15px; background:#f4f4f4; color:#000; display:flex; flex-direction:column; gap:8px; }
    .bubble { max-width:80%; padding:8px 12px; border-radius:16px; line-height:1.4; word-wrap:break-word; }
    .left { background:#e5e5ea; color:#000; align-self:flex-start; }
    .right { background:#007aff; color:#fff; align-self:flex-end; text-align:right; }
    #chatInputBoxAI { width: calc(95% - 20px); margin:10px; padding:10px; border:none; border-radius:20px; }
    .close-btn { cursor:pointer; font-size:14px; background:red; color:white; width:25px; height:25px; display:flex; align-items:center; justify-content:center; border-radius:50%; }
  </style>
</head>
<body>
  <div id="chatBoxAI">
    <div id="chatHeader">
      <span><img src="assets_frontend/images/robot.png" style="width:30px;"> AI Assistant</span>
      <span class="close-btn" onclick="window.close()">×</span>
    </div>
    <div id="chatMessagesAI"></div>
    <input type="text" id="chatInputBoxAI" placeholder="Type your message..." onkeypress="chatHandleKeyAI(event)">
  </div>

  <script>
  (function(){
    const CLIENT_CODE = "CLTTEST001";
    const TOKEN_ENDPOINT = "https://royalxpay.com/proxy/token";

    let accessToken = null;
    let ws = null;
    let wsConnected = false;
    let pendingSendQueue = [];
    let aiBubbleEl = null;
    let aiBuffer = "";
    let messageCompleted = false;
    let finalizeTimeout;

    // Append chat bubble
    function appendChatBubbleAI(text, side="left") {
      const chatArea = document.getElementById('chatMessagesAI');
      const bubble = document.createElement('div');
      bubble.classList.add('bubble', side);
      bubble.textContent = text;
      chatArea.appendChild(bubble);
      chatArea.scrollTop = chatArea.scrollHeight;
    }

    async function fetchAccessTokenAI() {
      try {
        const res = await axios.post(TOKEN_ENDPOINT, { client_code: CLIENT_CODE }, {
          headers: { 'Content-Type': 'application/json' }
        });
        accessToken = res.data.access_token || res.data.token;
        if (!accessToken) throw new Error("No token received");
      } catch (err) {
        appendChatBubbleAI("❌ Failed to get token.", "left");
        throw err;
      }
    }

    async function ensureTokenAndSocketAI() {
      if (!accessToken) await fetchAccessTokenAI();
      if (wsConnected && ws && ws.readyState === WebSocket.OPEN) return;
      return connectWebSocketAI(accessToken);
    }

    function connectWebSocketAI(token) {
      return new Promise((resolve, reject) => {
        const wsUrl = `wss://interactivebyskiphi.skiphi.com/ws/ai-chat/?token=${encodeURIComponent(token)}&clientCode=${CLIENT_CODE}`;
        const socket = new WebSocket(wsUrl);

        socket.onopen = () => {
          ws = socket;
          wsConnected = true;
          appendChatBubbleAI("🔗 Connected to AI assistant.", "left");
          while (pendingSendQueue.length) {
            ws.send(JSON.stringify(pendingSendQueue.shift()));
          }
          resolve();
        };

        socket.onmessage = (ev) => {
          let parsed;
          try { parsed = JSON.parse(ev.data); } catch(e){ return; }

          if (parsed.status && parsed.status.includes("completed")) {
            messageCompleted = true;
            finalizeAIBubble();
            return;
          }

          if (parsed.message) {
            if (!aiBubbleEl) aiBubbleEl = createAIBubble();
            aiBuffer += parsed.message;
            updateAIBubble(aiBuffer);
            resetFinalizeTimeout();
          }
        };

        socket.onerror = () => {
          wsConnected = false;
          appendChatBubbleAI("❌ WebSocket connection failed.", "left");
          reject();
        };

        socket.onclose = () => {
          wsConnected = false;
          aiBubbleEl = null;
          aiBuffer = "";
          messageCompleted = false;
          clearTimeout(finalizeTimeout);
        };
      });
    }

    function sendMessageToAI(text) {
      const msg = { text, sender: "user", time: new Date().toISOString() };
      messageCompleted = false;
      resetFinalizeTimeout();
      if (ws && ws.readyState === WebSocket.OPEN) {
        aiBubbleEl = createAIBubble();
        aiBuffer = "";
        ws.send(JSON.stringify(msg));
      } else {
        pendingSendQueue.push(msg);
        ensureTokenAndSocketAI();
        aiBubbleEl = createAIBubble();
        aiBuffer = "";
      }
    }

    function createAIBubble() {
      const chatArea = document.getElementById('chatMessagesAI');
      const bubble = document.createElement('div');
      bubble.classList.add('bubble', 'left');
      bubble.textContent = "💬 ...";
      chatArea.appendChild(bubble);
      chatArea.scrollTop = chatArea.scrollHeight;
      return bubble;
    }

    function updateAIBubble(text) {
      if (aiBubbleEl) aiBubbleEl.textContent = text;
      document.getElementById('chatMessagesAI').scrollTop = document.getElementById('chatMessagesAI').scrollHeight;
    }

    function finalizeAIBubble() {
      if (aiBubbleEl) aiBubbleEl.textContent = aiBuffer || "🤖 (no reply)";
      aiBubbleEl = null;
      aiBuffer = "";
      clearTimeout(finalizeTimeout);
    }
  function resetFinalizeTimeout() {
    clearTimeout(finalizeTimeout);
    finalizeTimeout = setTimeout(() => {
      if (!messageCompleted) {
        console.log("⏰ No response received, finalizing bubble.");
        finalizeAIBubble();
        appendChatBubbleAI("⚠️ No response from server, please try again.", "left");
      }
    }, 7000); 
  }

    window.openChatBoxAI = function() {
    document.getElementById('chatBoxAI').style.display = 'block';
    const chatArea = document.getElementById('chatMessagesAI');
    chatArea.innerHTML = '';
    appendChatBubbleAI("👋 Hi there! I am your assistant.", "left");
    appendChatBubbleAI("How can I help you today?", "left");
    ensureTokenAndSocketAI().catch(() => {
      appendChatBubbleAI("❌ Unable to connect. Please try later.", "left");
    });
  };

    // Input handling
    window.chatHandleKeyAI = function(e) {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        const input = e.target;
        const text = input.value.trim();
        if (!text) return;
        appendChatBubbleAI("🧑 " + text, "right");
        sendMessageToAI(text);
        input.value = "";
      }
    };

    // Initialize chat
    (async function initChat(){
      appendChatBubbleAI("👋 Hi there! I am your assistant.", "left");
      appendChatBubbleAI("How can I help you today?", "left");
      await ensureTokenAndSocketAI();
    })();
  })();
  </script>
</body>
</html>
