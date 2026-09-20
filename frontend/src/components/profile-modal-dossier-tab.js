import { el } from './dom.js';

export function renderProfileModalDossierTab(app) {
  const rawData = app.data || app.dulieu || {};
  const formPayload = (rawData.payload && typeof rawData.payload === 'object') ? rawData.payload : rawData;

  const apiFiles = Array.isArray(app.files) ? app.files : [];
  const payloadFiles = Array.isArray(formPayload.attached_files) || Array.isArray(rawData.files)
    ? (Array.isArray(formPayload.attached_files) ? formPayload.attached_files : rawData.files)
    : [];
  const combinedFiles = deduplicateFiles([...apiFiles, ...payloadFiles]);

  const container = el('div', { style: 'display: flex; flex-direction: column; gap: 1.25rem;' });

  const table = combinedFiles.length > 0 ? el('table', { style: 'width: 100%; border-collapse: collapse; font-size: 12.5px;' }, [
    el('thead', {}, el('tr', { style: 'background: #f8fafc; border-bottom: 1px solid #d0d7de; text-align: left;' }, [
      el('th', { style: 'padding: 0.65rem 0.75rem; width: 45px; text-align: center;' }, 'STT'),
      el('th', { style: 'padding: 0.65rem 0.75rem;' }, 'Thành phần hồ sơ'),
      el('th', { style: 'padding: 0.65rem 0.75rem;' }, 'Tệp tin đính kèm'),
      el('th', { style: 'padding: 0.65rem 0.75rem; width: 110px;' }, 'Dung lượng'),
      el('th', { style: 'padding: 0.65rem 0.75rem; width: 120px;' }, 'Ngày tải lên'),
      el('th', { style: 'padding: 0.65rem 0.75rem; width: 110px; text-align: right;' }, 'Thao tác'),
    ])),
    el('tbody', {}, combinedFiles.map((f, idx) => {
      const docTitle = f.document_name || f.name || `Giấy tờ đính kèm #${idx + 1}`;
      const fileName = f.file_name || f.name || f.tenTep || docTitle;
      const fileSize = f.size ? `${(f.size / 1024).toFixed(1)} KB` : (f.file_size || '-');
      const uploadDate = f.uploaded_at ? f.uploaded_at.split('T')[0] : (app.received_at || 'Khi nộp hồ sơ');

      return el('tr', { style: 'border-bottom: 1px solid #e2e8f0;' }, [
        el('td', { style: 'padding: 0.65rem 0.75rem; text-align: center; color: #64748b;' }, String(idx + 1)),
        el('td', { style: 'padding: 0.65rem 0.75rem; font-weight: 600; color: #0f172a;' }, docTitle),
        el('td', { style: 'padding: 0.65rem 0.75rem; color: #0284c7; font-weight: 500;' }, fileName),
        el('td', { style: 'padding: 0.65rem 0.75rem; color: #64748b;' }, fileSize),
        el('td', { style: 'padding: 0.65rem 0.75rem; color: #64748b; font-size: 12px;' }, uploadDate),
        el('td', { style: 'padding: 0.65rem 0.75rem; text-align: right;' }, [
          f.download_url ? el('a', {
            href: f.download_url,
            target: '_blank',
            class: 'btn btn-secondary btn-sm',
            style: 'padding: 0.2rem 0.6rem; font-size: 11.5px; text-decoration: none; background: #f0fdf4; color: #166534; font-weight: 700; border: 1px solid #bbf7d0;',
          }, 'Tải về') : el('span', { style: 'color: #0369a1; font-size: 11px; font-weight: 600; background: #e0f2fe; padding: 0.2rem 0.5rem; border-radius: 3px;' }, 'Bản nộp trực tuyến'),
        ]),
      ]);
    })),
  ]) : el('div', { style: 'padding: 2rem; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 6px; text-align: center; color: #64748b; font-size: 13px;' },
    'Chưa có thành phần hồ sơ hoặc tệp tin nào được nộp trực tuyến (Người nộp xuất trình bản chính trực tiếp tại Bộ phận một cửa).'
  );

  const dossierCard = el('div', { class: 'card', style: 'padding: 1.5rem; background: #ffffff; border: 1px solid #d0d7de; border-radius: 6px;' }, [
    el('div', { style: 'display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;' }, [
      el('div', { style: 'font-size: 13.5px; font-weight: 800; color: #004482; text-transform: uppercase;' }, 'DANH MỤC THÀNH PHẦN HỒ SƠ ĐÃ NỘP'),
      el('span', { style: `font-size: 11.5px; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 4px; ${combinedFiles.length > 0 ? 'background: #e0f2fe; color: #0369a1;' : 'background: #f1f5f9; color: #64748b;'}` },
        `Đã nộp: ${combinedFiles.length} tệp`
      ),
    ]),
    table,
  ]);

  container.append(dossierCard);
  return container;
}

function deduplicateFiles(files) {
  const seen = new Set();
  return files.filter((file) => {
    const documentId = file.document_type_id || file.document_id;
    const key = documentId ? `document:${documentId}` : `file:${file.file_name || file.name || file.tenTep}`;
    if (seen.has(key)) return false;
    seen.add(key);
    return true;
  });
}
