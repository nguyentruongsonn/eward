import { el } from './dom.js';
import { api } from '../api/client.js';
import { normalizeStaffRole } from './staff-nav.js';
import {
  getDefaultNotifications,
  createBellIcon,
  createNotificationTypeIcon,
} from './staff-notification-data.js';

export function createStaffNotificationDropdown({ navigate, user }) {
  const role = normalizeStaffRole(user?.role || user?.vaiTro);
  const container = el('div', {
    class: 'staff-notification-dropdown',
    style: 'position: relative; display: inline-block;',
  });

  const READ_KEY = 'eward_staff_notifications_read';
  const getReadIds = () => {
    try {
      return JSON.parse(localStorage.getItem(READ_KEY) || '[]');
    } catch (_) {
      return [];
    }
  };
  const saveReadId = (id) => {
    const list = getReadIds();
    if (!list.includes(id)) {
      list.push(id);
      localStorage.setItem(READ_KEY, JSON.stringify(list));
    }
  };
  const markAllRead = (ids) => {
    localStorage.setItem(READ_KEY, JSON.stringify(ids));
  };

  let isOpen = false;
  let notifications = getDefaultNotifications(role);

  const badge = el('span', {
    class: 'staff-notification-badge',
    style: 'display: none; position: absolute; top: -4px; right: -4px; background: #dc2626; color: #ffffff; font-size: 10px; font-weight: 800; border-radius: 9999px; min-width: 17px; height: 17px; align-items: center; justify-content: center; padding: 0 3px; border: 2px solid #ffffff; line-height: 1;',
  });

  const triggerBtn = el('button', {
    type: 'button',
    title: 'Thông báo nghiệp vụ',
    style: 'position: relative; width: 34px; height: 34px; border-radius: 6px; background: #ffffff; border: 1px solid #e2e8f0; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.15s ease; box-shadow: 0 1px 2px rgba(0,0,0,0.03);',
    onClick: (e) => {
      e.stopPropagation();
      isOpen = !isOpen;
      menu.style.display = isOpen ? 'block' : 'none';
    },
  }, [createBellIcon(), badge]);

  const listContainer = el('div', {
    style: 'max-height: 360px; overflow-y: auto; display: flex; flex-direction: column;',
  });

  const unreadCountLabel = el('span', {
    style: 'font-size: 11px; font-weight: 700; color: #dc2626; background: #fee2e2; padding: 0.1rem 0.45rem; border-radius: 4px;',
  });

  const markAllBtn = el('button', {
    type: 'button',
    style: 'background: none; border: none; font-size: 11.5px; color: #004b87; font-weight: 600; cursor: pointer; padding: 0;',
    onClick: (e) => {
      e.stopPropagation();
      markAllRead(notifications.map((n) => n.id));
      renderList();
    },
  }, 'Đánh dấu đã đọc');

  const header = el('div', {
    style: 'padding: 0.75rem 1rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;',
  }, [
    el('div', { style: 'display: flex; align-items: center; gap: 0.4rem;' }, [
      el('span', { style: 'font-size: 12px; font-weight: 800; color: #004482; text-transform: uppercase;' }, 'Thông báo'),
      unreadCountLabel,
    ]),
    markAllBtn,
  ]);

  const footer = el('div', {
    style: 'padding: 0.6rem 1rem; background: #f8fafc; border-top: 1px solid #e2e8f0; text-align: center;',
  }, [
    el('button', {
      type: 'button',
      style: 'background: none; border: none; font-size: 12px; color: #004b87; font-weight: 700; cursor: pointer;',
      onClick: () => {
        isOpen = false;
        menu.style.display = 'none';
        navigate('/can-bo/ho-so');
      },
    }, 'Xem tất cả hồ sơ cần xử lý →'),
  ]);

  const menu = el('div', {
    class: 'staff-notification-menu',
    style: 'display: none; position: absolute; right: 0; top: calc(100% + 6px); width: 340px; max-width: calc(100vw - 2rem); background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 10px 25px -5px rgba(15,23,42,0.12); z-index: 1000; overflow: hidden;',
  }, [header, listContainer, footer]);

  document.addEventListener('click', () => {
    if (isOpen) {
      isOpen = false;
      menu.style.display = 'none';
    }
  });

  function renderList() {
    const readIds = getReadIds();
    const unreadCount = notifications.filter((n) => !readIds.includes(n.id)).length;

    if (unreadCount > 0) {
      badge.textContent = String(unreadCount);
      badge.style.display = 'inline-flex';
      unreadCountLabel.textContent = `${unreadCount} mới`;
      unreadCountLabel.style.display = 'inline-block';
    } else {
      badge.style.display = 'none';
      unreadCountLabel.style.display = 'none';
    }

    listContainer.replaceChildren();

    if (!notifications.length) {
      listContainer.append(
        el('div', { style: 'padding: 2rem; text-align: center; color: #94a3b8; font-size: 12.5px;' }, 'Không có thông báo mới.')
      );
      return;
    }

    notifications.forEach((item) => {
      const isRead = readIds.includes(item.id);
      const bg = isRead ? '#ffffff' : '#f0f9ff';

      const row = el('div', {
        style: `padding: 0.75rem 1rem; border-bottom: 1px solid #f1f5f9; background: ${bg}; cursor: pointer; transition: background 0.15s ease; display: flex; gap: 0.65rem; align-items: flex-start;`,
        onClick: () => {
          saveReadId(item.id);
          isOpen = false;
          menu.style.display = 'none';
          renderList();
          if (item.path) navigate(item.path);
        },
        onmouseenter: (e) => { e.currentTarget.style.background = '#f8fafc'; },
        onmouseleave: (e) => { e.currentTarget.style.background = bg; },
      }, [
        el('div', {
          style: `width: 26px; height: 26px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px; ${item.type === 'rework' ? 'background: #fee2e2;' : item.type === 'forward' ? 'background: #e0f2fe;' : 'background: #dcfce7;'}`,
        }, [createNotificationTypeIcon(item.type)]),
        el('div', { style: 'flex: 1; min-width: 0;' }, [
          el('div', { style: 'display: flex; justify-content: space-between; align-items: baseline; gap: 0.35rem;' }, [
            el('span', { style: `font-size: 12.5px; font-weight: ${isRead ? '600' : '800'}; color: #0f172a; line-height: 1.3;` }, item.title),
            !isRead ? el('span', { style: 'width: 6px; height: 6px; border-radius: 50%; background: #0284c7; flex-shrink: 0;' }) : null,
          ]),
          el('div', { style: 'font-size: 11.5px; color: #475569; margin-top: 0.2rem; line-height: 1.35;' }, item.content),
          el('div', { style: 'font-size: 10.5px; color: #94a3b8; margin-top: 0.3rem;' }, item.time),
        ]),
      ]);

      listContainer.append(row);
    });
  }

  async function loadDynamicNotifications() {
    try {
      const res = await api.get('/admin/applications?per_page=6');
      const items = res?.data || [];
      const dynamicList = [];

      items.forEach((app) => {
        const id = Number(app.trangThai ?? app.status_id ?? app.status);
        const code = app.maHSXL || ('HSXL_' + app.id);
        const name = app.tenTTHC || app.procedure_name || 'Hồ sơ TTHC';

        if (id === 12) {
          dynamicList.push({
            id: 'app-rework-' + app.id,
            title: 'Lãnh đạo yêu cầu xử lý lại: ' + code,
            content: 'Hồ sơ "' + name + '" được Lãnh đạo yêu cầu rà soát và hoàn thiện lại.',
            time: 'Vừa xong',
            path: '/can-bo/ho-so/' + app.id,
            type: 'rework',
          });
        } else if (id === 2) {
          dynamicList.push({
            id: 'app-forward-' + app.id,
            title: 'Cán bộ Một cửa chuyển xử lý: ' + code,
            content: 'Hồ sơ "' + name + '" đã được tiếp nhận và bàn giao cho bạn thụ lý.',
            time: 'Gần đây',
            path: '/can-bo/ho-so/' + app.id,
            type: 'forward',
          });
        } else if (id === 9) {
          dynamicList.push({
            id: 'app-approve-' + app.id,
            title: 'Lãnh đạo đã phê duyệt: ' + code,
            content: 'Hồ sơ "' + name + '" đã hoàn tất phê duyệt và ký số kết quả.',
            time: 'Gần đây',
            path: '/can-bo/ho-so/' + app.id,
            type: 'approve',
          });
        }
      });

      if (dynamicList.length > 0) {
        notifications = dynamicList;
        renderList();
      }
    } catch (_) {}
  }

  renderList();
  loadDynamicNotifications();

  container.append(triggerBtn, menu);
  return container;
}
