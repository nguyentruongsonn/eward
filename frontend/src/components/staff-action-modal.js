import { el } from './dom.js';

let activeModal = null;

export function openStaffActionModal({ action, actionLabel, procedureComponents = [], onSubmit }) {
  if (activeModal) {
    activeModal.remove();
    activeModal = null;
  }

  const backdrop = el('div', {
    class: 'modal-backdrop',
    style: 'position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 1050; display: flex; align-items: center; justify-content: center; padding: 1.5rem;',
  });

  const onKey = (e) => { if (e.key === 'Escape') close(); };
  const close = () => {
    window.removeEventListener('keydown', onKey);
    backdrop.remove();
    activeModal = null;
  };
  window.addEventListener('keydown', onKey);

  const errorAlert = el('div', {
    style: 'display: none; padding: 0.6rem 0.85rem; background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; border-radius: 4px; font-size: 12.5px; margin-bottom: 0.85rem;',
  });

  const noteInput = el('textarea', {
    class: 'input',
    rows: 3,
    placeholder: action === 'reject' || action === 'rework'
      ? 'Nhập rõ lý do cụ thể... (bắt buộc)'
      : (action === 'requestSupplement' ? 'Nhập rõ lý do, nội dung cần bổ sung, hướng dẫn cụ thể cho công dân... (bắt buộc)' : 'Nhập ý kiến / ghi chú xử lý (nếu có)...'),
    style: 'width: 100%; font-size: 13px; resize: vertical;',
  });

  const isReasonRequired = action === 'reject' || action === 'rework' || action === 'requestSupplement';
  const selectedDocs = new Set();
  let docSection = null;

  if (action === 'requestSupplement') {
    if (procedureComponents.length > 0) {
      const masterChk = el('input', { type: 'checkbox', style: 'cursor: pointer; width: 16px; height: 16px;' });
      const countBadge = el('span', {
        style: 'font-size: 11.5px; font-weight: 700; color: #004482; background: #e0f2fe; padding: 0.2rem 0.6rem; border-radius: 4px;',
      }, `Đã chọn: 0 / ${procedureComponents.length} giấy tờ`);

      const rowCheckboxes = [];

      function updateCounts() {
        const total = procedureComponents.length;
        const selected = selectedDocs.size;
        countBadge.textContent = `Đã chọn: ${selected} / ${total} giấy tờ`;
        masterChk.checked = total > 0 && selected === total;
        masterChk.indeterminate = selected > 0 && selected < total;
      }

      masterChk.addEventListener('change', () => {
        const shouldCheck = masterChk.checked;
        rowCheckboxes.forEach(({ chk, docId, row }) => {
          chk.checked = shouldCheck;
          if (shouldCheck) {
            selectedDocs.add(docId);
            row.style.background = '#f0f7ff';
          } else {
            selectedDocs.delete(docId);
            row.style.background = '#ffffff';
          }
        });
        updateCounts();
      });

      const tableRows = procedureComponents.map((doc, idx) => {
        const docId = Number(doc.id);
        const chk = el('input', { type: 'checkbox', value: String(docId), style: 'cursor: pointer; width: 16px; height: 16px;' });
        const isReq = doc.required === true || doc.required === 1 || String(doc.required).toLowerCase().includes('bắt buộc') || doc.yeuCau === 'Bắt buộc';

        const row = el('tr', {
          style: 'border-bottom: 1px solid #e2e8f0; font-size: 12.5px; transition: background 0.15s ease; cursor: pointer;',
        });

        const toggle = (force) => {
          const newState = typeof force === 'boolean' ? force : !chk.checked;
          chk.checked = newState;
          if (newState) {
            selectedDocs.add(docId);
            row.style.background = '#f0f7ff';
          } else {
            selectedDocs.delete(docId);
            row.style.background = '#ffffff';
          }
          updateCounts();
        };

        chk.addEventListener('change', (e) => {
          e.stopPropagation();
          toggle(chk.checked);
        });

        row.addEventListener('click', (e) => {
          if (e.target !== chk) toggle();
        });

        rowCheckboxes.push({ chk, docId, row });

        const reqBadge = el('span', {
          style: `font-size: 11px; font-weight: 700; padding: 0.15rem 0.5rem; border-radius: 3px; ${
            isReq ? 'background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca;' : 'background: #f1f5f9; color: #475569;'
          }`,
        }, isReq ? 'Bắt buộc' : 'Tùy chọn');

        const formType = doc.banChinh ? `Bản chính: ${doc.banChinh}` : (doc.banSao ? `Bản sao: ${doc.banSao}` : 'Bản điện tử / giấy');

        row.replaceChildren(
          el('td', { style: 'text-align: center; padding: 0.6rem 0.5rem;' }, [chk]),
          el('td', { style: 'text-align: center; padding: 0.6rem 0.5rem; color: #64748b; font-weight: 600;' }, String(idx + 1)),
          el('td', { style: 'padding: 0.6rem 0.75rem;' }, [
            el('div', { style: 'font-weight: 600; color: #0f172a; line-height: 1.4;' }, doc.name),
            (doc.component_name && doc.component_name !== doc.name)
              ? el('div', { style: 'font-size: 11.5px; color: #64748b; margin-top: 0.15rem;' }, doc.component_name)
              : null,
          ]),
          el('td', { style: 'text-align: center; padding: 0.6rem 0.5rem;' }, [reqBadge]),
          el('td', { style: 'text-align: center; padding: 0.6rem 0.5rem; color: #475569; font-size: 12px;' }, formType),
        );

        return row;
      });

      const tableEl = el('table', { style: 'width: 100%; border-collapse: collapse;' }, [
        el('thead', { style: 'position: sticky; top: 0; z-index: 2;' }, [
          el('tr', { style: 'background: #f8fafc; border-bottom: 2px solid #cbd5e1; font-size: 12px; color: #334155; text-align: left;' }, [
            el('th', { style: 'width: 45px; text-align: center; padding: 0.6rem 0.5rem;' }, [masterChk]),
            el('th', { style: 'width: 50px; text-align: center; padding: 0.6rem 0.5rem;' }, 'STT'),
            el('th', { style: 'padding: 0.6rem 0.75rem;' }, 'Tên thành phần hồ sơ / Giấy tờ yêu cầu'),
            el('th', { style: 'width: 120px; text-align: center; padding: 0.6rem 0.5rem;' }, 'Tính chất'),
            el('th', { style: 'width: 140px; text-align: center; padding: 0.6rem 0.5rem;' }, 'Hình thức nộp'),
          ]),
        ]),
        el('tbody', {}, tableRows),
      ]);

      docSection = el('div', { style: 'margin-bottom: 1.25rem;' }, [
        el('div', { style: 'display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;' }, [
          el('label', { style: 'font-size: 12.5px; font-weight: 700; color: #004482;' }, 'Danh mục thành phần hồ sơ / giấy tờ yêu cầu bổ sung:'),
          countBadge,
        ]),
        el('div', { style: 'max-height: 280px; overflow-y: auto; border: 1px solid #cbd5e1; border-radius: 4px; background: #ffffff;' }, [tableEl]),
      ]);
    } else {
      docSection = el('div', {
        style: 'padding: 0.85rem 1rem; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 4px; font-size: 12.5px; color: #64748b; margin-bottom: 1rem;',
      }, 'Thủ tục chưa cấu hình danh mục giấy tờ cụ thể. Cán bộ vui lòng nhập rõ nội dung và giấy tờ cần bổ sung vào ô bên dưới.');
    }
  }

  const cancelBtn = el('button', {
    type: 'button',
    class: 'btn btn-secondary btn-sm',
    style: 'border: 1px solid #cbd5e1; color: #334155; font-weight: 600; height: 34px; padding: 0 1.25rem;',
    onClick: close,
  }, 'Hủy bỏ');

  const confirmBtn = el('button', {
    type: 'button',
    class: 'btn btn-primary btn-sm',
    style: `font-weight: 700; height: 34px; padding: 0 1.5rem; ${action === 'reject' || action === 'rework' ? 'background: #b91c1c;' : 'background: #004482;'}`,
    onClick: async () => {
      errorAlert.style.display = 'none';
      const note = noteInput.value.trim();

      if (isReasonRequired && !note) {
        errorAlert.textContent = 'Vui lòng nhập lý do / nội dung hướng dẫn trước khi xác nhận.';
        errorAlert.style.display = 'block';
        noteInput.focus();
        return;
      }

      confirmBtn.disabled = true;
      confirmBtn.textContent = 'Đang xử lý...';

      try {
        await onSubmit({
          note,
          reason: note,
          document_ids: Array.from(selectedDocs),
        });
        close();
      } catch (err) {
        errorAlert.textContent = err.message || 'Lỗi khi thực hiện hành động.';
        errorAlert.style.display = 'block';
        confirmBtn.disabled = false;
        confirmBtn.textContent = 'Xác nhận thực hiện';
      }
    },
  }, action === 'requestSupplement' ? 'Gửi yêu cầu bổ sung' : 'Xác nhận thực hiện');

  const modalWidth = action === 'requestSupplement' ? '920px' : '560px';
  const card = el('div', {
    class: 'card',
    style: `background: #ffffff; border-radius: 6px; border: 1px solid #d0d7de; width: 95%; max-width: ${modalWidth}; padding: 1.5rem 1.75rem; box-shadow: 0 16px 40px rgba(0,0,0,0.25); max-height: 90vh; overflow-y: auto;`,
    onClick: (e) => e.stopPropagation(),
  }, [
    el('div', { style: 'display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.65rem;' }, [
      el('h3', { style: 'font-size: 16px; font-weight: 800; color: #004482; margin: 0; text-transform: uppercase;' }, actionLabel),
      el('button', { type: 'button', style: 'border: none; background: none; font-size: 20px; color: #64748b; cursor: pointer; padding: 0.25rem;', onClick: close }, '✕'),
    ]),
    errorAlert,
    docSection,
    el('div', { style: 'margin-bottom: 1.25rem;' }, [
      el('label', { style: 'display: block; font-size: 12px; font-weight: 700; color: #0f172a; margin-bottom: 0.35rem;' },
        isReasonRequired ? 'LÝ DO / NỘI DUNG YÊU CẦU: *' : 'Ý KIẾN / GHI CHÚ:'
      ),
      noteInput,
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
