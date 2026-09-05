import { el } from './dom.js';
import { api } from '../api/client.js';
import { createWordEditor } from './word-editor.js';
import { showToast } from './toast.js';

let activeOpinionModal = null;

export function openStaffOpinionModal({ app, onSaved }) {
  if (activeOpinionModal) {
    activeOpinionModal.remove();
    activeOpinionModal = null;
  }

  const editor = createWordEditor({
    placeholder: 'Cán bộ thụ lý ghi chú nội dung ý kiến, căn cứ pháp lý thẩm định hồ sơ...',
    initialContent: app.processing_opinion || '',
    minHeight: '130px',
  });

  const uploadInput = el('input', {
    type: 'file',
    accept: '.pdf,.png,.jpg,.jpeg,.doc,.docx',
    style: 'display: none;',
  });

  const filesContainer = el('div', {
    style: 'display: flex; flex-direction: column; gap: 0.4rem; max-height: 160px; overflow-y: auto; margin-top: 0.4rem;',
  });

  const uploadStatus = el('span', {
    style: 'font-size: 11.5px; color: #64748b; font-style: italic;',
  });

  async function loadModalFiles() {
    filesContainer.replaceChildren(el('span', { style: 'font-size: 12px; color: #64748b; font-style: italic;' }, 'Đang tải danh sách tệp...'));
    try {
      const res = await api.get(`/admin/applications/${app.id}/opinion-files`);
      const list = res?.data || [];
      if (!list.length) {
        filesContainer.replaceChildren(el('div', {
          style: 'padding: 0.65rem 0.75rem; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 4px; font-size: 12px; color: #64748b; text-align: center;',
        }, 'Chưa có tệp / ảnh đính kèm. Bấm nút "+ Tải lên tệp / ảnh" bên trên để tải.'));
        return;
      }
      filesContainer.replaceChildren(...list.map(f => {
        const isImg = f.mime_type?.startsWith('image/') || /\.(png|jpe?g|webp)$/i.test(f.name || '');
        return el('div', {
          style: 'display: flex; justify-content: space-between; align-items: center; padding: 0.4rem 0.65rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px;',
        }, [
          el('div', { style: 'display: flex; align-items: center; gap: 0.45rem; overflow: hidden;' }, [
            el('span', {
              style: `font-size: 10px; font-weight: 700; padding: 0.1rem 0.35rem; border-radius: 2px; ${isImg ? 'background: #e0f2fe; color: #0369a1;' : 'background: #fef3c7; color: #92400e;'}`
            }, isImg ? 'ẢNH' : 'TỆP'),
            el('a', {
              href: f.download_url,
              target: '_blank',
              style: 'font-size: 12px; font-weight: 600; color: #004b87; text-decoration: none; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;',
            }, f.name || 'Tệp ý kiến'),
            el('span', { style: 'font-size: 11px; color: #94a3b8;' }, f.size ? `(${(f.size / 1024).toFixed(1)} KB)` : ''),
          ]),
          el('div', { style: 'display: flex; gap: 0.35rem; align-items: center;' }, [
            el('a', {
              href: f.download_url,
              target: '_blank',
              class: 'btn btn-secondary btn-sm',
              style: 'padding: 0.15rem 0.5rem; font-size: 11px;',
            }, 'Xem'),
            el('button', {
              type: 'button',
              class: 'btn btn-secondary btn-sm',
              style: 'padding: 0.15rem 0.5rem; font-size: 11px; color: #b91c1c; border-color: #fca5a5;',
              onClick: async () => {
                if (!confirm(`Xóa tệp "${f.name}"?`)) return;
                try {
                  await api.delete(`/admin/applications/${app.id}/opinion-files/${f.id}`);
                  loadModalFiles();
                  if (typeof onSaved === 'function') onSaved();
                  showToast.success(`Đã xóa tệp "${f.name}".`);
                } catch (err) { showToast.error(`Lỗi xóa tệp: ${err.message}`); }
              },
            }, 'Xóa'),
          ]),
        ]);
      }));
    } catch (_) {
      filesContainer.replaceChildren(el('div', { style: 'font-size: 12px; color: #64748b;' }, 'Không thể nạp tệp ý kiến.'));
    }
  }

  uploadInput.addEventListener('change', async () => {
    if (!uploadInput.files?.length) return;
    const file = uploadInput.files[0];
    const fd = new FormData();
    fd.append('file', file);
    uploadStatus.textContent = `Đang tải lên "${file.name}"...`;
    try {
      await api.post(`/admin/applications/${app.id}/opinion-files`, fd);
      uploadStatus.textContent = `Đã tải lên "${file.name}".`;
      uploadInput.value = '';
      loadModalFiles();
      if (typeof onSaved === 'function') onSaved();
    } catch (err) {
      uploadStatus.textContent = `Lỗi tải tệp: ${err.message}`;
    }
  });

  const uploadBtn = el('button', {
    type: 'button',
    class: 'btn btn-secondary btn-sm',
    style: 'display: inline-flex; align-items: center; gap: 0.35rem; font-size: 12px; font-weight: 700; color: #004b87; border: 1px solid #cbd5e1; height: 30px; padding: 0 0.75rem; background: #f0f7ff;',
    onClick: () => uploadInput.click(),
  }, [
    el('span', { style: 'font-size: 14px; font-weight: 800;' }, '+'),
    el('span', {}, 'Tải lên tệp / ảnh'),
  ]);

  const fileSection = el('div', {
    style: 'margin-top: 1rem; border-top: 1px solid #e2e8f0; padding-top: 0.75rem;',
  }, [
    el('div', { style: 'display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;' }, [
      el('label', { style: 'font-size: 12px; font-weight: 700; color: #004b87; text-transform: uppercase;' }, 'Tệp / Ảnh ý kiến đính kèm:'),
      uploadBtn,
    ]),
    uploadInput,
    uploadStatus,
    filesContainer,
  ]);

  const backdrop = el('div', {
    class: 'modal-backdrop',
    style: 'position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 1050; display: flex; align-items: center; justify-content: center; padding: 1rem;',
  });

  const onKey = (e) => { if (e.key === 'Escape') close(); };
  const close = () => {
    window.removeEventListener('keydown', onKey);
    backdrop.remove();
    activeOpinionModal = null;
  };
  window.addEventListener('keydown', onKey);
  backdrop.addEventListener('click', close);

  const saveBtn = el('button', {
    type: 'button',
    class: 'btn btn-primary btn-sm',
    style: 'background: #004b87; font-weight: 700; height: 34px; padding: 0 1.25rem;',
    onClick: async () => {
      saveBtn.disabled = true;
      saveBtn.textContent = 'Đang lưu...';
      try {
        const text = editor.getText();
        await api.post(`/admin/applications/${app.id}/opinion`, { content: text, opinion: text });
        app.processing_opinion = text;
        showToast.success('Đã lưu ý kiến xử lý thành công.');
        close();
        if (onSaved) onSaved();
      } catch (err) {
        showToast.error(`Lỗi lưu ý kiến: ${err.message}`);
        saveBtn.disabled = false;
        saveBtn.textContent = 'Lưu ý kiến xử lý';
      }
    },
  }, 'Lưu ý kiến xử lý');

  const card = el('div', {
    class: 'card',
    style: 'width: 100%; max-width: 760px; background: #ffffff; border-radius: 6px; padding: 1.5rem; box-shadow: 0 12px 32px rgba(0,0,0,0.25); max-height: 90vh; overflow-y: auto;',
    onClick: (e) => e.stopPropagation(),
  }, [
    el('div', { style: 'display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.65rem; margin-bottom: 1rem;' }, [
      el('h3', { style: 'font-size: 15px; font-weight: 800; color: #004b87; margin: 0; text-transform: uppercase;' }, 'Ý kiến xử lý & Tệp đính kèm'),
      el('button', { type: 'button', style: 'border: none; background: none; font-size: 18px; color: #64748b; cursor: pointer;', onClick: close }, '✕'),
    ]),
    el('div', { style: 'margin-bottom: 0.35rem;' }, [
      el('label', { style: 'font-size: 12px; font-weight: 700; color: #004b87; text-transform: uppercase; display: block; margin-bottom: 0.35rem;' }, 'Nội dung ý kiến thẩm định:'),
      editor.wrapper,
    ]),
    fileSection,
    el('div', { style: 'display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1.25rem; border-top: 1px solid #e2e8f0; padding-top: 0.75rem;' }, [
      el('button', { type: 'button', class: 'btn btn-secondary btn-sm', style: 'height: 34px;', onClick: close }, 'Đóng'),
      saveBtn,
    ]),
  ]);

  backdrop.append(card);
  document.body.append(backdrop);
  activeOpinionModal = backdrop;
  loadModalFiles();
}

