import { el } from '../components/dom.js';
import { getAuthToken, getStoredUser, clearAuthToken } from '../api/client.js';
import { openAuthModal } from '../components/auth-modal.js';
import { createProfileAccountTab } from '../components/profile-account-tab.js';
import { createProfileApplicationsTab } from '../components/profile-applications-tab.js';
import { createProfilePaymentsTab } from '../components/profile-payments-tab.js';

export function renderProfilePage({ navigate, searchParams }) {
  const container = el('div', { class: 'container-portal page-profile', style: 'padding-top: 1.5rem; padding-bottom: 3rem;' });

  const breadcrumb = el('div', {
    class: 'breadcrumb',
    style: 'display: flex; align-items: center; gap: 0.4rem; font-size: 12.5px; color: #64748b; margin-bottom: 1.25rem;',
  }, [
    el('a', { href: '/', style: 'color: #004482; text-decoration: none; font-weight: 600;' }, 'Trang chủ'),
    el('span', {}, '/'),
    el('span', { style: 'color: #0f172a; font-weight: 700;' }, 'Tài khoản công dân'),
  ]);

  const mainArea = el('div', { style: 'margin-top: 0.5rem;' });
  container.append(breadcrumb, mainArea);

  function checkAuthAndRender() {
    const token = getAuthToken();
    const user = getStoredUser();

    if (!token) {
      mainArea.replaceChildren(el('div', {
        class: 'card',
        style: 'padding: 3rem 2rem; max-width: 540px; margin: 2rem auto; text-align: center; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);',
      }, [
        el('div', { style: 'width: 56px; height: 56px; border-radius: 50%; background: #eff6ff; color: #004482; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;' },
          el('span', { class: 'material-symbols-outlined', style: 'font-size: 32px;' }, 'lock')
        ),
        el('h2', { style: 'font-family: var(--font-heading); font-size: 18px; font-weight: 800; color: #004482; margin-bottom: 0.5rem;' }, 'YÊU CẦU ĐĂNG NHẬP'),
        el('p', { style: 'font-size: 13px; color: #475569; line-height: 1.6; margin-bottom: 1.5rem;' }, 'Quý công dân vui lòng đăng nhập để xem thông tin tài khoản, hồ sơ dịch vụ công và lịch sử thanh toán.'),
        el('button', {
          type: 'button',
          class: 'btn btn-primary',
          style: 'background: #004482; font-weight: 700; padding: 0.6rem 1.75rem;',
          onClick: () => openAuthModal('login', () => checkAuthAndRender()),
        }, 'Đăng nhập ngay'),
      ]));
      return;
    }

    renderWorkspace(user || {});
  }

  function renderWorkspace(currentUser) {
    let activeTab = searchParams?.get('tab') || 'account';
    let userState = { ...currentUser };

    const aside = el('aside', {
      class: 'profile-aside card',
      style: 'width: 270px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1.25rem; box-shadow: 0 1px 3px rgba(15,23,42,0.03); flex-shrink: 0;',
    });

    const userCard = el('div', { style: 'text-align: center; padding-bottom: 1rem; border-bottom: 1px solid #f1f5f9; margin-bottom: 0.75rem;' });

    function updateUserCard() {
      const displayName = userState.full_name || userState.hoTen || userState.name || 'Công dân';
      const initial = displayName.charAt(0).toUpperCase();
      const email = userState.email || 'Chưa cung cấp email';
      const role = userState.role || userState.vaiTro || 'Công dân';

      userCard.replaceChildren(
        el('div', { style: 'width: 52px; height: 52px; border-radius: 50%; background: #004482; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 800; margin: 0 auto 0.65rem;' }, initial),
        el('h3', { style: 'font-size: 15px; font-weight: 800; color: #0f172a; margin: 0;' }, displayName),
        el('div', { style: 'font-size: 11.5px; color: #64748b; margin-top: 0.2rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;' }, email),
        el('div', { style: 'margin-top: 0.5rem;' },
          el('span', { style: 'display: inline-block; padding: 0.15rem 0.6rem; border-radius: 9999px; background: #eff6ff; color: #004482; font-size: 10.5px; font-weight: 700; border: 1px solid #bfdbfe;' }, role)
        )
      );
    }
    updateUserCard();

    const menuItems = [
      { id: 'account', label: 'Thông tin tài khoản', icon: 'person' },
      { id: 'applications', label: 'Hồ sơ dịch vụ công', icon: 'folder_shared' },
      { id: 'payments', label: 'Lịch sử thanh toán', icon: 'receipt_long' },
    ];

    const navList = el('nav', { style: 'display: flex; flex-direction: column; gap: 0.35rem;' });

    function updateNav() {
      navList.replaceChildren(...menuItems.map(item => {
        const isActive = activeTab === item.id;
        return el('button', {
          type: 'button',
          style: `width: 100%; text-align: left; display: flex; align-items: center; gap: 0.65rem; padding: 0.65rem 0.85rem; border-radius: 6px; font-size: 13px; font-weight: ${isActive ? '700' : '500'}; color: ${isActive ? '#004482' : '#334155'}; background: ${isActive ? '#eff6ff' : 'transparent'}; border: 1px solid ${isActive ? '#bfdbfe' : 'transparent'}; cursor: pointer; transition: all 0.15s ease;`,
          onClick: () => {
            activeTab = item.id;
            updateNav();
            renderTabContent();
          },
        }, [
          el('span', { class: 'material-symbols-outlined', style: `font-size: 19px; color: ${isActive ? '#004482' : '#64748b'};` }, item.icon),
          el('span', {}, item.label),
        ]);
      }));
    }

    const logoutBtn = el('button', {
      type: 'button',
      style: 'width: 100%; text-align: left; display: flex; align-items: center; gap: 0.65rem; padding: 0.65rem 0.85rem; border-radius: 6px; font-size: 13px; font-weight: 600; color: #dc2626; background: transparent; border: none; cursor: pointer; margin-top: 0.75rem; border-top: 1px solid #f1f5f9; padding-top: 0.85rem;',
      onClick: () => {
        clearAuthToken();
        window.dispatchEvent(new CustomEvent('auth:change', { detail: null }));
        navigate('/');
      },
    }, [
      el('span', { class: 'material-symbols-outlined', style: 'font-size: 19px; color: #dc2626;' }, 'logout'),
      el('span', {}, 'Đăng xuất tài khoản'),
    ]);

    aside.append(userCard, navList, logoutBtn);
    updateNav();

    const rightContent = el('main', {
      class: 'profile-main-content',
      style: 'flex: 1; min-width: 300px;',
    });

    function renderTabContent() {
      if (activeTab === 'account') {
        rightContent.replaceChildren(createProfileAccountTab({
          user: userState,
          onProfileUpdated: (updated) => {
            userState = { ...userState, ...updated };
            updateUserCard();
          },
        }));
      } else if (activeTab === 'applications') {
        rightContent.replaceChildren(createProfileApplicationsTab({ navigate }));
      } else if (activeTab === 'payments') {
        rightContent.replaceChildren(createProfilePaymentsTab({ navigate }));
      }
    }

    renderTabContent();

    const layout = el('div', {
      style: 'display: flex; gap: 1.5rem; align-items: flex-start; flex-wrap: wrap;',
    }, [aside, rightContent]);

    mainArea.replaceChildren(layout);
  }

  checkAuthAndRender();
  return container;
}
