import { el } from './dom.js';
import { api } from '../api/client.js';
import { showToast } from './toast.js';

export function openPasswordOtpModal({ challenge, newPassword, onSuccess }) {
  let modal = null;

  const close = () => {
    if (modal) {
      modal.remove();
      modal = null;
    }
    document.removeEventListener('keydown', handleEsc);
  };

  const handleEsc = (e) => {
    if (e.key === 'Escape') close();
  };
  document.addEventListener('keydown', handleEsc);

  const errorAlert = el('div', {
    style: 'display: none; padding: 0.6rem 0.8rem; border-radius: 4px; background: #ffebee; color: var(--error); font-size: 12px; margin-bottom: 0.75rem;',
  });

  const otpInput = el('input', {
    type: 'text',
    class: 'input',
    placeholder: 'Nhập mã 6 chữ số...',
    maxLength: 6,
    required: true,
    style: 'letter-spacing: 6px; font-size: 18px; font-weight: 700; text-align: center; height: 42px; margin-bottom: 1rem;',
  });

  const submitBtn = el('button', {
    type: 'submit',
    class: 'btn btn-primary',
    style: 'flex: 1; height: 40px; font-weight: 700;',
  }, 'Xác nhận đổi');

  const cancelBtn = el('button', {
    type: 'button',
    class: 'btn btn-ghost',
    style: 'height: 40px; font-size: 13px;',
    onClick: close,
  }, 'Hủy');

  const resendBtn = el('button', {
    type: 'button',
    class: 'btn btn-ghost btn-sm',
    style: 'font-size: 12px; color: var(--primary); align-self: center;',
    onClick: async () => {
      try {
        resendBtn.disabled = true;
        await api.post('/citizen/password/resend', { challenge_id: challenge.challenge_id });
        showToast.success('Mã OTP mới đã được gửi vào email.');
      } catch (e) {
        showToast.error(e?.data?.message || e?.message || 'Không thể gửi lại OTP.');
      } finally {
        setTimeout(() => { resendBtn.disabled = false; }, 30000);
      }
    },
  }, 'Gửi lại mã OTP');

  const form = el('form', {
    style: 'display: flex; flex-direction: column;',
    onSubmit: async (e) => {
      e.preventDefault();
      errorAlert.style.display = 'none';
      const code = otpInput.value.trim();
      if (!code || code.length !== 6) return showToast.error('Vui lòng nhập đủ 6 chữ số OTP.');

      submitBtn.disabled = true;
      submitBtn.textContent = 'Đang xử lý...';
      try {
        await api.post('/citizen/password/verify', {
          challenge_id: challenge.challenge_id,
          code,
          new_password: newPassword,
          new_password_confirmation: newPassword,
        });
        close();
        if (typeof onSuccess === 'function') onSuccess();
      } catch (err) {
        const msg = err?.data?.message || err?.message || 'Mã OTP không hợp lệ hoặc đã hết hạn.';
        errorAlert.textContent = msg;
        errorAlert.style.display = 'block';
        showToast.error(msg);
      } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Xác nhận đổi';
      }
    },
  }, [
    errorAlert,
    otpInput,
    el('div', { style: 'display: flex; gap: 0.5rem; margin-bottom: 0.75rem;' }, [cancelBtn, submitBtn]),
    resendBtn,
  ]);

  const modalBox = el('div', {
    class: 'card',
    style: 'position: relative; width: 100%; max-width: 400px; background: #ffffff; border-radius: 8px; padding: 1.75rem; box-shadow: var(--shadow-modal); z-index: 1001;',
    role: 'dialog',
    'aria-modal': 'true',
  }, [
    el('h3', { style: 'font-size: 16px; font-weight: 700; color: #004482; margin: 0 0 0.5rem;' }, 'Xác thực OTP đổi mật khẩu'),
    el('p', { style: 'font-size: 13px; color: #64748b; margin: 0 0 1rem;' }, `Mã OTP đã được gửi tới ${challenge.masked_email || 'email của bạn'}. Vui lòng nhập mã để hoàn tất.`),
    form,
  ]);

  modal = el('div', {
    style: 'position: fixed; inset: 0; background: rgba(18, 48, 74, 0.45); display: flex; align-items: center; justify-content: center; padding: 1rem; z-index: 1000;',
    onClick: (e) => {
      if (e.target === modal) close();
    },
  }, modalBox);

  document.body.appendChild(modal);
  otpInput.focus();
}
