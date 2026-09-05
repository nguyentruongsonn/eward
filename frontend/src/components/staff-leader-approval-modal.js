import { el } from './dom.js';
import { api } from '../api/client.js';
import { createWordEditor } from './word-editor.js';

let activeModal = null;

export function openStaffLeaderApprovalModal({ app, onSubmitted }) {
  if (activeModal) {
    activeModal.remove();
    activeModal = null;
  }

  const backdrop = el('div', {
    class: 'modal-backdrop',
    style: 'position: fixed; inset: 0; background: rgba(15, 23, 42, 0.65); z-index: 1050; display: flex; align-items: center; justify-content: center; padding: 1rem;',
  });

  const onKey = (e) => { if (e.key === 'Escape') close(); };
  const close = () => {
    window.removeEventListener('keydown', onKey);
    backdrop.remove();
    activeModal = null;
  };
  window.addEventListener('keydown', onKey);

  const errorAlert = el('div', {
    style: 'display: none; padding: 0.5rem 0.75rem; background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; border-radius: 4px; font-size: 12px; margin-bottom: 0.75rem;',
  });

  const editor = createWordEditor({
    placeholder: 'Nhập nội dung ý kiến phê duyệt, kết luận thẩm định và dặn dò bàn giao kết quả...',
    minHeight: '130px',
  });

  const cancelBtn = el('button', {
    type: 'button',
    class: 'btn btn-secondary btn-sm',
    style: 'height: 34px; font-size: 12.5px; padding: 0 1rem; border: 1px solid #cbd5e1; color: #334155; font-weight: 600;',
    onClick: close,
  }, 'Hủy bỏ');

  const confirmBtn = el('button', {
    type: 'button',
    class: 'btn btn-primary btn-sm',
    style: 'background: #004482; color: #ffffff; font-weight: 700; height: 34px; font-size: 12.5px; padding: 0 1.25rem; border: none;',
    onClick: async () => {
      errorAlert.style.display = 'none';
      confirmBtn.disabled = true;
      confirmBtn.textContent = 'Đang phê duyệt...';
      try {
        const comment = editor.getText().trim();
        await api.post(`/admin/applications/${app.id}/approve`, {
          approval_comment: comment || 'Lãnh đạo đã phê duyệt hồ sơ và chuyển kết quả về Bộ phận Một cửa.',
          note: comment || 'Lãnh đạo phê duyệt hoàn thành hồ sơ.',
        });
        close();
        if (onSubmitted) onSubmitted();
      } catch (err) {
        errorAlert.textContent = err.message || 'Lỗi khi phê duyệt hồ sơ.';
        errorAlert.style.display = 'block';
        confirmBtn.disabled = false;
        confirmBtn.textContent = 'Xác nhận';
      }
    },
  }, 'Xác nhận');

  const card = el('div', {
    class: 'card',
    style: 'width: 100%; max-width: 620px; background: #ffffff; border-radius: 6px; padding: 1.25rem 1.5rem; box-shadow: 0 16px 40px rgba(0,0,0,0.25); max-height: 90vh; overflow-y: auto;',
    onClick: (e) => e.stopPropagation(),
  }, [
    el('div', { style: 'display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.5rem;' }, [
      el('h3', { style: 'font-size: 15px; font-weight: 800; color: #004482; margin: 0; text-transform: uppercase;' }, 'Trả kết quả cán bộ một cửa'),
      el('button', { type: 'button', style: 'border: none; background: none; font-size: 20px; color: #64748b; cursor: pointer; padding: 0.25rem;', onClick: close }, '✕'),
    ]),
    errorAlert,
    el('div', { style: 'margin-bottom: 1rem;' }, [
      el('label', { style: 'display: block; font-size: 12px; font-weight: 700; color: #0f172a; margin-bottom: 0.35rem;' }, 'Ý KIẾN PHÊ DUYỆT / BÌNH LUẬN (SOẠN THẢO VĂN BẢN):'),
      editor.wrapper,
    ]),
    el('div', { style: 'display: flex; justify-content: flex-end; gap: 0.6rem; border-top: 1px solid #f1f5f9; padding-top: 0.75rem;' }, [
      cancelBtn,
      confirmBtn,
    ]),
  ]);

  backdrop.append(card);
  backdrop.addEventListener('click', close);
  document.body.append(backdrop);
  activeModal = backdrop;
}
