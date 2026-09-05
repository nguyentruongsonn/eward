import { el } from './dom.js';
import { getStoredUser, clearAuthToken } from '../api/client.js';
import { getStaffNavigation } from './staff-nav.js';
import { getNavItemIcon } from './nav-icons.js';
import { createStaffNotificationDropdown } from './staff-notification-dropdown.js';

export function createStaffLayout({ navigate, contentNode, currentPath }) {
  const user = getStoredUser();
  const displayName = user?.full_name || user?.hoTen || user?.name || 'Cán bộ';
  const roleName = user?.role || user?.vaiTro || 'Cán bộ một cửa';

  const doSearch = () => {
    const q = searchInput.value.trim();
    if (q) navigate(`/can-bo/ho-so?search=${encodeURIComponent(q)}`);
  };
  const searchInput = el('input', {
    type: 'search', class: 'input staff-header-search', placeholder: 'Tìm kiếm nhanh mã hồ sơ, CCCD, tên công dân...',
    style: 'width: 290px; max-width: 100%; height: 34px; padding: 0.25rem 0.75rem; font-size: 12.5px; background: #f8fafc; border: 1px solid #cbd5e1; color: #0f172a; border-radius: 6px; outline: none;',
    onKeydown: (e) => { if (e.key === 'Enter') doSearch(); },
  });
  const searchBtn = el('button', {
    type: 'button', style: 'background: #004b87; color: #ffffff; border: none; height: 34px; padding: 0 0.85rem; border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer; transition: background 0.15s ease;',
    onClick: doSearch,
  }, 'Tìm kiếm');

  const searchContainer = el('div', { style: 'display: flex; align-items: center; gap: 0.35rem;' }, [searchInput, searchBtn]);

  const profileDropdown = el('div', { class: 'staff-profile-dropdown', style: 'position: relative; display: inline-block;' });
  let isDropdownOpen = false;
  const closeProfileDropdown = () => {
    if (isDropdownOpen) {
      isDropdownOpen = false;
      dropdownMenu.style.display = 'none';
      document.removeEventListener('click', closeProfileDropdown);
    }
  };

  const dropdownTrigger = el('button', {
    type: 'button', class: 'btn-profile-trigger',
    style: 'display: flex; align-items: center; gap: 0.6rem; background: #ffffff; border: 1px solid #e2e8f0; padding: 0.3rem 0.65rem; border-radius: 6px; cursor: pointer; color: #0f172a; box-shadow: 0 1px 2px rgba(0,0,0,0.03);',
    onClick: (e) => {
      e.stopPropagation();
      isDropdownOpen = !isDropdownOpen;
      dropdownMenu.style.display = isDropdownOpen ? 'block' : 'none';
      if (isDropdownOpen) {
        document.addEventListener('click', closeProfileDropdown);
      } else {
        document.removeEventListener('click', closeProfileDropdown);
      }
    },
  }, [
    el('span', {
      style: 'width: 26px; height: 26px; border-radius: 50%; background: #eff6ff; color: #004b87; display: inline-flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800; border: 1px solid #bfdbfe;',
    }, displayName.charAt(0).toUpperCase()),
    el('div', { style: 'text-align: left; display: flex; flex-direction: column;' }, [
      el('span', { style: 'font-size: 12.5px; font-weight: 700; color: #0f172a; line-height: 1.2;' }, displayName),
      el('span', { style: 'font-size: 11px; color: #0284c7; font-weight: 600;' }, roleName),
    ]),
    el('span', { class: 'material-symbols-outlined', style: 'font-size: 16px; color: #64748b;' }, 'expand_more'),
  ]);

  const dropdownMenu = el('div', {
    class: 'staff-profile-menu',
    style: 'display: none; position: absolute; right: 0; top: calc(100% + 6px); width: 230px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 10px 25px -5px rgba(15,23,42,0.12); z-index: 1000; overflow: hidden; color: #0f172a;',
  }, [
    el('div', { style: 'padding: 0.75rem 1rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0;' }, [
      el('div', { style: 'font-size: 13px; font-weight: 800; color: #004b87; line-height: 1.3;' }, displayName),
      el('div', { style: 'font-size: 11.5px; color: #64748b; margin-top: 0.15rem; word-break: break-all;' }, user?.email || 'canbo@eward.gov.vn'),
      el('div', { style: 'display: inline-block; margin-top: 0.35rem; font-size: 10.5px; font-weight: 700; color: #004b87; background: #eff6ff; padding: 0.1rem 0.45rem; border-radius: 4px; border: 1px solid #bfdbfe;' }, roleName),
    ]),
    el('div', { style: 'padding: 0.35rem 0;' }, [
      el('button', {
        type: 'button', style: 'width: 100%; text-align: left; padding: 0.5rem 1rem; background: none; border: none; font-size: 12.5px; color: #334155; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;',
        onClick: () => { closeProfileDropdown(); navigate('/can-bo'); },
      }, 'Bảng điều hành'),
      el('button', {
        type: 'button', style: 'width: 100%; text-align: left; padding: 0.5rem 1rem; background: none; border: none; font-size: 12.5px; color: #334155; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;',
        onClick: () => { closeProfileDropdown(); navigate('/can-bo/ho-so'); },
      }, 'Danh sách hồ sơ'),
    ]),
    el('div', { style: 'border-top: 1px solid #f1f5f9; padding: 0.35rem 0;' }, [
      el('button', {
        type: 'button', style: 'width: 100%; text-align: left; padding: 0.5rem 1rem; background: none; border: none; font-size: 12.5px; color: #dc2626; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;',
        onClick: () => { closeProfileDropdown(); clearAuthToken(); window.dispatchEvent(new CustomEvent('auth:change', { detail: null })); navigate('/dang-nhap'); },
      }, 'Đăng xuất tài khoản'),
    ]),
  ]);

  profileDropdown.append(dropdownTrigger, dropdownMenu);

  let isCollapsed = false;
  const toggleSidebarBtn = el('button', {
    type: 'button', title: 'Thu gọn / Mở rộng menu nghiệp vụ',
    style: 'background: #f8fafc; border: 1px solid #e2e8f0; color: #004b87; border-radius: 6px; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 14px;',
    onClick: () => toggleSidebar(),
  }, [el('span', { class: 'material-symbols-outlined', style: 'font-size: 20px;' }, 'menu')]);

  const notificationDropdown = createStaffNotificationDropdown({ navigate, user });

  const topbar = el('header', {
    class: 'staff-topbar',
    style: 'background: #ffffff; color: #0f172a; padding: 0.65rem 1.25rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(15,23,42,0.03); z-index: 50; gap: 1rem; flex-wrap: wrap;',
  }, [
    el('div', { style: 'display: flex; align-items: center; gap: 0.5rem;' }, [toggleSidebarBtn]),
    searchContainer,
    el('div', { style: 'display: flex; align-items: center; gap: 0.75rem;' }, [notificationDropdown, profileDropdown]),
  ]);

  const navGroups = getStaffNavigation(roleName, currentPath);
  const navList = el('div', {
    class: 'staff-nav-list',
    style: 'display: flex; flex-direction: column; gap: 2px; flex: 1; overflow-y: auto;',
  });

  navGroups.forEach(group => {
    if (group.id === 'applications') {
      const isChildActive = group.items.some(it => currentPath === it.path);
      let isExpanded = true;

      const toggleArrow = el('span', { class: 'sidebar-item-label', style: 'font-size: 10px; color: rgba(255,255,255,0.7); margin-left: auto;' }, isExpanded ? '▾' : '▸');

      const parentBtn = el('button', {
        type: 'button', title: 'Hồ sơ xử lý',
        style: `width: 100%; text-align: left; display: flex; align-items: center; gap: 0.65rem; padding: 0.5rem 0.75rem; border-radius: 6px; font-size: 12.5px; border: none; cursor: pointer; transition: all 0.15s ease; ${isChildActive ? 'background: rgba(255,255,255,0.12); color: #ffffff; font-weight: 700;' : 'background: transparent; color: rgba(255,255,255,0.9); font-weight: 600;'}`,
        onClick: () => {
          if (isCollapsed) return;
          isExpanded = !isExpanded;
          toggleArrow.textContent = isExpanded ? '▾' : '▸';
          subContainer.style.display = isExpanded ? 'flex' : 'none';
        },
      }, [
        getNavItemIcon('/can-bo/ho-so', 'Tất cả hồ sơ'),
        el('span', { class: 'sidebar-item-label', style: 'line-height: 1.35;' }, 'Hồ sơ'),
        toggleArrow,
      ]);

      const subContainer = el('div', {
        class: 'sidebar-sub-list',
        style: `display: ${isExpanded ? 'flex' : 'none'}; flex-direction: column; gap: 2px; margin-bottom: 0.25rem;`,
      }, group.items.map(item => {
        const isActive = currentPath === item.path;
        const b = el('button', {
          type: 'button', title: item.label,
          style: `width: 100%; text-align: left; display: flex; align-items: center; padding: 0.4rem 0.75rem 0.4rem 2.1rem; border-radius: 5px; font-size: 12px; border: none; cursor: pointer; transition: all 0.15s ease; ${isActive ? 'background: rgba(255,255,255,0.16); color: #ffffff; font-weight: 700; border-left: 3px solid #38bdf8;' : 'background: transparent; color: rgba(255,255,255,0.85); font-weight: 500; border-left: 3px solid transparent;'}`,
          onClick: () => navigate(item.path),
        }, [
          el('span', { class: 'sidebar-item-label', style: 'line-height: 1.35; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;' }, item.label),
        ]);
        if (!isActive) {
          b.onmouseenter = () => { b.style.background = 'rgba(255,255,255,0.08)'; b.style.color = '#ffffff'; };
          b.onmouseleave = () => { b.style.background = 'transparent'; b.style.color = 'rgba(255,255,255,0.85)'; };
        }
        return b;
      }));

      navList.append(parentBtn, subContainer);
    } else {
      group.items.forEach(item => {
        const isActive = currentPath === item.path;
        const navIcon = getNavItemIcon(item.path, item.label);
        const b = el('button', {
          type: 'button', title: item.label,
          style: `width: 100%; text-align: left; display: flex; align-items: center; gap: 0.65rem; padding: 0.5rem 0.75rem; border-radius: 6px; font-size: 12.5px; border: none; cursor: pointer; transition: all 0.15s ease; ${isActive ? 'background: rgba(255,255,255,0.16); color: #ffffff; font-weight: 700; border-left: 3px solid #38bdf8;' : 'background: transparent; color: rgba(255,255,255,0.9); font-weight: 500; border-left: 3px solid transparent;'}`,
          onClick: () => navigate(item.path),
        }, [
          navIcon,
          el('span', { class: 'sidebar-item-label', style: 'line-height: 1.35; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;' }, item.label),
        ]);
        if (!isActive) {
          b.onmouseenter = () => { b.style.background = 'rgba(255,255,255,0.08)'; b.style.color = '#ffffff'; };
          b.onmouseleave = () => { b.style.background = 'transparent'; b.style.color = 'rgba(255,255,255,0.9)'; };
        }
        navList.append(b);
      });
    }
  });

  const sidebarBrand = el('div', {
    style: 'display: flex; align-items: center; gap: 0.65rem; padding: 0.35rem 0.5rem 0.85rem; border-bottom: 1px solid rgba(255, 255, 255, 0.12); margin-bottom: 0.65rem;',
  }, [
    el('div', {
      style: 'width: 34px; height: 34px; border-radius: 8px; background: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 13px; color: #004b87; box-shadow: 0 2px 4px rgba(0,0,0,0.15); flex-shrink: 0;',
    }, 'EW'),
    el('div', { class: 'sidebar-brand-text' }, [
      el('div', { style: 'font-family: var(--font-heading); font-size: 12.5px; font-weight: 800; color: #ffffff; line-height: 1.2;' }, 'UBND CẤP XÃ/PHƯỜNG'),
      el('div', { style: 'font-size: 11px; color: #bae6fd; font-weight: 500;' }, 'Hệ thống Quản lý Một cửa'),
    ]),
  ]);

  const sidebar = el('aside', {
    class: 'staff-sidebar',
    style: 'width: 250px; height: 100vh; border-right: 1px solid rgba(255, 255, 255, 0.12); display: flex; flex-direction: column; padding: 0.85rem 0.65rem; flex-shrink: 0; box-sizing: border-box; transition: width 0.2s ease;',
  }, [
    sidebarBrand,
    navList,
  ]);

  function toggleSidebar() {
    isCollapsed = !isCollapsed;
    sidebar.style.width = isCollapsed ? '64px' : '250px';
    sidebar.style.padding = isCollapsed ? '0.75rem 0.35rem' : '0.85rem 0.65rem';
    const brandText = sidebar.querySelector('.sidebar-brand-text');
    if (brandText) brandText.style.display = isCollapsed ? 'none' : 'block';
    toggleSidebarBtn.replaceChildren(
      el('span', { class: 'material-symbols-outlined', style: 'font-size: 20px;' }, isCollapsed ? 'menu_open' : 'menu')
    );

    const subLists = navList.querySelectorAll('.sidebar-sub-list');
    subLists.forEach(sl => { sl.style.display = isCollapsed ? 'none' : 'flex'; });

    const labels = navList.querySelectorAll('.sidebar-item-label');
    labels.forEach(l => { l.style.display = isCollapsed ? 'none' : 'inline'; });

    const buttons = navList.querySelectorAll('button');
    buttons.forEach(b => {
      b.style.justifyContent = isCollapsed ? 'center' : 'flex-start';
      if (isCollapsed) b.style.padding = '0.5rem 0';
      else if (!b.closest('.sidebar-sub-list')) b.style.padding = '0.5rem 0.75rem';
      else b.style.padding = '0.4rem 0.75rem 0.4rem 2.1rem';
    });
  }

  const workspaceContent = el('div', {
    class: 'staff-workspace-content',
    style: 'flex: 1; overflow-y: auto; background: #f8fafc;',
  }, contentNode);

  const mainArea = el('main', {
    class: 'staff-main-area',
    style: 'flex: 1; display: flex; flex-direction: column; height: 100vh; overflow: hidden; min-width: 0;',
  }, [topbar, workspaceContent]);

  return el('div', {
    class: 'staff-layout-container',
    style: 'height: 100vh; display: flex; flex-direction: row; background: #f8fafc; font-family: var(--font-body); overflow: hidden;',
  }, [sidebar, mainArea]);
}

