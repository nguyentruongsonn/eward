import { el } from './dom.js';
import { api } from '../api/client.js';

export const CHAT_AI_ENABLED = false;

export function renderChatAiWidget() {
  if (!CHAT_AI_ENABLED) {
    return el('div', { id: 'eward-chat-ai-root', style: 'display: none;' });
  }

  let isOpen = false;
  let isLoading = false;
  const STORAGE_KEY = 'eward_ai_chat_history';

  const defaultWelcome = {
    role: 'assistant',
    text: 'Xin chào! Tôi là Trợ lý số hỗ trợ dịch vụ công cấp cơ sở. Tôi có thể giải đáp thông tin về quy trình, hồ sơ, thời hạn và lệ phí các thủ tục hành chính. Bạn cần hỗ trợ thủ tục nào?',
  };

  let messages = [defaultWelcome];
  try {
    const saved = localStorage.getItem(STORAGE_KEY);
    if (saved) {
      const parsed = JSON.parse(saved);
      if (Array.isArray(parsed) && parsed.length > 0) messages = parsed;
    }
  } catch (_) {}

  const saveMessages = () => {
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(messages.slice(-20)));
    } catch (_) {}
  };

  const container = el('div', { id: 'eward-chat-ai-root', style: 'position: fixed; bottom: 20px; right: 20px; z-index: 9999; font-family: var(--font-body);' });

  const toggleBtn = el('button', {
    type: 'button',
    style: 'background: #004482; color: #ffffff; border: none; border-radius: 24px; padding: 0.65rem 1.15rem; font-size: 13px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 14px rgba(0,0,0,0.2); transition: transform 0.15s, background 0.15s; display: flex; align-items: center; gap: 0.4rem;',
    onClick: () => toggleChat(),
  }, [
    el('span', { style: 'display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #22c55e;' }),
    'Trợ lý ảo AI',
  ]);

  const messagesBox = el('div', {
    style: 'flex: 1; overflow-y: auto; padding: 1rem; display: flex; flex-direction: column; gap: 0.75rem; background: #f8fafc;',
  });

  const inputEl = el('input', {
    type: 'text',
    placeholder: 'Nhập câu hỏi thủ tục (ví dụ: Làm giấy khai sinh cần gì)...',
    style: 'flex: 1; height: 38px; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0 0.75rem; font-size: 12.5px; outline: none;',
    onKeydown: (e) => {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
      }
    },
  });

  const sendBtn = el('button', {
    type: 'button',
    style: 'height: 38px; padding: 0 1rem; background: #004482; color: #ffffff; border: none; border-radius: 4px; font-size: 12.5px; font-weight: 700; cursor: pointer;',
    onClick: () => sendMessage(),
  }, 'Gửi');

  const scrollToBottom = () => {
    setTimeout(() => {
      messagesBox.scrollTop = messagesBox.scrollHeight;
    }, 50);
  };

  function renderMessages() {
    const nodes = messages.map(m => {
      const isUser = m.role === 'user';
      return el('div', {
        style: `max-width: 86%; align-self: ${isUser ? 'flex-end' : 'flex-start'}; padding: 0.65rem 0.85rem; border-radius: 4px; font-size: 12.5px; line-height: 1.5; white-space: pre-wrap; ${isUser ? 'background: #004482; color: #ffffff;' : 'background: #ffffff; color: #0f172a; border: 1px solid #d0d7de;'}`,
      }, m.text);
    });

    if (isLoading) {
      nodes.push(el('div', {
        style: 'max-width: 86%; align-self: flex-start; padding: 0.5rem 0.85rem; border-radius: 4px; font-size: 12px; color: #64748b; background: #ffffff; border: 1px solid #d0d7de; font-style: italic;',
      }, 'Trợ lý AI đang tra cứu quy định và soạn trả lời...'));
    }

    messagesBox.replaceChildren(...nodes);
    scrollToBottom();
  }

  async function sendMessage(textToSend) {
    const text = (textToSend || inputEl.value).trim();
    if (!text || isLoading) return;

    messages.push({ role: 'user', text });
    inputEl.value = '';
    isLoading = true;
    renderMessages();
    saveMessages();

    try {
      const res = await api.post('/public/chat', { message: text });
      const reply = res?.data?.reply || res?.reply || 'Dịch vụ chưa phản hồi câu hỏi này.';
      messages.push({ role: 'assistant', text: reply });
    } catch (err) {
      messages.push({
        role: 'assistant',
        text: 'Hiện dịch vụ tư vấn AI đang bận hoặc gián đoạn. Bạn vui lòng tra cứu trực tiếp thông tin tại mục "Thủ tục hành chính" hoặc liên hệ tổng đài hỗ trợ.',
      });
    } finally {
      isLoading = false;
      renderMessages();
      saveMessages();
    }
  }

  const suggestions = [
    'Thủ tục khai sinh',
    'Đăng ký kết hôn',
    'Cấp phép xây dựng',
    'Chứng thực bản sao',
  ];

  const suggestionsRow = el('div', {
    style: 'display: flex; gap: 0.35rem; padding: 0.4rem 0.75rem; background: #ffffff; border-top: 1px solid #f1f5f9; flex-wrap: wrap;',
  }, suggestions.map(s => el('button', {
    type: 'button',
    style: 'font-size: 11px; padding: 0.2rem 0.5rem; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 3px; color: #004482; cursor: pointer;',
    onClick: () => sendMessage(s),
  }, s)));

  const chatHeader = el('div', {
    style: 'background: #004482; color: #ffffff; padding: 0.75rem 1rem; border-radius: 6px 6px 0 0; display: flex; justify-content: space-between; align-items: center;',
  }, [
    el('div', {}, [
      el('div', { style: 'font-weight: 700; font-size: 13.5px;' }, 'Trợ lý số giải đáp TTHC'),
      el('div', { style: 'font-size: 11px; color: rgba(255,255,255,0.8);' }, 'Hệ thống tư vấn tự động trực tuyến'),
    ]),
    el('div', { style: 'display: flex; gap: 0.5rem; align-items: center;' }, [
      el('button', {
        type: 'button',
        title: 'Xóa lịch sử chat',
        style: 'background: transparent; border: none; color: rgba(255,255,255,0.8); font-size: 11px; cursor: pointer; text-decoration: underline;',
        onClick: () => {
          messages = [defaultWelcome];
          saveMessages();
          renderMessages();
        },
      }, 'Xóa lịch sử'),
      el('button', {
        type: 'button',
        title: 'Đóng cửa sổ',
        style: 'background: transparent; border: none; color: #ffffff; font-size: 16px; font-weight: 700; cursor: pointer; line-height: 1;',
        onClick: () => toggleChat(),
      }, '✕'),
    ]),
  ]);

  const inputBar = el('div', {
    style: 'padding: 0.6rem 0.75rem; background: #ffffff; border-top: 1px solid #e2e8f0; display: flex; gap: 0.5rem; align-items: center; border-radius: 0 0 6px 6px;',
  }, inputEl, sendBtn);

  const chatWindow = el('div', {
    style: 'display: none; width: 380px; max-width: calc(100vw - 32px); height: 500px; max-height: calc(100vh - 100px); background: #ffffff; border: 1px solid #d0d7de; border-radius: 6px; box-shadow: 0 8px 30px rgba(0,0,0,0.18); flex-direction: column; overflow: hidden; margin-bottom: 0.5rem;',
  }, chatHeader, messagesBox, suggestionsRow, inputBar);

  function toggleChat() {
    isOpen = !isOpen;
    chatWindow.style.display = isOpen ? 'flex' : 'none';
    toggleBtn.style.display = isOpen ? 'none' : 'flex';
    if (isOpen) {
      renderMessages();
      inputEl.focus();
    }
  }

  container.append(chatWindow, toggleBtn);
  return container;
}
