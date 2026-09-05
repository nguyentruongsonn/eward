import { el } from './dom.js';
import { api, setAuthTokens, setStoredUser } from '../api/client.js';
import { showToast } from './toast.js';
import { createAuthOtpForm } from './auth-otp-form.js';

let modalInstance = null;

export function openAuthModal(defaultTab = 'login', onAuthSuccess) {
  if (modalInstance) {
    modalInstance.remove();
    modalInstance = null;
  }

  let activeTab = defaultTab;

  const close = () => {
    if (modalInstance) {
      modalInstance.remove();
      modalInstance = null;
    }
    document.removeEventListener('keydown', handleEsc);
  };

  const handleEsc = (e) => {
    if (e.key === 'Escape') close();
  };
  document.addEventListener('keydown', handleEsc);

  const tabLoginBtn = el('button', {
    type: 'button',
    style: 'flex: 1; padding: 0.85rem; font-size: 14px; font-weight: 700; border-bottom: 2px solid var(--primary); color: var(--primary); background: transparent;',
  }, 'ĐĂNG NHẬP');

  const tabRegisterBtn = el('button', {
    type: 'button',
    style: 'flex: 1; padding: 0.85rem; font-size: 14px; font-weight: 700; border-bottom: 2px solid transparent; color: var(--on-surface-variant); background: transparent;',
  }, 'ĐĂNG KÝ TÀI KHOẢN');

  const tabBar = el('div', { style: 'display: flex; border-bottom: 1px solid var(--outline-variant); margin-bottom: 1.25rem;' }, tabLoginBtn, tabRegisterBtn);

  const errorAlert = el('div', {
    style: 'display: none; padding: 0.65rem 0.85rem; border-radius: var(--rounded-sm); background: #ffebee; color: var(--error); font-size: 12px; margin-bottom: 1rem;',
  });

  const handleAuthComplete = (res) => {
    const token = res.data?.access_token || res.data?.token || res.access_token || res.token;
    const refreshToken = res.data?.refresh_token || res.refresh_token || token;
    if (token) {
      setAuthTokens(token, refreshToken);
      if (res.data?.user) setStoredUser(res.data.user);
      window.dispatchEvent(new CustomEvent('auth:change', { detail: res.data?.user }));
      close();
      if (typeof onAuthSuccess === 'function') onAuthSuccess(res);
      else window.location.reload();
    }
  };

  const otp = createAuthOtpForm({
    onBack: () => {
      otp.hide();
      tabBar.style.display = 'flex';
      switchTab(activeTab);
    },
    onSuccess: handleAuthComplete,
    onError: (msg) => {
      errorAlert.textContent = msg;
      errorAlert.style.display = 'block';
    },
  });

  const showOtpScreen = (flow, email) => {
    tabBar.style.display = 'none';
    loginForm.style.display = 'none';
    registerForm.style.display = 'none';
    errorAlert.style.display = 'none';
    otp.show(flow, email);
  };

  const loginEmail = el('input', { type: 'text', class: 'input', placeholder: 'Email hoặc Số CCCD...', required: true });
  const loginPass = el('input', { type: 'password', class: 'input', placeholder: 'Mật khẩu...', required: true });
  const loginBtn = el('button', { type: 'submit', class: 'btn btn-primary', style: 'width: 100%; height: 42px; margin-top: 0.5rem;' }, 'Đăng nhập');

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
      loginEmail.value = acc.email;
      loginPass.value = 'password123';
    },
  }, acc.label));

  const loginForm = el('form', {
    style: 'display: flex; flex-direction: column; gap: 1rem;',
    onSubmit: async (e) => {
      e.preventDefault();
      errorAlert.style.display = 'none';
      loginBtn.disabled = true;
      loginBtn.textContent = 'Đang xác thực...';

      try {
        const res = await api.post('/auth/login', {
          email: loginEmail.value.trim(),
          password: loginPass.value,
        });
        if (res.data?.verification_required || res.verification_required) {
          const email = res.data?.email || res.email || loginEmail.value.trim();
          showToast.success('Mã OTP đã gửi về email. Vui lòng kiểm tra!');
          showOtpScreen('login', email);
          return;
        }
        handleAuthComplete(res);
      } catch (err) {
        const msg = err.message || 'Tài khoản hoặc mật khẩu không chính xác.';
        errorAlert.textContent = msg;
        errorAlert.style.display = 'block';
        showToast.error(msg);
      } finally {
        loginBtn.disabled = false;
        loginBtn.textContent = 'Đăng nhập';
      }
    },
  }, [
    el('div', {}, [el('label', { style: 'display: block; font-size: 12px; font-weight: 700; margin-bottom: 0.25rem;' }, 'TÀI KHOẢN (EMAIL / CCCD): *'), loginEmail]),
    el('div', {}, [el('label', { style: 'display: block; font-size: 12px; font-weight: 700; margin-bottom: 0.25rem;' }, 'MẬT KHẨU: *'), loginPass]),
    loginBtn,
    el('div', { style: 'padding-top: 0.75rem; border-top: 1px solid var(--outline-variant); font-size: 11px; color: var(--on-surface-variant);' }, [
      el('div', { style: 'margin-bottom: 0.35rem; font-weight: 600;' }, 'Tài khoản thử nghiệm nhanh:'),
      el('div', { style: 'display: flex; gap: 0.35rem; flex-wrap: wrap;' }, demoAccounts),
    ]),
  ]);

  const regName = el('input', { type: 'text', class: 'input', placeholder: 'Họ và tên...', required: true });
  const regCccd = el('input', { type: 'text', class: 'input numeric-data', placeholder: 'Số Căn cước công dân (12 số)...', required: true });
  const regPhone = el('input', { type: 'tel', class: 'input numeric-data', placeholder: 'Số điện thoại liên hệ...', required: true });
  const regEmail = el('input', { type: 'email', class: 'input', placeholder: 'Địa chỉ email...', required: true });
  const regPass = el('input', { type: 'password', class: 'input', placeholder: 'Mật khẩu...', required: true });
  const regBtn = el('button', { type: 'submit', class: 'btn btn-primary', style: 'width: 100%; height: 42px; margin-top: 0.5rem;' }, 'Đăng ký tài khoản');

  const registerForm = el('form', {
    style: 'display: none; flex-direction: column; gap: 0.75rem;',
    onSubmit: async (e) => {
      e.preventDefault();
      errorAlert.style.display = 'none';
      regBtn.disabled = true;
      regBtn.textContent = 'Đang xử lý đăng ký...';

      try {
        const res = await api.post('/auth/register', {
          name: regName.value.trim(),
          so_cccd: regCccd.value.trim(),
          so_dien_thoai: regPhone.value.trim(),
          email: regEmail.value.trim(),
          password: regPass.value,
        });
        const targetEmail = res.data?.email || res.email || regEmail.value.trim();
        showToast.success('Mã OTP đã gửi về email. Vui lòng xác thực!');
        showOtpScreen('register', targetEmail);
      } catch (err) {
        const msg = err.message || 'Đăng ký không thành công. Vui lòng kiểm tra lại thông tin.';
        errorAlert.textContent = msg;
        errorAlert.style.display = 'block';
        showToast.error(msg);
      } finally {
        regBtn.disabled = false;
        regBtn.textContent = 'Đăng ký tài khoản';
      }
    },
  }, [
    el('div', {}, [el('label', { style: 'display: block; font-size: 11px; font-weight: 700; margin-bottom: 0.2rem;' }, 'HỌ VÀ TÊN: *'), regName]),
    el('div', { style: 'display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;' }, [
      el('div', {}, [el('label', { style: 'display: block; font-size: 11px; font-weight: 700; margin-bottom: 0.2rem;' }, 'SỐ CCCD: *'), regCccd]),
      el('div', {}, [el('label', { style: 'display: block; font-size: 11px; font-weight: 700; margin-bottom: 0.2rem;' }, 'SỐ ĐIỆN THOẠI: *'), regPhone]),
    ]),
    el('div', {}, [el('label', { style: 'display: block; font-size: 11px; font-weight: 700; margin-bottom: 0.2rem;' }, 'EMAIL: *'), regEmail]),
    el('div', {}, [el('label', { style: 'display: block; font-size: 11px; font-weight: 700; margin-bottom: 0.2rem;' }, 'MẬT KHẨU: *'), regPass]),
    regBtn,
  ]);

  const switchTab = (tab) => {
    activeTab = tab;
    errorAlert.style.display = 'none';
    otp.hide();
    tabBar.style.display = 'flex';
    if (tab === 'login') {
      tabLoginBtn.style.borderBottomColor = 'var(--primary)';
      tabLoginBtn.style.color = 'var(--primary)';
      tabRegisterBtn.style.borderBottomColor = 'transparent';
      tabRegisterBtn.style.color = 'var(--on-surface-variant)';
      loginForm.style.display = 'flex';
      registerForm.style.display = 'none';
    } else {
      tabRegisterBtn.style.borderBottomColor = 'var(--primary)';
      tabRegisterBtn.style.color = 'var(--primary)';
      tabLoginBtn.style.borderBottomColor = 'transparent';
      tabLoginBtn.style.color = 'var(--on-surface-variant)';
      loginForm.style.display = 'none';
      registerForm.style.display = 'flex';
    }
  };

  tabLoginBtn.addEventListener('click', () => switchTab('login'));
  tabRegisterBtn.addEventListener('click', () => switchTab('register'));
  if (defaultTab === 'register') switchTab('register');

  const modalBox = el('div', {
    class: 'card',
    style: 'position: relative; width: 100%; max-width: 460px; background: #ffffff; border-radius: var(--rounded-lg); padding: 2rem; box-shadow: var(--shadow-modal); z-index: 1001;',
    role: 'dialog',
    'aria-modal': 'true',
  }, [
    el('button', {
      type: 'button',
      style: 'position: absolute; right: 1rem; top: 1rem; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: var(--rounded-full); color: var(--on-surface-variant); font-size: 18px;',
      onClick: close,
    }, '✕'),
    el('div', { style: 'text-align: center; margin-bottom: 1.25rem;' }, [
      el('div', { style: 'width: 44px; height: 44px; border-radius: 50%; background: var(--primary-fixed); color: var(--primary); display: flex; align-items: center; justify-content: center; margin: 0 auto 0.5rem;' },
        el('span', { class: 'material-symbols-outlined', style: 'font-size: 24px;' }, 'account_balance')
      ),
      el('h2', { style: 'font-family: var(--font-heading); font-size: 18px; font-weight: 700;' }, 'Cổng Dịch Vụ Công Trực Tuyến'),
    ]),
    tabBar,
    errorAlert,
    loginForm,
    registerForm,
    otp.form,
  ]);

  modalInstance = el('div', {
    style: 'position: fixed; inset: 0; background: rgba(18, 48, 74, 0.45); display: flex; align-items: center; justify-content: center; padding: 1rem; z-index: 1000;',
    onClick: (e) => {
      if (e.target === modalInstance) close();
    },
  }, modalBox);

  document.body.appendChild(modalInstance);
  if (activeTab === 'login') loginEmail.focus(); else regName.focus();
}
