import { el } from './dom.js';
import { openAuthModal } from './auth-modal.js';
import { api, getAuthToken, getStoredUser, clearAuthToken, setStoredUser } from '../api/client.js';

export function renderHeader({ navigate }) {
  const emblemSvg = el('div', {
    class: 'header-emblem',
    style: 'width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;',
  });
  emblemSvg.innerHTML = `
    <svg width="38" height="38" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
      <circle cx="24" cy="24" r="23" fill="#B91C1C"/>
      <circle cx="24" cy="24" r="20" stroke="#FDE047" stroke-width="1.5" stroke-dasharray="3 2"/>
      <polygon points="24,11 27.8,21.5 39,21.5 30,28.1 33.4,38.5 24,32 14.6,38.5 18,28.1 9,21.5 20.2,21.5" fill="#FDE047"/>
    </svg>
  `;

  const actionsContainer = el('div', { class: 'header-actions', style: 'display: flex; align-items: center; gap: 0.5rem;' });

  function updateAuthUI() {
    actionsContainer.replaceChildren();

    const token = getAuthToken();
    const user = getStoredUser();

    if (token) {
      const displayName = user?.full_name || user?.hoTen || user?.name || 'Tài khoản';
      const roleName = user?.role || user?.vaiTro || 'Đã xác thực';

      const isStaff = roleName.includes('Cán bộ') || roleName.includes('Lãnh đạo') || roleName.includes('Quản trị') || roleName.includes('Admin') || roleName.includes('Một cửa') || roleName.includes('Thụ lý');

      const dropdownWrapper = el('div', { class: 'user-profile-dropdown', style: 'position: relative; display: inline-block;' });

      const dropdownTrigger = el('button', {
        type: 'button',
        class: 'btn btn-ghost btn-sm',
        style: 'display: flex; align-items: center; gap: 0.45rem; padding: 0.35rem 0.65rem; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 4px; color: #004482; font-weight: 700; font-size: 12.5px;',
      }, [
        el('span', { style: 'width: 22px; height: 22px; border-radius: 50%; background: #004482; color: #ffffff; display: inline-flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800;' }, displayName.charAt(0).toUpperCase()),
        el('span', { style: 'max-width: 140px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;' }, displayName),
        el('span', { style: 'font-size: 10px; color: #64748b; margin-left: 2px;' }, '▾'),
      ]);

      const dropdownMenu = el('div', {
        class: 'dropdown-menu',
        style: 'display: none; position: absolute; right: 0; top: calc(100% + 4px); width: 230px; background: #ffffff; border: 1px solid #d0d7de; border-radius: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); z-index: 1000; padding: 0.5rem 0;',
      }, [
        el('div', { style: 'padding: 0.6rem 0.85rem; border-bottom: 1px solid #f1f5f9;' }, [
          el('div', { style: 'font-size: 13px; font-weight: 700; color: #0f172a; line-height: 1.3;' }, displayName),
          el('div', { style: 'font-size: 11px; color: #64748b; margin-top: 0.2rem;' }, roleName),
          user?.email ? el('div', { style: 'font-size: 11px; color: #64748b; margin-top: 0.15rem; word-break: break-all;' }, user.email) : '',
        ]),
        isStaff ? el('button', {
          type: 'button',
          style: 'width: 100%; text-align: left; padding: 0.5rem 0.85rem; background: #e0f2fe; border: none; font-size: 12.5px; color: #004482; font-weight: 700; cursor: pointer;',
          onClick: () => {
            dropdownMenu.style.display = 'none';
            navigate('/can-bo');
          },
        }, 'Bàn làm việc Cán bộ') : null,
        el('button', {
          type: 'button',
          style: 'width: 100%; text-align: left; padding: 0.5rem 0.85rem; background: transparent; border: none; font-size: 12.5px; color: #334155; font-weight: 600; cursor: pointer;',
          onClick: () => {
            dropdownMenu.style.display = 'none';
            navigate('/tai-khoan');
          },
        }, 'Thông tin tài khoản'),
        el('button', {
          type: 'button',
          style: 'width: 100%; text-align: left; padding: 0.5rem 0.85rem; background: transparent; border: none; font-size: 12.5px; color: #334155; font-weight: 600; cursor: pointer;',
          onClick: () => {
            dropdownMenu.style.display = 'none';
            navigate('/tai-khoan?tab=applications');
          },
        }, 'Hồ sơ đã nộp'),
        el('div', { style: 'height: 1px; background: #f1f5f9; margin: 0.25rem 0;' }),
        el('button', {
          type: 'button',
          style: 'width: 100%; text-align: left; padding: 0.5rem 0.85rem; background: transparent; border: none; font-size: 12.5px; color: #b91c1c; font-weight: 700; cursor: pointer;',
          onClick: () => {
            clearAuthToken();
            window.dispatchEvent(new CustomEvent('auth:change', { detail: null }));
            window.location.reload();
          },
        }, 'Đăng xuất tài khoản'),
      ]);

      dropdownTrigger.addEventListener('click', (e) => {
        e.stopPropagation();
        const isOpen = dropdownMenu.style.display === 'block';
        dropdownMenu.style.display = isOpen ? 'none' : 'block';
      });

      document.addEventListener('click', (e) => {
        if (!dropdownWrapper.contains(e.target)) {
          dropdownMenu.style.display = 'none';
        }
      });

      dropdownWrapper.append(dropdownTrigger, dropdownMenu);
      if (isStaff) {
        actionsContainer.append(el('button', {
          type: 'button',
          class: 'btn btn-primary btn-sm',
          style: 'background: #004482; font-weight: 700; font-size: 12px;',
          onClick: () => navigate('/can-bo'),
        }, 'Bàn làm việc Cán bộ'));
      }
      actionsContainer.append(dropdownWrapper);
    } else {
      actionsContainer.append(
        el('button', {
          type: 'button',
          class: 'btn btn-primary btn-sm',
          style: 'background: #004b87; font-weight: 700;',
          onClick: () => openAuthModal('login', () => updateAuthUI()),
        }, 'Đăng nhập')
      );
    }
  }

  updateAuthUI();
  window.addEventListener('auth:change', updateAuthUI);

  if (getAuthToken()) {
    api.get('/auth/me').then(res => {
      if (res?.data) {
        setStoredUser(res.data);
        updateAuthUI();
      }
    }).catch(err => {
      if (err.status === 401) {
        clearAuthToken();
        updateAuthUI();
      }
    });
  }

  const mainHeader = el('header', { class: 'main-header', style: 'border-bottom: 1px solid #e2e8f0; background: #ffffff;' }, [
    el('div', { class: 'container-portal main-header-content', style: 'padding: 0.75rem 1rem; display: flex; justify-content: space-between; align-items: center;' }, [
      el('a', {
        href: '/',
        class: 'header-brand',
        style: 'display: flex; align-items: center; gap: 0.75rem; text-decoration: none; color: inherit;',
        onClick: (e) => { e.preventDefault(); navigate('/'); },
      }, [
        emblemSvg,
        el('div', { class: 'brand-text' }, [
          el('h1', { style: 'font-family: var(--font-heading); font-size: 16px; font-weight: 800; color: #004b87; letter-spacing: 0.01em; margin: 0; line-height: 1.25;' }, 'CỔNG DỊCH VỤ CÔNG TRỰC TUYẾN'),
          el('p', { style: 'font-size: 11px; font-weight: 600; color: #64748b; margin: 0.2rem 0 0; letter-spacing: 0.02em; text-transform: uppercase;' }, 'HỆ THỐNG GIẢI QUYẾT THỦ TỤC HÀNH CHÍNH XÃ ABC'),
        ]),
      ]),
      actionsContainer,
    ]),
  ]);

  const navBar = el('nav', { class: 'nav-bar', style: 'background: #073866; border-top: 1px solid rgba(255,255,255,0.1);' }, [
    el('div', { class: 'container-portal nav-content', style: 'display: flex; justify-content: space-between; align-items: center;' }, [
      el('div', { class: 'nav-links', style: 'display: flex; gap: 0.35rem; padding: 0.35rem 0;' }, [
        el('a', {
          href: '/',
          class: 'nav-link active',
          style: 'font-size: 13px; font-weight: 700; padding: 0.45rem 1rem; border-radius: 4px; color: #004b87; background: #ffffff; text-decoration: none;',
          onClick: (e) => { e.preventDefault(); navigate('/'); },
        }, 'Trang chủ'),
        el('a', {
          href: '/thu-tuc',
          class: 'nav-link',
          style: 'font-size: 13px; font-weight: 600; padding: 0.45rem 1rem; border-radius: 4px; color: rgba(255,255,255,0.9); text-decoration: none;',
          onClick: (e) => { e.preventDefault(); navigate('/thu-tuc'); },
        }, 'Thủ tục hành chính'),
        el('a', {
          href: '/tra-cuu',
          class: 'nav-link',
          style: 'font-size: 13px; font-weight: 600; padding: 0.45rem 1rem; border-radius: 4px; color: rgba(255,255,255,0.9); text-decoration: none;',
          onClick: (e) => { e.preventDefault(); navigate('/tra-cuu'); },
        }, 'Tra cứu tiến độ hồ sơ'),
        el('a', {
          href: '/phan-anh',
          class: 'nav-link',
          style: 'font-size: 13px; font-weight: 600; padding: 0.45rem 1rem; border-radius: 4px; color: rgba(255,255,255,0.9); text-decoration: none;',
          onClick: (e) => { e.preventDefault(); navigate('/phan-anh'); },
        }, 'Phản ánh kiến nghị'),
      ]),
      el('div', { class: 'nav-clock', style: 'font-size: 12px; color: rgba(255,255,255,0.85);' }, [
        el('span', { id: 'header-clock-ticker' }, 'Hôm nay: Thứ Năm, 10/09/2026'),
      ]),
    ]),
  ]);

  return el('div', { class: 'portal-header-wrapper' }, mainHeader, navBar);
}
