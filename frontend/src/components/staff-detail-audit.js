import { el } from './dom.js';
import { api, getAuthToken, getStoredUser } from '../api/client.js';
import { normalizeStaffRole } from './staff-nav.js';
import { showToast } from './toast.js';
import { openFilePreviewModal } from './file-preview-modal.js';
import { openStaffOpinionModal } from './staff-opinion-modal.js';
import { renderStaffTimelineLedger } from './staff-detail-timeline.js';

function createMenu(f, app, onRefresh, resultFiles = []) {
  let timer = null;
  const menu = el('div', {
    style: 'position: absolute; right: 0; top: 100%; width: 185px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; box-shadow: 0 10px 25px -5px rgba(15,23,42,0.12); z-index: 1000; display: none; padding: 0.25rem 0;',
  });
  const show = () => { if (timer) clearTimeout(timer); menu.style.display = 'block'; };
  const hide = () => { if (timer) clearTimeout(timer); timer = setTimeout(() => { menu.style.display = 'none'; }, 200); };

  function item(label, color, fn) {
    const b = el('button', {
      type: 'button',
      style: `width: 100%; text-align: left; padding: 0.45rem 0.75rem; background: none; border: none; font-size: 12px; color: ${color}; font-weight: 600; cursor: pointer; display: block;`,
      onClick: (e) => { e.stopPropagation(); menu.style.display = 'none'; fn(); },
    }, label);
    b.onmouseenter = () => { b.style.background = '#f8fafc'; };
    b.onmouseleave = () => { b.style.background = 'none'; };
    return b;
  }

  const matchedResult = (resultFiles || []).find(rf => rf.name === f.name);

  async function approveFile() {
    if (!confirm(`Xác nhận duyệt tệp "${f.name}" và chuyển sang Kết quả xử lý?`)) return;
    try {
      const res = await fetch(f.download_url, { headers: { Authorization: `Bearer ${getAuthToken()}` } });
      const blob = await res.blob();
      const fd = new FormData();
      fd.append('file', blob, f.name || 'ket_qua_duyet.pdf');
      await api.post(`/admin/applications/${app.id}/result-files`, fd);
      const c1 = `Lãnh đạo đã duyệt văn bản: "${f.name}" và chuyển vào Kết quả xử lý.`;
      await api.post(`/admin/applications/${app.id}/comments`, { content: c1, comment: c1 }).catch(() => {});
      showToast.success(`Đã duyệt tệp "${f.name}". Tệp đã chuyển sang Kết quả xử lý.`);
      if (typeof onRefresh === 'function') onRefresh();
    } catch (err) { showToast.error(`Lỗi duyệt tệp: ${err.message}`); }
  }

  async function revertApprove() {
    if (!confirm(`Xác nhận hoàn lại thao tác duyệt văn bản "${f.name}"? Tệp sẽ được gỡ khỏi Kết quả xử lý.`)) return;
    try {
      if (matchedResult?.id) {
        await api.delete(`/admin/applications/${app.id}/result-files/${matchedResult.id}`);
      }
      const c = `Lãnh đạo đã hoàn lại thao tác duyệt văn bản: "${f.name}".`;
      await api.post(`/admin/applications/${app.id}/comments`, { content: c, comment: c }).catch(() => {});
      showToast.info(`Đã hoàn lại thao tác duyệt văn bản "${f.name}".`);
      if (typeof onRefresh === 'function') onRefresh();
    } catch (err) { showToast.error(`Lỗi hoàn lại thao tác: ${err.message}`); }
  }

  async function reworkFile() {
    const note = prompt('Nhập lý do / yêu cầu cán bộ thụ lý xem xét lại:');
    if (!note) return;
    try {
      await api.post(`/admin/applications/${app.id}/rework`, { note });
      const c2 = `Lãnh đạo yêu cầu xử lý lại: "${note}"`;
      await api.post(`/admin/applications/${app.id}/comments`, { content: c2, comment: c2 }).catch(() => {});
      showToast.warning('Đã gửi yêu cầu xử lý lại hồ sơ cho cán bộ thụ lý.');
      if (typeof onRefresh === 'function') onRefresh();
    } catch (err) { showToast.error(`Lỗi yêu cầu xử lý lại: ${err.message}`); }
  }

  async function deleteFile() {
    if (!confirm(`Xác nhận xóa tệp "${f.name}"?`)) return;
    try {
      await api.delete(`/admin/applications/${app.id}/opinion-files/${f.id}`);
      const c3 = `Đã xóa tệp ý kiến: "${f.name}"`;
      await api.post(`/admin/applications/${app.id}/comments`, { content: c3, comment: c3 }).catch(() => {});
      showToast.success(`Đã xóa tệp "${f.name}".`);
      if (typeof onRefresh === 'function') onRefresh();
    } catch (err) { showToast.error(`Lỗi xóa tệp: ${err.message}`); }
  }

  const role = normalizeStaffRole(getStoredUser()?.vaiTro || getStoredUser()?.role);
  const isLeader = role === 'leader';
  const isOneStop = role === 'one-stop';
  const menuItems = [
    item('Xem tệp / ảnh', '#004b87', () => openFilePreviewModal({ url: f.download_url, name: f.name })),
  ];
  if (isLeader) {
    if (matchedResult) {
      menuItems.push(item('Hoàn lại thao tác duyệt', '#e11d48', revertApprove));
    } else {
      menuItems.push(item('Duyệt văn bản', '#16a34a', approveFile));
    }
    menuItems.push(item('Yêu cầu xử lý lại', '#b91c1c', reworkFile));
  }
  menuItems.push(item('Tải xuống', '#334155', () => window.open(f.download_url, '_blank')));
  if (!isOneStop && !isLeader) {
    menuItems.push(item('Xóa tệp', '#dc2626', deleteFile));
  }
  menu.replaceChildren(...menuItems);

  const btn = el('button', {
    type: 'button', style: 'background: #ffffff; border: 1px solid #e2e8f0; border-radius: 4px; padding: 0.15rem 0.45rem; font-size: 12px; font-weight: 800; color: #475569; cursor: pointer; line-height: 1;',
    onClick: (e) => { e.stopPropagation(); menu.style.display = menu.style.display === 'block' ? 'none' : 'block'; },
  }, '...');

  const w = el('div', { style: 'position: relative; display: inline-block;', onmouseenter: show, onmouseleave: hide }, [btn, menu]);
  menu.onmouseenter = show; menu.onmouseleave = hide;
  document.addEventListener('click', () => { menu.style.display = 'none'; });
  return w;
}

function uploadIcon() {
  const span = document.createElement('span');
  span.style.display = 'inline-flex';
  span.style.alignItems = 'center';
  span.innerHTML = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>`;
  return span;
}

export function renderStaffAuditSection(app, onRefresh) {
  const user = getStoredUser();
  const role = normalizeStaffRole(user?.vaiTro || user?.role);
  const isLeader = role === 'leader';
  const resultFiles = Array.isArray(app.result_files) ? app.result_files : [];
  const timelineItems = Array.isArray(app.timeline) ? app.timeline : [];

  const opinionFilesList = el('div', { style: 'display: flex; flex-direction: column; gap: 0.45rem; margin-top: 0.5rem;' });

  async function loadOpinionFiles() {
    opinionFilesList.replaceChildren(el('span', { style: 'font-size: 12px; color: #64748b;' }, 'Đang tải tệp...'));
    try {
      const res = await api.get(`/admin/applications/${app.id}/opinion-files`);
      const list = res?.data || [];
      if (!list.length) {
        opinionFilesList.replaceChildren(el('div', {
          style: 'padding: 0.65rem 0.85rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; text-align: center; color: #64748b; font-size: 12px;',
        }, 'Chưa có tệp đính kèm.'));
        return;
      }
      const fileRows = list.map(f => {
        const isImg = f.mime_type?.startsWith('image/') || /\.(png|jpe?g|webp)$/i.test(f.name || '');
        return el('div', { style: 'display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0.75rem; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px;' }, [
          el('div', { style: 'display: flex; align-items: center; gap: 0.55rem;' }, [
            el('span', { style: `font-size: 10px; font-weight: 800; padding: 0.15rem 0.4rem; border-radius: 3px; ${isImg ? 'background: #eff6ff; color: #004b87;' : 'background: #f1f5f9; color: #475569;'}` }, isImg ? 'ẢNH' : 'TỆP'),
            el('div', {}, [
              el('a', { href: f.download_url, target: '_blank', style: 'font-size: 12.5px; font-weight: 600; color: #004b87; text-decoration: none;' }, f.name || 'Tep_dinh_kem.pdf'),
              el('div', { style: 'font-size: 11px; color: #64748b;' }, f.size ? `${(f.size / 1024).toFixed(1)} KB` : ''),
            ]),
          ]),
          createMenu(f, app, onRefresh, resultFiles),
        ]);
      });
      opinionFilesList.replaceChildren(...fileRows);
    } catch (_) {
      opinionFilesList.replaceChildren(el('div', { style: 'font-size: 12px; color: #64748b;' }, 'Chưa có tệp đính kèm.'));
    }
  }

  async function revertResultFile(rf) {
    if (!confirm(`Xác nhận hoàn lại thao tác duyệt văn bản "${rf.name}"? Tệp sẽ được gỡ khỏi Kết quả xử lý.`)) return;
    try {
      await api.delete(`/admin/applications/${app.id}/result-files/${rf.id}`);
      const c = `Lãnh đạo đã hoàn lại thao tác duyệt văn bản: "${rf.name}".`;
      await api.post(`/admin/applications/${app.id}/comments`, { content: c, comment: c }).catch(() => {});
      showToast.info(`Đã hoàn lại thao tác duyệt văn bản "${rf.name}".`);
      if (typeof onRefresh === 'function') onRefresh();
    } catch (err) { showToast.error(`Lỗi hoàn lại thao tác: ${err.message}`); }
  }

  const hasOpinion = Boolean(app.processing_opinion?.trim());
  const opinionBox = hasOpinion ? el('div', {
    style: 'background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 0.75rem 0.85rem; font-size: 12.5px; line-height: 1.5; color: #0f172a; white-space: pre-wrap;',
  }, app.processing_opinion) : null;

  const canManageOpinion = role === 'case-officer' || role === 'administrator';
  const opinionModalBtn = canManageOpinion ? el('button', {
    type: 'button',
    title: 'Soạn ý kiến & Tải lên tệp',
    style: 'background: #004b87; color: #ffffff; border: none; padding: 0.35rem 0.65rem; border-radius: 4px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 0.35rem; font-size: 12px; font-weight: 700;',
    onClick: () => openStaffOpinionModal({
      app,
      onSaved: () => {
        loadOpinionFiles();
        if (typeof onRefresh === 'function') onRefresh();
      },
    }),
  }, [uploadIcon(), el('span', {}, 'Tải lên')]) : null;

  const opinionCard = el('div', {
    class: 'staff-audit-opinion-box',
    style: 'background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 1px 3px rgba(15,23,42,0.04); padding: 1.15rem 1.25rem; display: flex; flex-direction: column; gap: 0.75rem;',
  }, [
    el('div', { style: 'display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.65rem;' }, [
      el('h4', { style: 'font-family: var(--font-heading); font-size: 13.5px; font-weight: 800; color: rgb(0, 68, 130); margin: 0;' }, 'Ý kiến xử lý'),
      opinionModalBtn,
    ]),
    opinionBox,
    el('div', {}, [
      opinionBox ? el('div', { style: 'font-size: 11.5px; font-weight: 700; color: #0f172a; margin-bottom: 0.35rem;' }, 'Tệp đính kèm:') : null,
      opinionFilesList,
    ]),
  ]);

  const resultCard = el('div', {
    class: 'staff-audit-result-box',
    style: 'background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 1px 3px rgba(15,23,42,0.04); padding: 1.15rem 1.25rem; display: flex; flex-direction: column; gap: 0.75rem;',
  }, [
    el('div', { style: 'border-bottom: 1px solid #e2e8f0; padding-bottom: 0.65rem;' }, [
      el('h4', { style: 'font-family: var(--font-heading); font-size: 13.5px; font-weight: 800; color: rgb(0, 68, 130); margin: 0;' }, 'Kết quả xử lý'),
    ]),
    resultFiles.length > 0 ? el('div', { style: 'display: flex; flex-direction: column; gap: 0.45rem;' },
      resultFiles.map(rf => el('div', { style: 'display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0.75rem; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px;' }, [
        el('div', {}, [
          el('strong', { style: 'color: #0f172a; font-size: 12.5px; display: block;' }, rf.name || 'Van_ban_ket_qua.pdf'),
          el('span', { style: 'font-size: 11px; color: #64748b;' }, rf.size ? `${(rf.size / 1024).toFixed(1)} KB` : ''),
        ]),
        el('div', { style: 'display: flex; gap: 0.35rem; align-items: center;' }, [
          rf.download_url ? el('button', { type: 'button', style: 'padding: 0.3rem 0.65rem; font-size: 11.5px; font-weight: 700; background: #004b87; color: #ffffff; border: none; border-radius: 4px; cursor: pointer;', onClick: () => openFilePreviewModal({ url: rf.download_url, name: rf.name }) }, 'Xem trước') : null,
          rf.download_url ? el('a', { href: rf.download_url, target: '_blank', style: 'padding: 0.3rem 0.65rem; font-size: 11.5px; font-weight: 700; background: #004b87; color: #ffffff; border: none; border-radius: 4px; text-decoration: none; cursor: pointer;' }, 'Tải xuống') : null,
          isLeader ? el('button', {
            type: 'button',
            style: 'padding: 0.3rem 0.65rem; font-size: 11.5px; font-weight: 700; background: #fff1f2; color: #e11d48; border: 1px solid #fecdd3; border-radius: 4px; cursor: pointer;',
            title: 'Hoàn lại thao tác duyệt văn bản này',
            onClick: () => revertResultFile(rf),
          }, 'Hoàn lại thao tác') : null,
        ]),
      ]))
    ) : el('div', { style: 'padding: 1.5rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 12px; color: #64748b; text-align: center;' }, 'Chưa có kết quả xử lý.'),
  ]);

  loadOpinionFiles();

  return el('div', { style: 'margin-top: 1.25rem;' }, [
    el('div', { style: 'display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 1rem; margin-top: 0.75rem;' }, [opinionCard, resultCard]),
    renderStaffTimelineLedger(timelineItems),
  ]);
}
