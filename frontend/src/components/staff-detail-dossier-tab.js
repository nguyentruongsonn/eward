import { el } from './dom.js';

export function renderStaffDossierTab(app) {
  const rawData = app.data || {};
  const formPayload = (rawData.payload && typeof rawData.payload === 'object') ? rawData.payload : rawData;

  const components = Array.isArray(app.procedure_components) ? app.procedure_components : [];
  const apiFiles = Array.isArray(app.files) ? app.files : [];
  const payloadFiles = Array.isArray(formPayload.attached_files) ? formPayload.attached_files : [];
  const combinedFiles = [...apiFiles, ...payloadFiles];
  const resultFiles = Array.isArray(app.result_files) ? app.result_files : [];

  const container = el('div', { style: 'display: flex; flex-direction: column; gap: 1.25rem;' });

  const submittedTable = combinedFiles.length > 0 ? el('table', { style: 'width: 100%; border-collapse: collapse; font-size: 12.5px;' }, [
    el('thead', {}, el('tr', { style: 'background: #f8fafc; border-bottom: 1px solid #d0d7de; text-align: left;' }, [
      el('th', { style: 'padding: 0.55rem 0.65rem; width: 45px; text-align: center;' }, 'STT'),
      el('th', { style: 'padding: 0.55rem 0.65rem;' }, 'Thành phần hồ sơ'),
      el('th', { style: 'padding: 0.55rem 0.65rem;' }, 'Tệp tin đính kèm'),
      el('th', { style: 'padding: 0.55rem 0.65rem; width: 110px;' }, 'Dung lượng'),
      el('th', { style: 'padding: 0.55rem 0.65rem; width: 120px;' }, 'Ngày tải lên'),
      el('th', { style: 'padding: 0.55rem 0.65rem; width: 110px; text-align: right;' }, 'Thao tác'),
    ])),
    el('tbody', {}, combinedFiles.map((f, idx) => {
      const matchedComp = components.find(c => String(c.id) === String(f.document_type_id || f.maGiayTo));
      const docTitle = matchedComp?.name || f.document_name || f.name || `Giấy tờ đính kèm #${idx + 1}`;
      const fileName = f.file_name || f.name || f.tenTep || docTitle;
      const fileSize = f.size ? `${(f.size / 1024).toFixed(1)} KB` : (f.file_size || '-');
      const uploadDate = f.uploaded_at ? f.uploaded_at.split('T')[0] : 'Khi nộp hồ sơ';

      return el('tr', { style: 'border-bottom: 1px solid #e2e8f0;' }, [
        el('td', { style: 'padding: 0.55rem 0.65rem; text-align: center; color: #64748b;' }, String(idx + 1)),
        el('td', { style: 'padding: 0.55rem 0.65rem; font-weight: 600; color: #0f172a;' }, [
          el('div', {}, docTitle),
          matchedComp?.component_name ? el('div', { style: 'font-size: 11px; color: #64748b; font-weight: normal;' }, matchedComp.component_name) : null,
        ]),
        el('td', { style: 'padding: 0.55rem 0.65rem; color: #0284c7; font-weight: 500;' }, fileName),
        el('td', { style: 'padding: 0.55rem 0.65rem; color: #64748b;' }, fileSize),
        el('td', { style: 'padding: 0.55rem 0.65rem; color: #64748b; font-size: 12px;' }, uploadDate),
        el('td', { style: 'padding: 0.55rem 0.65rem; text-align: right;' }, [
          f.download_url ? el('a', {
            href: f.download_url,
            target: '_blank',
            class: 'btn btn-secondary btn-sm',
            style: 'padding: 0.2rem 0.6rem; font-size: 11.5px; text-decoration: none; background: #f0fdf4; color: #166534; font-weight: 700; border: 1px solid #bbf7d0;',
          }, 'Tải về ⤓') : el('span', { style: 'color: #0369a1; font-size: 11px; font-weight: 600; background: #e0f2fe; padding: 0.2rem 0.5rem; border-radius: 3px;' }, 'Bản nộp trực tuyến'),
        ]),
      ]);
    })),
  ]) : el('div', { style: 'padding: 1.5rem; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 4px; text-align: center; color: #64748b; font-size: 13px;' },
    'Chưa có thành phần hồ sơ hoặc tệp tin nào được nộp trực tuyến (Người nộp xuất trình bản chính trực tiếp tại Bộ phận một cửa).'
  );

  const dossierCard = el('div', { class: 'card', style: 'padding: 1.25rem 1.5rem; background: #ffffff; border: 1px solid #d0d7de;' }, [
    el('div', { style: 'display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem; flex-wrap: wrap; gap: 0.5rem;' }, [
      el('div', { style: 'font-size: 13.5px; font-weight: 800; color: #004482;' }, 'DANH MỤC THÀNH PHẦN HỒ SƠ ĐÃ NỘP'),
      el('span', { style: `font-size: 11.5px; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 4px; ${combinedFiles.length > 0 ? 'background: #e0f2fe; color: #0369a1;' : 'background: #f1f5f9; color: #64748b;'}` },
        `Đã nộp: ${combinedFiles.length} tệp`
      ),
    ]),
    submittedTable,
  ]);

  container.append(dossierCard);
  return container;
}
