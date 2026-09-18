import { el } from './dom.js';
import { api, getStoredUser } from '../api/client.js';
import { normalizeStaffRole } from './staff-nav.js';
import { openStaffActionModal } from './staff-action-modal.js';
import { showToast } from './toast.js';

export function createRowActionMenu(item, navigate, onRefresh, isNearBottom = false) {
  let isOpen = false;
  let closeTimer = null;

  const menu = el('div', {
    style: `position: absolute; right: 0; ${isNearBottom ? 'bottom: 100%;' : 'top: 100%;'} width: 185px; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 4px; box-shadow: 0 8px 20px rgba(0,0,0,0.14); z-index: 1000; display: none; padding: 0.35rem 0; text-align: left;`,
  });

  const handleDocClick = (e) => {
    if (!wrapper.contains(e.target)) {
      closeNow();
    }
  };

  function openMenu() {
    if (closeTimer) { clearTimeout(closeTimer); closeTimer = null; }
    if (!isOpen) {
      document.addEventListener('click', handleDocClick);
    }
    isOpen = true;
    menu.style.display = 'block';
  }

  function scheduleClose() {
    if (closeTimer) clearTimeout(closeTimer);
    closeTimer = setTimeout(() => {
      closeNow();
    }, 220);
  }

  function closeNow() {
    if (closeTimer) { clearTimeout(closeTimer); closeTimer = null; }
    if (isOpen) {
      document.removeEventListener('click', handleDocClick);
    }
    isOpen = false;
    menu.style.display = 'none';
  }

  function createMenuItem(label, color, onClick) {
    const btn = el('button', {
      type: 'button',
      style: `width: 100%; text-align: left; padding: 0.45rem 0.85rem; background: none; border: none; font-size: 12.5px; color: ${color}; font-weight: 600; cursor: pointer; display: block;`,
      onClick: (e) => {
        e.stopPropagation();
        closeNow();
        onClick();
      },
    }, label);
    btn.addEventListener('mouseenter', () => { btn.style.background = '#f1f5f9'; });
    btn.addEventListener('mouseleave', () => { btn.style.background = 'none'; });
    return btn;
  }

  const user = getStoredUser();
  const role = normalizeStaffRole(user?.vaiTro || user?.role);

  let items = [];

  if (role === 'case-officer') {
    items = [
      createMenuItem('Xử lý hồ sơ', '#004482', () => navigate(`/can-bo/ho-so/${item.id}`)),
      createMenuItem('Yêu cầu bổ sung', '#b45309', () => {
        openStaffActionModal({
          action: 'requestSupplement',
          actionLabel: 'Yêu cầu bổ sung hồ sơ',
          procedureComponents: [],
          onSubmit: async (payload) => {
            await api.post(`/admin/applications/${item.id}/supplement-requests`, payload);
            onRefresh();
          },
        });
      }),
      createMenuItem('Tạm dừng', '#d97706', async () => {
        if (confirm(`Xác nhận tạm dừng xử lý hồ sơ ${item.id}?`)) {
          try {
            await api.post(`/admin/applications/${item.id}/comments`, { comment: 'Cán bộ thụ lý tạm dừng giải quyết hồ sơ' });
            showToast.success(`Đã tạm dừng xử lý hồ sơ ${item.id}.`);
            onRefresh();
          } catch (err) { showToast.error(err.message); }
        }
      }),
      createMenuItem('Dừng xử lý', '#dc2626', () => {
        openStaffActionModal({
          action: 'rework',
          actionLabel: 'Dừng xử lý hồ sơ',
          procedureComponents: [],
          onSubmit: async (payload) => {
            await api.post(`/admin/applications/${item.id}/rework`, payload);
            onRefresh();
          },
        });
      }),
      createMenuItem('Xem lịch sử cập nhật', '#334155', () => navigate(`/can-bo/ho-so/${item.id}#audit`)),
      createMenuItem('Tải văn bản hồ sơ', '#334155', () => navigate(`/can-bo/ho-so/${item.id}?tab=dossier`)),
    ];
  } else if (role === 'one-stop') {
    const sid = Number(item.trangThai ?? item.status_id ?? item.status ?? 1);
    items = [];
    if (sid === 1 || sid === 11) {
      items.push(createMenuItem('Tiếp nhận hồ sơ', '#004482', () => {
        openStaffActionModal({
          action: 'accept', actionLabel: 'Tiếp nhận hồ sơ', procedureComponents: [],
          onSubmit: async (p) => { await api.post(`/admin/applications/${item.id}/accept`, p); onRefresh(); },
        });
      }));
    }
    if (sid === 1 || sid === 2) {
      items.push(createMenuItem('Yêu cầu bổ sung', '#b45309', () => {
        openStaffActionModal({
          action: 'requestSupplement', actionLabel: 'Yêu cầu bổ sung', procedureComponents: [],
          onSubmit: async (p) => { await api.post(`/admin/applications/${item.id}/supplement-requests`, p); onRefresh(); },
        });
      }));
    }
    if (sid === 9) {
      items.push(createMenuItem('Trả kết quả', '#15803d', () => {
        openStaffActionModal({
          action: 'deliver', actionLabel: 'Trả kết quả', procedureComponents: [],
          onSubmit: async (p) => { await api.post(`/admin/applications/${item.id}/deliver`, p); onRefresh(); },
        });
      }));
    }
    items.push(
      createMenuItem('Xem chi tiết', '#334155', () => navigate(`/can-bo/ho-so/${item.id}`)),
      createMenuItem('Xem lịch sử cập nhật', '#334155', () => navigate(`/can-bo/ho-so/${item.id}#audit`)),
      createMenuItem('Tải văn bản hồ sơ', '#334155', () => navigate(`/can-bo/ho-so/${item.id}?tab=dossier`)),
    );
  } else if (role === 'leader') {
    items = [
      createMenuItem('Xem & Phê duyệt', '#15803d', () => navigate(`/can-bo/ho-so/${item.id}`)),
      createMenuItem('Yêu cầu xử lý lại', '#b91c1c', () => {
        openStaffActionModal({
          action: 'rework', actionLabel: 'Yêu cầu xử lý lại', procedureComponents: [],
          onSubmit: async (p) => { await api.post(`/admin/applications/${item.id}/rework`, p); onRefresh(); },
        });
      }),
      createMenuItem('Xem lịch sử cập nhật', '#334155', () => navigate(`/can-bo/ho-so/${item.id}#audit`)),
      createMenuItem('Tải văn bản hồ sơ', '#334155', () => navigate(`/can-bo/ho-so/${item.id}?tab=dossier`)),
    ];
  } else {
    items = [
      createMenuItem('Xử lý hồ sơ', '#004482', () => navigate(`/can-bo/ho-so/${item.id}`)),
      createMenuItem('Tiếp nhận hồ sơ', '#004482', () => {
        openStaffActionModal({ action: 'accept', actionLabel: 'Tiếp nhận hồ sơ', procedureComponents: [], onSubmit: async (p) => { await api.post(`/admin/applications/${item.id}/accept`, p); onRefresh(); } });
      }),
      createMenuItem('Từ chối hồ sơ', '#b91c1c', () => {
        openStaffActionModal({ action: 'reject', actionLabel: 'Từ chối tiếp nhận', procedureComponents: [], onSubmit: async (p) => { await api.post(`/admin/applications/${item.id}/reject`, p); onRefresh(); } });
      }),
      createMenuItem('Yêu cầu bổ sung', '#b45309', () => {
        openStaffActionModal({ action: 'requestSupplement', actionLabel: 'Yêu cầu bổ sung', procedureComponents: [], onSubmit: async (p) => { await api.post(`/admin/applications/${item.id}/supplement-requests`, p); onRefresh(); } });
      }),
      createMenuItem('Dừng xử lý', '#d97706', () => {
        openStaffActionModal({ action: 'rework', actionLabel: 'Dừng xử lý', procedureComponents: [], onSubmit: async (p) => { await api.post(`/admin/applications/${item.id}/rework`, p); onRefresh(); } });
      }),
      createMenuItem('Xem lịch sử cập nhật', '#334155', () => navigate(`/can-bo/ho-so/${item.id}#audit`)),
      createMenuItem('Tải văn bản hồ sơ', '#334155', () => navigate(`/can-bo/ho-so/${item.id}?tab=dossier`)),
    ];
  }

  menu.replaceChildren(...items);

  const triggerBtn = el('button', {
    type: 'button',
    style: 'background: #ffffff; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0.2rem 0.55rem; font-size: 13px; font-weight: 800; color: #475569; cursor: pointer; line-height: 1; letter-spacing: 1px;',
    onClick: (e) => {
      e.stopPropagation();
      if (isOpen) closeNow();
      else openMenu();
    },
  }, '...');

  const wrapper = el('div', {
    style: 'position: relative; display: inline-block;',
  }, [triggerBtn, menu]);

  wrapper.addEventListener('mouseenter', openMenu);
  wrapper.addEventListener('mouseleave', scheduleClose);
  menu.addEventListener('mouseenter', openMenu);
  menu.addEventListener('mouseleave', scheduleClose);

  return wrapper;
}
