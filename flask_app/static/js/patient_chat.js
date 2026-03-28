// patient_chat.js
document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();

    if (!window.CHAT_CONFIG || !window.CHAT_CONFIG.userId) return;

    const API_BASE = `${window.location.protocol}//${window.location.hostname}:8001/api`;
    const TOKEN = window.CHAT_CONFIG.token;
    const USER_ID = window.CHAT_CONFIG.userId;
    const HEADERS = {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${TOKEN}`
    };

    const chatListEl = document.getElementById('chat-list');
    const noChatsMsg = document.getElementById('no-chats-msg');
    const chatBoxContainer = document.getElementById('chat-box-container');
    const chatMessagesEl = document.getElementById('chat-messages');
    const chatInput = document.getElementById('chat-input');
    const sendBtn = document.getElementById('send-btn');
    const activeChatName = document.getElementById('active-chat-name');
    const backBtn = document.getElementById('back-btn');

    let currentChatId = null;
    let autoUpdateInterval = null;
    let activeMedicoId = null;

    // Toast Container
    const toastContainer = document.createElement('div');
    toastContainer.className = 'fixed top-4 right-4 z-[9999] flex flex-col gap-2';
    document.body.appendChild(toastContainer);

    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'bg-pink-primary text-white px-4 py-3 rounded-xl shadow-lg flex items-center gap-3 transition-opacity duration-300';
        toast.innerHTML = `<i data-lucide="bell" class="w-5 h-5"></i><p class="text-sm font-bold">${escapeHtml(message)}</p>`;
        toastContainer.appendChild(toast);
        lucide.createIcons();
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }

    async function checkNotifications() {
        try {
            const res = await fetch(`${API_BASE}/notificaciones/${USER_ID}`, { headers: HEADERS });
            if (!res.ok) return;
            const notifs = await res.json();
            
            const unread = notifs.filter(n => !n.leido);
            for (const n of unread) {
                showToast(n.mensaje);
                await fetch(`${API_BASE}/notificaciones/${n.id_notificacion}/leer`, {
                    method: 'PATCH',
                    headers: HEADERS
                });
            }
        } catch (e) {
            console.error('Error cargando notificaciones:', e);
        }
    }

    setInterval(checkNotifications, 5000);
    checkNotifications();

    async function loadChats() {
        try {
            const res = await fetch(`${API_BASE}/chats/usuario/${USER_ID}`, { headers: HEADERS });
            if (!res.ok) return;
            const chats = await res.json();
            
            chatListEl.innerHTML = '';
            
            if (chats.length === 0) {
                noChatsMsg.style.display = 'block';
                chatListEl.appendChild(noChatsMsg);
                return;
            }

            // Para cada chat, obtener el info del médico para mostrar el nombre
            const medicosRes = await fetch(`${API_BASE}/medicos/con-nombre?solo_activos=false`, { headers: HEADERS });
            const medicos = medicosRes.ok ? await medicosRes.json() : [];

            chats.forEach(chat => {
                const medico = medicos.find(m => m.id_medico === chat.id_medico);
                const docName = medico ? medico.nombre_completo : `Médico #${chat.id_medico}`;

                const div = document.createElement('div');
                const isActive = currentChatId === chat.id_chat;
                div.className = `flex items-center gap-3 p-3 rounded-xl cursor-pointer transition-colors ${isActive ? 'bg-pink-50' : 'hover:bg-gray-50'}`;
                
                div.innerHTML = `
                    <div class="relative w-10 h-10 flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-white border border-pink-100 text-purple-700 flex items-center justify-center">
                            <i data-lucide="stethoscope" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-xs font-bold ${isActive ? 'text-pink-primary' : 'text-gray-800'} flex justify-between">${docName}</h4>
                        <p class="text-[10px] text-gray-500 truncate font-semibold mt-0.5">${chat.estatus === 'restringido' ? 'Chat bloqueado' : 'Toca para ver mensajes'}</p>
                    </div>
                `;
                
                div.onclick = () => selectChat(chat.id_chat, chat.id_medico, docName, chat.estatus);
                chatListEl.appendChild(div);
            });
            lucide.createIcons();

        } catch (e) {
            console.error('Error cargando chats:', e);
        }
    }

    async function selectChat(chatId, medicoId, docName, estatus) {
        currentChatId = chatId;
        activeMedicoId = medicoId;
        activeChatName.textContent = docName;
        
        chatBoxContainer.style.display = 'flex';
        
        // Mobile view logic
        if (window.innerWidth < 1024) {
            chatListEl.parentElement.style.display = 'none';
        }

        if (estatus === 'restringido') {
            chatInput.disabled = true;
            sendBtn.disabled = true;
            chatInput.placeholder = "Este chat ha sido restringido.";
        } else {
            chatInput.disabled = false;
            sendBtn.disabled = false;
            chatInput.placeholder = "Escribe tu mensaje...";
        }

        await loadMessages();
        
        // Set up polling for messages
        if (autoUpdateInterval) clearInterval(autoUpdateInterval);
        autoUpdateInterval = setInterval(loadMessages, 3000);
        
        // Reload list to highlight selected chat
        loadChats();
    }

    async function loadMessages() {
        if (!currentChatId) return;
        try {
            const res = await fetch(`${API_BASE}/chats/${currentChatId}/mensajes`, { headers: HEADERS });
            if (!res.ok) return;
            const mensajes = await res.json();
            
            chatMessagesEl.innerHTML = '';
            mensajes.forEach(msg => {
                const asPatient = msg.emisor === 'usuario';
                const timeStr = new Date(msg.fecha_envio).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                
                const wrapper = document.createElement('div');
                if (asPatient) {
                    wrapper.className = "flex flex-col items-end gap-1 max-w-[80%] self-end ml-auto";
                    wrapper.innerHTML = `
                        <div class="px-5 py-3 bg-pink-primary text-white rounded-2xl rounded-tr-sm text-xs font-bold shadow-sm leading-relaxed whitespace-pre-wrap">${escapeHtml(msg.mensaje)}</div>
                        <span class="text-[9px] text-gray-400 font-bold mr-1">${timeStr}</span>
                    `;
                } else {
                    wrapper.className = "flex flex-col items-start gap-1 max-w-[80%]";
                    wrapper.innerHTML = `
                        <div class="px-5 py-3 bg-gray-100 rounded-2xl rounded-tl-sm text-xs text-gray-800 font-semibold shadow-sm leading-relaxed whitespace-pre-wrap">${escapeHtml(msg.mensaje)}</div>
                        <span class="text-[9px] text-gray-400 font-bold ml-1">${timeStr}</span>
                    `;
                }
                chatMessagesEl.appendChild(wrapper);
            });
            
            // Scroll to bottom
            chatMessagesEl.scrollTop = chatMessagesEl.scrollHeight;
        } catch (e) {
            console.error('Error cargando mensajes:', e);
        }
    }

    async function sendMessage() {
        if (!currentChatId || chatInput.disabled) return;
        const text = chatInput.value.trim();
        if (!text) return;
        
        try {
            const res = await fetch(`${API_BASE}/mensajes`, {
                method: 'POST',
                headers: HEADERS,
                body: JSON.stringify({
                    id_chat: currentChatId,
                    emisor: 'usuario',
                    mensaje: text
                })
            });
            if (res.ok) {
                chatInput.value = '';
                loadMessages();
            } else {
                const data = await res.json();
                alert(data.detail || "Error al enviar el mensaje.");
            }
        } catch (e) {
            console.error('Error enviando mensaje:', e);
        }
    }

    sendBtn.addEventListener('click', sendMessage);
    chatInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendMessage();
    });

    if (backBtn) {
        backBtn.addEventListener('click', () => {
            chatBoxContainer.style.display = 'none';
            chatListEl.parentElement.style.display = 'flex';
            currentChatId = null;
            if (autoUpdateInterval) clearInterval(autoUpdateInterval);
            loadChats();
        });
    }

    function escapeHtml(unsafe) {
        return unsafe.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    // Inicializar
    loadChats();
});
