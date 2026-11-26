<div id="obe-chatbot" class="chatbot-wrapper">
    <div id="chat-window" class="chat-window" style="display: none;">
        <div class="chat-header">
            <span><i class="fas fa-robot"></i> Asisten Kurikulum UKRI</span>
            <button onclick="toggleChat()" class="close-chat">&times;</button>
        </div>
        <div id="chat-body" class="chat-body">
            <div class="msg bot">Halo {{ auth()->user()->name ?? 'User' }}! Ada yang bisa saya bantu terkait kurikulum OBE?</div>
        </div>
        <div class="chat-footer">
            <input type="text" id="chat-input" placeholder="Tanya tentang CPL/CPMK...">
            <button id="chat-send"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>
    <button class="chat-toggle-btn" onclick="toggleChat()">
        <i class="fas fa-comments"></i>
    </button>
</div>

<style>
    .chatbot-wrapper { position: fixed; bottom: 30px; right: 30px; z-index: 9999; font-family: 'Inter', sans-serif; }
    .chat-toggle-btn { width: 60px; height: 60px; border-radius: 50%; background: #F8B803; color: #1b1b18; border: none; cursor: pointer; font-size: 24px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); transition: transform 0.3s; }
    .chat-toggle-btn:hover { transform: scale(1.1); }
    .chat-window { width: 350px; height: 500px; background: white; border-radius: 15px; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 10px 30px rgba(0,0,0,0.2); margin-bottom: 20px; border: 1px solid #e3e3e0; }
    .chat-header { background: #1b1b18; color: #F8B803; padding: 15px; display: flex; justify-content: space-between; align-items: center; font-weight: bold; }
    .close-chat { background: none; border: none; color: white; font-size: 24px; cursor: pointer; }
    .chat-body { flex: 1; padding: 15px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px; background: #fdfdfc; }
    .msg { padding: 10px 14px; border-radius: 12px; max-width: 85%; font-size: 14px; line-height: 1.5; }
    .msg.bot { background: #e3e3e0; align-self: flex-start; color: #1b1b18; }
    .msg.user { background: #F8B803; align-self: flex-end; color: #1b1b18; font-weight: 500; }
    .chat-footer { padding: 12px; border-top: 1px solid #eee; display: flex; gap: 8px; }
    .chat-footer input { flex: 1; border: 1px solid #ddd; padding: 8px 12px; border-radius: 8px; outline: none; }
    .chat-footer button { background: #1b1b18; color: #F8B803; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer; }
</style>

<script>
    function toggleChat() {
        const win = document.getElementById('chat-window');
        win.style.display = win.style.display === 'none' ? 'flex' : 'none';
    }

    document.getElementById('chat-send').addEventListener('click', sendMsg);
    document.getElementById('chat-input').addEventListener('keypress', (e) => { if(e.key === 'Enter') sendMsg(); });

    async function sendMsg() {
        const input = document.getElementById('chat-input');
        const body = document.getElementById('chat-body');
        const msg = input.value.trim();
        if(!msg) return;

        appendMsg('user', msg);
        input.value = '';

        const loadingId = 'bot-' + Date.now();
        appendMsg('bot', '...', loadingId);

        try {
            const response = await fetch('{{ route("chatbot.chat") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message: msg })
            });
            const data = await response.json();
            document.getElementById(loadingId).innerText = data.answer || 'Maaf, jawaban AI Agent kosong.';
        } catch (e) {
            document.getElementById(loadingId).innerText = 'Error: Gagal menghubungi server.';
        }
        body.scrollTop = body.scrollHeight;
    }

    function appendMsg(type, text, id = '') {
        const body = document.getElementById('chat-body');
        const div = document.createElement('div');
        div.className = 'msg ' + type;
        if(id) div.id = id;
        div.innerText = text;
        body.appendChild(div);
        body.scrollTop = body.scrollHeight;
    }
</script>
