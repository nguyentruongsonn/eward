import { el } from './dom.js';

export function createSubmissionDossier(components = []) {
  const container = el('div', { class: 'submission-dossier-wrapper', style: 'display: flex; flex-direction: column; gap: 1rem;' });

  const flatDocuments = [];
  (components || []).forEach(comp => {
    (comp.documents || []).forEach(doc => {
      flatDocuments.push({ ...doc, component_name: comp.name });
    });
  });

  if (flatDocuments.length === 0) {
    flatDocuments.push(
      { id: 'default_1', name: 'Giấy tờ tùy thân (CCCD / Hộ chiếu)', type: 'Giấy tờ tùy thân', required: 'Bắt buộc', original_copies: 1, duplicate_copies: 0 },
      { id: 'default_2', name: 'Đơn / Giấy tờ chứng minh nội dung yêu cầu', type: 'Tờ khai', required: 'Không bắt buộc', original_copies: 1, duplicate_copies: 0 }
    );
  }

  const docRegistry = [];

  const table = el('table', {
    class: 'table',
    style: 'width: 100%; border-collapse: collapse; font-size: 13px; background: #ffffff; border: 1px solid #d0d7de; border-radius: 4px;',
  });

  const thead = el('thead', { style: 'background: #f8fafc; border-bottom: 1px solid #cbd5e1;' }, [
    el('tr', {}, [
      el('th', { style: 'padding: 0.65rem 0.5rem; text-align: center; width: 45px; color: #334155; font-weight: 700;' }, 'STT'),
      el('th', { style: 'padding: 0.65rem 0.75rem; text-align: left; color: #334155; font-weight: 700;' }, 'Tên giấy tờ, tài liệu'),
      el('th', { style: 'padding: 0.65rem 0.75rem; text-align: center; width: 110px; color: #334155; font-weight: 700;' }, 'Quy định'),
      el('th', { style: 'padding: 0.65rem 0.75rem; text-align: center; width: 130px; color: #334155; font-weight: 700;' }, 'Số lượng'),
      el('th', { style: 'padding: 0.65rem 0.75rem; text-align: center; width: 190px; color: #334155; font-weight: 700;' }, 'Đính kèm tệp'),
    ]),
  ]);

  const tbody = el('tbody');

  flatDocuments.forEach((doc, idx) => {
    const isRequired = doc.required === 'Bắt buộc' || doc.required === true;
    const fileInput = el('input', { type: 'file', accept: '.pdf,.jpg,.jpeg,.png,.doc,.docx', style: 'display: none;' });

    const fileStatus = el('div', {
      style: 'display: none; font-size: 11.5px; color: #0f172a; margin-top: 0.35rem; word-break: break-all; text-align: left;',
    });

    const uploadBtn = el('button', {
      type: 'button',
      class: 'btn btn-secondary btn-sm',
      style: 'border: 1px solid #cbd5e1; color: #004482; font-size: 12px; font-weight: 600; padding: 0.25rem 0.65rem;',
      onClick: () => fileInput.click(),
    }, 'Chọn tệp');

    let currentFile = null;

    fileInput.addEventListener('change', () => {
      if (fileInput.files && fileInput.files[0]) {
        const file = fileInput.files[0];
        const sizeKb = Math.round(file.size / 1024);
        currentFile = {
          document_id: doc.id,
          document_name: doc.name,
          file_name: file.name,
          file_size: `${sizeKb} KB`,
          file_type: file.type,
        };

        fileStatus.replaceChildren(
          el('span', { class: 'material-symbols-outlined', style: 'font-size: 14px; vertical-align: -2px; margin-right: 3px;' }, 'description'),
          el('span', {}, `${file.name} (${sizeKb} KB) `),
          el('button', {
            type: 'button',
            style: 'background: transparent; color: #b91c1c; font-weight: 700; border: none; cursor: pointer; padding: 0 2px;',
            title: 'Hủy tệp',
            onClick: () => {
              fileInput.value = '';
              currentFile = null;
              fileStatus.style.display = 'none';
              uploadBtn.textContent = 'Chọn tệp';
            },
          }, '✕')
        );
        fileStatus.style.display = 'block';
        uploadBtn.textContent = 'Đổi tệp';
      }
    });

    docRegistry.push({ doc, isRequired, getFile: () => currentFile });

    const copiesText = `Bản chính: ${doc.original_copies || 0} • Bản sao: ${doc.duplicate_copies || 0}`;

    const tr = el('tr', {
      style: `border-bottom: 1px solid #e2e8f0; ${idx % 2 === 1 ? 'background: #fafbfc;' : 'background: #ffffff;'}`,
    }, [
      el('td', { style: 'padding: 0.75rem 0.5rem; text-align: center; color: #64748b; font-weight: 600;' }, String(idx + 1)),
      el('td', { style: 'padding: 0.75rem;' }, [
        el('div', { style: 'font-weight: 600; color: #0f172a; line-height: 1.4;' }, doc.name),
        doc.type ? el('div', { style: 'font-size: 11.5px; color: #64748b; margin-top: 0.15rem;' }, `Loại giấy tờ: ${doc.type}`) : '',
      ]),
      el('td', { style: 'padding: 0.75rem; text-align: center;' }, [
        el('span', {
          style: `font-size: 11.5px; font-weight: 600; ${isRequired ? 'color: #b91c1c;' : 'color: #475569;'}`,
        }, isRequired ? 'Bắt buộc' : 'Không bắt buộc'),
      ]),
      el('td', { style: 'padding: 0.75rem; text-align: center; font-size: 12px; color: #475569;' }, copiesText),
      el('td', { style: 'padding: 0.75rem; text-align: center;' }, [
        uploadBtn,
        fileInput,
        fileStatus,
      ]),
    ]);

    tbody.append(tr);
  });

  table.append(thead, tbody);

  const tableCard = el('div', {
    class: 'card',
    style: 'padding: 1.25rem; background: #ffffff; border: 1px solid #d0d7de; border-radius: 4px; overflow-x: auto;',
  }, [
    el('h3', { style: 'font-size: 14px; font-weight: 700; color: #004482; margin: 0 0 1rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.5rem;' }, 'DANH MỤC THÀNH PHẦN HỒ SƠ & GIẤY TỜ NỘP'),
    table,
  ]);

  container.append(tableCard);

  return {
    element: container,
    getAttachedFiles: () => {
      const files = [];
      docRegistry.forEach(item => {
        const f = item.getFile();
        if (f) files.push(f);
      });
      return files;
    },
    validate: () => {
      for (const item of docRegistry) {
        if (item.isRequired && !item.getFile()) {
          return { valid: false, message: `Vui lòng đính kèm tệp cho giấy tờ bắt buộc: "${item.doc.name}"` };
        }
      }
      return { valid: true };
    },
  };
}
