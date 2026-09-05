import { el } from './dom.js';
import { api } from '../api/client.js';
import { showToast } from './toast.js';

export function createAuthOtpForm({ onBack, onSuccess, onError }) {
  let context = null;

  const title = el('h3', { style: 'font-size: 16px; font-weight: 700; color: var(--primary); margin: 0 0 0.35rem;' }, 'Xác thực mã OTP');
  const desc = el('p', { style: 'font-size: 13px; color: #64748b; margin: 0 0 1rem;' }, '');
  const codeInput = el('input', {
    type: 'text',
    class: 'input',
    placeholder: 'Nhập mã 6 chữ số...',
    maxLength: 6,
    required: true,
    style: 'letter-spacing: 6px; font-size: 18px; font-weight: 700; text-align: center;',
  });
  const submitBtn = el('button', { type: 'submit', class: 'btn btn-primary', style: 'width: 100%; height: 42px;' }, 'Xác nhận OTP');
  const resendBtn = el('button', {
    type: 'button',
    class: 'btn btn-ghost btn-sm',
    style: 'font-size: 12px; color: var(--primary);',
    onClick: async () => {
      if (!context?.email) return;
      try {
        resendBtn.disabled = true;
        const endpoint = context.flow === 'login' ? '/auth/resend-login-otp' : '/auth/register/resend-otp';
        await api.post(endpoint, { email: context.email });
        showToast.success('Mã OTP mới đã được gửi vào email.');
      } catch (e) {
        showToast.error(e.message || 'Không thể gửi lại OTP.');
      } finally {
        setTimeout(() => { resendBtn.disabled = false; }, 30000);
      }
    },
  }, 'Gửi lại mã OTP');

  const backBtn = el('button', {
    type: 'button',
    class: 'btn btn-ghost btn-sm',
    style: 'font-size: 12px;',
    onClick: onBack,
  }, 'Quay lại');

  const form = el('form', {
    style: 'display: none; flex-direction: column; gap: 1rem;',
    onSubmit: async (e) => {
      e.preventDefault();
      const code = codeInput.value.trim();
      if (!code || code.length !== 6) return showToast.error('Vui lòng nhập đủ 6 chữ số OTP.');

      submitBtn.disabled = true;
      submitBtn.textContent = 'Đang xác thực...';
      try {
        const endpoint = context.flow === 'login' ? '/auth/verify-login-otp' : '/auth/register/verify-otp';
        const res = await api.post(endpoint, { email: context.email, code });
        showToast.success(context.flow === 'login' ? 'Đăng nhập thành công!' : 'Đăng ký thành công!');
        onSuccess(res);
      } catch (err) {
        const msg = err.message || 'Mã OTP không đúng hoặc đã hết hạn.';
        if (typeof onError === 'function') onError(msg);
        showToast.error(msg);
      } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Xác nhận OTP';
      }
    },
  }, [
    title,
    desc,
    codeInput,
    submitBtn,
    el('div', { style: 'display: flex; justify-content: space-between; align-items: center;' }, [backBtn, resendBtn]),
  ]);

  const show = (flow, email) => {
    context = { flow, email };
    codeInput.value = '';
    title.textContent = flow === 'login' ? 'Xác thực đăng nhập (OTP)' : 'Xác thực đăng ký (OTP)';
    desc.textContent = `Mã OTP đã được gửi đến email ${email}. Hiệu lực trong 10 phút.`;
    form.style.display = 'flex';
    codeInput.focus();
  };

  const hide = () => {
    context = null;
    form.style.display = 'none';
  };

  return { form, show, hide };
}
