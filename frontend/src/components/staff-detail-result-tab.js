import { el } from './dom.js';
import { api, getStoredUser } from '../api/client.js';
import { normalizeStaffRole } from './staff-nav.js';
import { showToast } from './toast.js';
import { openFilePreviewModal } from './file-preview-modal.js';

function field(label, val, filled) {
  return el('div', { style: 'display: flex; flex-direction: column; gap: 0.35rem;' }, [
    el('label', { style: `font-size: 11.5px; font-weight: 600; color: ${filled ? '#0f172a' : '#94a3b8'};` }, label),
    el('div', {
      style: `background: ${filled ? '#ffffff' : '#f8fafc'}; border: 1px ${filled ? 'solid #cbd5e1' : 'dashed #cbd5e1'}; border-radius: 4px; padding: 0.45rem 0.75rem; font-size: 13px; color: ${filled ? '#0f172a' : '#94a3b8'}; font-style: ${filled ? 'normal' : 'italic'};`,
    }, filled ? String(val) : '(Chưa có thông tin)'),
  ]);
}

function section(num, title, children, rightAction) {
  return el('div', { class: 'card', style: 'padding: 1.5rem; background: #ffffff; border: 1px solid #d0d7de;' }, [
    el('div', { style: 'display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; padding-bottom: 0.65rem; border-bottom: 1px solid #f1f5f9; flex-wrap: wrap; gap: 0.5rem;' }, [
      el('div', { style: 'display: flex; align-items: center; gap: 0.5rem;' }, [
        el('span', { style: 'display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; background: #e0f2fe; color: #0369a1; font-weight: 800; font-size: 12px; border-radius: 3px;' }, String(num)),
        el('span', { style: 'font-size: 14px; font-weight: 700; color: #004482;' }, title),
      ]),
      rightAction || null,
    ]),
    ...children,
  ]);
}

export function renderStaffResultTab(app, onRefresh) {
  const role = normalizeStaffRole(getStoredUser()?.vaiTro || getStoredUser()?.role);
  const isLeader = role === 'leader';
  const rawData = app.data || {};
  const payload = (rawData.payload && typeof rawData.payload === 'object') ? rawData.payload : rawData;

  const deliveryMethod = app.delivery_method || payload.delivery_method || '';
  const dueAt = app.due_at ? app.due_at.split('T')[0] : '';
  const deliveredAt = app.delivered_at ? app.delivered_at.split('T')[0] : '';
  const address = payload.dia_chi_nhan || payload.delivery_address || '';
  const resultFiles = Array.isArray(app.result_files) ? app.result_files : [];

  const deliveryGrid = el('div', { style: 'display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 0.85rem;' }, [
    field('Hình thức nhận kết quả', deliveryMethod, !!deliveryMethod),
    field('Ngày hẹn trả kết quả', dueAt, !!dueAt),
    field('Ngày trả thực tế', deliveredAt, !!deliveredAt),
    field('Địa chỉ nhận (bưu chính)', address, !!address),
  ]);

  const uploadInput = el('input', { type: 'file', accept: '.pdf,.png,.jpg,.jpeg', style: 'display: none;' });
  uploadInput.addEventListener('change', async () => {
    if (!uploadInput.files?.length) return;
    const file = uploadInput.files[0];
    const fd = new FormData();
    fd.append('file', file);
    try {
      await api.post(`/admin/applications/${app.id}/result-files`, fd);
      const msg = `Lãnh đạo đã tải lên tệp kết quả xử lý: "${file.name}"`;
      await api.post(`/admin/applications/${app.id}/comments`, { content: msg, comment: msg }).catch(() => {});
      showToast.success(`Đã tải lên văn bản kết quả "${file.name}".`);
      if (typeof onRefresh === 'function') onRefresh();
    } catch (err) {
      showToast.error(err.message || 'Lỗi khi tải lên tệp kết quả.');
    }
  });

  const uploadBtn = isLeader ? el('button', {
    type: 'button', class: 'btn btn-primary btn-sm',
    style: 'font-size: 11.5px; font-weight: 700; background: #004482; color: #ffffff; padding: 0.25rem 0.65rem; border: none; border-radius: 4px; cursor: pointer;',
    onClick: () => uploadInput.click(),
  }, '+ Tải lên văn bản kết quả') : null;

  const filesBlock = resultFiles.length > 0
    ? el('div', { style: 'display: flex; flex-direction: column; gap: 0.5rem;' },
        resultFiles.map(rf => el('div', { style: 'display: flex; justify-content: space-between; align-items: center; padding: 0.65rem 0.85rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 4px; flex-wrap: wrap; gap: 0.5rem;' }, [
          el('div', {}, [
            el('strong', { style: 'font-size: 13px; color: #166534;' }, rf.name || 'Văn bản kết quả điện tử'),
            rf.signed ? el('span', { style: 'margin-left: 0.5rem; font-size: 11px; background: #dcfce7; color: #166534; padding: 0.1rem 0.4rem; border-radius: 3px;' }, 'Đã ký số') : null,
          ]),
          el('div', { style: 'display: flex; gap: 0.4rem; align-items: center;' }, [
            rf.download_url ? el('button', {
              type: 'button', class: 'btn btn-secondary btn-sm',
              style: 'font-size: 11.5px; font-weight: 700; background: #ffffff; color: #004482; border: 1px solid #cbd5e1; padding: 0.25rem 0.65rem;',
              onClick: () => openFilePreviewModal({ url: rf.download_url, name: rf.name }),
            }, 'Xem trước') : null,
            rf.download_url ? el('a', {
              href: rf.download_url, target: '_blank',
              style: 'font-size: 11.5px; font-weight: 700; text-decoration: none; background: #dcfce7; color: #166534; border: 1px solid #86efac; padding: 0.25rem 0.65rem; border-radius: 4px;',
            }, 'Tải về') : null,
            isLeader ? el('button', {
              type: 'button', class: 'btn btn-sm',
              style: 'font-size: 11.5px; font-weight: 700; background: #ffffff; color: #b91c1c; border: 1px solid #fca5a5; padding: 0.25rem 0.65rem;',
              onClick: async () => {
                if (!confirm(`Xác nhận hủy văn bản kết quả "${rf.name || 'này'}"?`)) return;
                try {
                  await api.delete(`/admin/applications/${app.id}/result-files/${rf.id}`);
                  const msg = `Lãnh đạo đã hủy tệp kết quả xử lý: "${rf.name || rf.id}"`;
                  await api.post(`/admin/applications/${app.id}/comments`, { content: msg, comment: msg }).catch(() => {});
                  showToast.success('Đã hủy tệp kết quả xử lý.');
                  if (typeof onRefresh === 'function') onRefresh();
                } catch (err) {
                  showToast.error(err.message || 'Lỗi khi hủy tệp kết quả.');
                }
              },
            }, 'Hủy') : null,
          ]),
        ]))
      )
    : el('div', {
        style: 'padding: 1.5rem; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 4px; text-align: center; color: #64748b; font-size: 13px;',
      }, Number(app.status?.id) === 10
        ? 'Hồ sơ đã được trả kết quả. Liên hệ Bộ phận Một cửa để tra cứu văn bản điện tử.'
        : 'Chưa có tệp kết quả điện tử. Sẽ được đăng tải sau khi hoàn tất phê duyệt và số hóa.');

  const container = el('div', { style: 'display: flex; flex-direction: column; gap: 1.25rem;' });
  container.append(
    section(1, 'Hình thức nhận kết quả', [deliveryGrid]),
    section(2, 'Văn bản & Tệp kết quả điện tử', [uploadInput, filesBlock], uploadBtn),
  );
  return container;
}
