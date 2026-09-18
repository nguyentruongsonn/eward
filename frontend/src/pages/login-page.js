import { el } from '../components/dom.js';
import { api, setAuthTokens, setStoredUser } from '../api/client.js';
import { createAuthOtpForm } from '../components/auth-otp-form.js';

export function renderLoginPage({ navigate }) {
  const container = el('div', {
    class: 'container-portal',
    style: 'padding: 4rem 1rem; display: flex; justify-content: center; align-items: center;',
  });

  const emailInput = el('input', {
    type: 'email',
    class: 'input',
    id: 'login-email',
    placeholder: 'name@example.com hoặc số CCCD',
    required: true,
  });

  const passwordInput = el('input', {
    type: 'password',
    class: 'input',
    id: 'login-password',
    placeholder: 'Nhập mật khẩu...',
    required: true,
  });

  const errorAlert = el('div', {
    style: 'display: none; padding: 0.75rem; border-radius: var(--rounded-sm); background: #ffebee; color: var(--error); font-size: 13px; margin-bottom: 1rem;',
  });

  const submitBtn = el('button', {
    type: 'submit',
    class: 'btn btn-primary',
    style: 'width: 100%; height: 44px; margin-top: 0.5rem; font-size: 14px;',
  }, 'Đăng nhập vào hệ thống');

  const handleLoginSuccess = (res) => {
    const token = res.data?.access_token || res.data?.token || res.access_token || res.token;
    const refreshToken = res.data?.refresh_token || res.refresh_token || token;
    if (token) {
      setAuthTokens(token, refreshToken);
      if (res.data?.user) setStoredUser(res.data.user);
      window.dispatchEvent(new CustomEvent('auth:change', { detail: res.data?.user }));
      navigate('/');
    }
  };

  const otp = createAuthOtpForm({
    onBack: () => {
      otp.hide();
      credentialsSection.style.display = 'block';
      demoSection.style.display = 'block';
    },
    onSuccess: handleLoginSuccess,
    onError: (msg) => {
      errorAlert.textContent = msg;
      errorAlert.style.display = 'block';
    },
  });

  const demoAccounts = [
    { label: 'Công dân', email: 'nts594187a@gmail.com' },
    { label: 'Một cửa', email: 'canbo1@gmail.com' },
    { label: 'Thụ lý', email: 'canbo2@gmail.com' },
    { label: 'Lãnh đạo', email: 'lanhdao@gmail.com' },
    { label: 'Quản trị', email: 'admin@gmail.com' },
  ].map(acc => el('button', {
    type: 'button',
    class: 'btn btn-ghost btn-sm',
    style: 'font-size: 11px; padding: 0.2rem 0.5rem; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 3px; color: #004482;',
    onClick: () => {
      emailInput.value = acc.email;
      passwordInput.value = 'password123';
    },
  }, acc.label));

  const credentialsSection = el('div', {}, [
    el('div', { style: 'margin-bottom: 1.25rem;' }, [
      el('label', { for: 'login-email', style: 'display: block; font-size: 12px; font-weight: 700; margin-bottom: 0.35rem;' }, 'Email hoặc Số CCCD:'),
      emailInput,
    ]),
    el('div', { style: 'margin-bottom: 1.5rem;' }, [
      el('div', { style: 'display: flex; justify-content: space-between; margin-bottom: 0.35rem;' }, [
        el('label', { for: 'login-password', style: 'font-size: 12px; font-weight: 700;' }, 'Mật khẩu:'),
        el('a', { href: '#', style: 'font-size: 12px; color: var(--primary);' }, 'Quên mật khẩu?'),
      ]),
      passwordInput,
    ]),
    submitBtn,
  ]);

  const demoSection = el('div', {
    style: 'margin-top: 1.75rem; padding-top: 1.25rem; border-top: 1px solid var(--outline-variant); font-size: 12px; color: var(--on-surface-variant);',
  }, [
    el('div', { style: 'margin-bottom: 0.5rem; font-weight: 600;' }, 'Tài khoản thử nghiệm nhanh:'),
    el('div', { style: 'display: flex; gap: 0.35rem; flex-wrap: wrap;' }, demoAccounts),
  ]);

  const form = el('form', {
    class: 'card',
    style: 'width: 100%; max-width: 440px; padding: 2.5rem; background: #ffffff; border: 1px solid #d0d7de; border-radius: 4px;',
    onSubmit: async (e) => {
      e.preventDefault();
      errorAlert.style.display = 'none';
      submitBtn.disabled = true;
      submitBtn.textContent = 'Đang xác thực...';

      try {
        const res = await api.post('/auth/login', {
          email: emailInput.value.trim(),
          password: passwordInput.value,
        });

        if (res.data?.verification_required || res.verification_required) {
          const email = res.data?.email || res.email || emailInput.value.trim();
          credentialsSection.style.display = 'none';
          demoSection.style.display = 'none';
          otp.show('login', email);
          return;
        }

        handleLoginSuccess(res);
      } catch (err) {
        errorAlert.textContent = err.message || 'Tài khoản hoặc mật khẩu không chính xác.';
        errorAlert.style.display = 'block';
      } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Đăng nhập vào hệ thống';
      }
    },
  }, [
    el('div', { style: 'text-align: center; margin-bottom: 1.75rem;' }, [
      el('h2', { style: 'font-family: var(--font-heading); font-size: 20px; font-weight: 800; color: #004482; margin: 0;' }, 'ĐĂNG NHẬP HỆ THỐNG'),
      el('p', { style: 'font-size: 12px; color: #475569; margin-top: 0.35rem;' }, 'Cổng Dịch vụ công điện tử cấp Cơ sở'),
    ]),
    errorAlert,
    credentialsSection,
    otp.form,
    demoSection,
  ]);

  container.append(form);
  return container;
}
