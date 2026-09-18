import { el } from './dom.js';
import { api } from '../api/client.js';
import { createWordEditor } from './word-editor.js';

let activeModal = null;

export function openStaffCompletionModal({ app, onSubmitted }) {
  if (activeModal) {
    activeModal.remove();
    activeModal = null;
  }

  const backdrop = el('div', {
    style: 'position: fixed; inset: 0; background: rgba(15, 23, 42, 0.65); z-index: 1050; display: flex; align-items: center; justify-content: center; padding: 1rem;',
  });

  const errorAlert = el('div', {
    style: 'display: none; padding: 0.5rem 0.75rem; background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; border-radius: 4px; font-size: 12px; margin-bottom: 0.75rem;',
  });

  const unitSelect = el('select', {
    class: 'input',
    style: 'width: 100%; height: 34px; font-size: 12.5px; margin-bottom: 0.75rem;',
  }, [
    el('option', { value: 'ubnd' }, 'Lãnh đạo UBND Xã/Phường (Phê duyệt)'),
    el('option', { value: 'mot-cua' }, 'Bộ phận Tiếp nhận & Trả kết quả (Một cửa)'),
    el('option', { value: 'chuyen-mon' }, 'Phòng chuyên môn / Phối hợp thẩm định'),
  ]);

  const personSelect = el('select', {
    class: 'input',
    style: 'width: 100%; height: 34px; font-size: 12.5px; margin-bottom: 0.75rem;',
  }, [
    el('option', { value: '' }, '-- Đang tải danh sách người xử lý... --'),
  ]);

  async function loadLeaders() {
    try {
      const res = await api.get('/admin/users?role=leader');
      const list = res?.data || [];
      if (list.length > 0) {
        personSelect.replaceChildren(
          el('option', { value: '' }, '-- Chọn lãnh đạo phê duyệt (Chủ tịch / Phó Chủ tịch) --'),
          ...list.map(u => {
            const name = u.full_name || u.hoTen || u.name || 'Lãnh đạo';
            return el('option', { value: String(u.id) }, `${name}`);
          })
        );
      } else {
        personSelect.replaceChildren(
          el('option', { value: '' }, '-- Chọn lãnh đạo phê duyệt --'),
          el('option', { value: '4' }, 'Nguyễn Văn Tuấn (Chủ tịch UBND Phường)'),
          el('option', { value: '8' }, 'Phạm Thị Dung (Phó Chủ tịch UBND Phường)')
        );
      }
      if (app.approver?.id) {
        personSelect.value = String(app.approver.id);
      }
    } catch (_) {
      personSelect.replaceChildren(
        el('option', { value: '' }, '-- Chọn người phê duyệt --'),
        el('option', { value: '4' }, 'Nguyễn Văn Tuấn (Chủ tịch UBND Phường)'),
        el('option', { value: '8' }, 'Phạm Thị Dung (Phó Chủ tịch UBND Phường)')
      );
    }
  }

  const editor = createWordEditor({
    placeholder: 'Nhập nội dung báo cáo kết quả thẩm định, đề xuất lãnh đạo phê duyệt...',
    minHeight: '120px',
  });

  const onKey = (e) => { if (e.key === 'Escape') close(); };
  const close = () => {
    window.removeEventListener('keydown', onKey);
    backdrop.remove();
    activeModal = null;
  };
  window.addEventListener('keydown', onKey);

  const confirmBtn = el('button', {
    type: 'button',
    class: 'btn btn-primary btn-sm',
    style: 'background: #004482; color: #ffffff; font-weight: 700; height: 34px; font-size: 12.5px; padding: 0 1.25rem; border: none;',
    onClick: async () => {
      errorAlert.style.display = 'none';
      confirmBtn.disabled = true;
      confirmBtn.textContent = 'Đang gửi...';
      try {
        const comment = editor.getText();
        const leaderId = personSelect.value ? Number(personSelect.value) : null;
        await api.post(`/admin/applications/${app.id}/forward`, {
          leader_id: leaderId,
          note: comment || 'Cán bộ thụ lý xác nhận hoàn thành xử lý hồ sơ và chuyển lãnh đạo phê duyệt.',
        });
        close();
        if (onSubmitted) onSubmitted();
      } catch (err) {
        errorAlert.textContent = err.message || 'Lỗi khi gửi xác nhận hoàn thành.';
        errorAlert.style.display = 'block';
        confirmBtn.disabled = false;
        confirmBtn.textContent = 'Xác nhận';
      }
    },
  }, 'Xác nhận');

  const cancelBtn = el('button', {
    type: 'button',
    class: 'btn btn-secondary btn-sm',
    style: 'height: 34px; font-size: 12.5px; padding: 0 0.85rem; border: 1px solid #cbd5e1;',
    onClick: close,
  }, 'Hủy bỏ');

  const card = el('div', {
    class: 'card',
    style: 'width: 100%; max-width: 620px; background: #ffffff; border-radius: 6px; padding: 1.25rem 1.5rem; box-shadow: 0 12px 32px rgba(0,0,0,0.25); max-height: 90vh; overflow-y: auto;',
    onClick: (e) => e.stopPropagation(),
  }, [
    el('div', { style: 'display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.5rem;' }, [
      el('h3', { style: 'font-size: 15px; font-weight: 800; color: #004482; margin: 0; text-transform: uppercase;' }, 'Xác nhận hoàn thành & Trình duyệt hồ sơ'),
      el('button', { type: 'button', style: 'border: none; background: none; font-size: 20px; color: #64748b; cursor: pointer; padding: 0.25rem;', onClick: close }, '✕'),
    ]),
    errorAlert,
    el('div', { style: 'margin-bottom: 0.75rem;' }, [
      el('label', { style: 'display: block; font-size: 11.5px; font-weight: 700; color: #334155; margin-bottom: 0.3rem;' }, '1. Chọn đơn vị tiếp nhận xử lý:'),
      unitSelect,
    ]),
    el('div', { style: 'margin-bottom: 0.75rem;' }, [
      el('label', { style: 'display: block; font-size: 11.5px; font-weight: 700; color: #334155; margin-bottom: 0.3rem;' }, '2. Chọn người phê duyệt / Lãnh đạo UBND:'),
      personSelect,
    ]),
    el('div', { style: 'margin-bottom: 1rem;' }, [
      el('label', { style: 'display: block; font-size: 11.5px; font-weight: 700; color: #334155; margin-bottom: 0.3rem;' }, '3. Ý kiến / Bình luận trình duyệt (Soạn thảo văn bản):'),
      editor.wrapper,
    ]),
    el('div', { style: 'display: flex; justify-content: flex-end; gap: 0.5rem;' }, [
      cancelBtn,
      confirmBtn,
    ]),
  ]);

  backdrop.append(card);
  backdrop.addEventListener('click', close);
  document.body.append(backdrop);
  activeModal = backdrop;
  loadLeaders();
}
