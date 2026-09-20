import { el } from './dom.js';
import { api } from '../api/client.js';
import { showToast } from './toast.js';

const labels = {
  ho_ten: 'Họ và tên',
  full_name: 'Họ và tên',
  so_giay_to: 'Số giấy tờ',
  so_cmnd_cccd: 'Số CMND / CCCD',
  citizen_id: 'Số định danh cá nhân',
  ngay_sinh: 'Ngày sinh',
  so_dien_thoai: 'Số điện thoại',
  phone: 'Số điện thoại',
  email: 'Email',
  tinh_thanh: 'Tỉnh / Thành phố',
  phuong_xa: 'Phường / Xã',
  so_nha: 'Số nhà / Đường',
  dia_chi_chi_tiet: 'Địa chỉ chi tiết',
  ghi_chu: 'Ghi chú',
};

export function openApplicationEditModal(app, { onSaved } = {}) {
  const overlay = el('div', {
    style: 'position: fixed; inset: 0; z-index: 10000; display: flex; align-items: center; justify-content: center; padding: 1rem; background: rgba(15,23,42,0.65);',
  });
  const modal = el('div', {
    class: 'card',
    style: 'width: min(760px, 96vw); max-height: 92vh; overflow: hidden; display: flex; flex-direction: column; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);',
  });
  const body = el('div', { style: 'padding: 1.25rem 1.5rem; overflow-y: auto;' });
  const formId = `application-edit-form-${Date.now()}`;
  const saveButton = el('button', { type: 'submit', form: formId, class: 'btn btn-primary', style: 'background: #004482; font-weight: 700;' }, 'Lưu thay đổi');
  const raw = app.data || app.dulieu || {};
  const initial = raw.payload && typeof raw.payload === 'object' ? raw.payload : raw;
  const fields = Object.entries(initial).filter(([key]) => !['attached_files', 'delivery_method', 'payment_method'].includes(key));
  const controls = new Map();
  const payloadFiles = Array.isArray(initial.attached_files) ? initial.attached_files : [];
  const persistedFiles = Array.isArray(app.files) ? app.files : [];
  const procedureComponents = Array.isArray(app.procedure_components) ? app.procedure_components : [];
  const documentEntries = buildDocumentEntries(procedureComponents, persistedFiles, payloadFiles);
  const fileInputs = new Map();

  const form = el('form', {
    id: formId,
    onSubmit: async (event) => {
      event.preventDefault();
      saveButton.disabled = true;
      saveButton.textContent = 'Đang lưu...';
      const data = {};
      let valid = true;
      controls.forEach(({ control, original }) => {
        if (!valid) return;
        const value = control.value.trim();
        if (original !== null && typeof original === 'object') {
          try {
            data[control.dataset.key] = value ? JSON.parse(value) : original;
          } catch (_) {
            showToast.error(`Giá trị của "${control.dataset.label}" phải là JSON hợp lệ.`);
            valid = false;
          }
        } else {
          data[control.dataset.key] = value;
        }
      });

      if (!valid) {
        saveButton.disabled = false;
        saveButton.textContent = 'Lưu thay đổi';
        return;
      }

      try {
        const response = await api.patch(`/citizen/applications/${app.id || app.maHSXL}`, { data });
        for (const entry of documentEntries) {
          const selected = fileInputs.get(entry.documentTypeId)?.files?.[0];
          if (!selected) continue;
          if (!entry.documentTypeId) {
            throw new Error(`Không xác định được loại giấy tờ cho "${entry.title}".`);
          }
          const body = new FormData();
          body.append('document_type', String(entry.documentTypeId));
          body.append('file', selected);
          await api.post(`/citizen/applications/${app.id || app.maHSXL}/documents`, body);
        }
        showToast.success('Đã cập nhật hồ sơ.');
        overlay.remove();
        if (typeof onSaved === 'function') onSaved(response?.data || response);
      } catch (err) {
        showToast.error(err?.data?.message || err?.message || 'Không thể cập nhật hồ sơ.');
        saveButton.disabled = false;
        saveButton.textContent = 'Lưu thay đổi';
      }
    },
  });

  if (fields.length === 0) {
    form.append(el('div', { style: 'padding: 1.5rem; text-align: center; color: #64748b; font-size: 13px;' }, 'Hồ sơ chưa có thông tin biểu mẫu để chỉnh sửa.'));
  } else {
    const grid = el('div', { style: 'display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;' });
    fields.forEach(([key, value]) => {
      const isStructured = value !== null && typeof value === 'object';
      const textValue = isStructured ? JSON.stringify(value, null, 2) : String(value ?? '');
      const control = isStructured
        ? el('textarea', { class: 'input', rows: '4', value: textValue, style: 'width: 100%; box-sizing: border-box; resize: vertical;' })
        : el('input', { class: 'input', type: key.includes('email') ? 'email' : (key.includes('ngay') ? 'date' : 'text'), value: textValue, style: 'width: 100%; box-sizing: border-box;' });
      control.dataset.key = key;
      control.dataset.label = labels[key] || key;
      controls.set(key, { control, original: value });
      grid.append(el('label', { style: 'display: flex; flex-direction: column; gap: 0.35rem; font-size: 12px; font-weight: 700; color: #334155;' }, [
        el('span', {}, labels[key] || key),
        control,
      ]));
    });
    form.append(grid);
  }

  const close = () => overlay.remove();
  modal.append(
    el('div', { style: 'padding: 1rem 1.5rem; background: #ffffff; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;' }, [
      el('div', {}, [
        el('div', { style: 'font-size: 11px; font-weight: 800; color: #004b87; text-transform: uppercase;' }, 'CHỈNH SỬA HỒ SƠ'),
        el('h2', { style: 'font-size: 16px; color: #004b87; margin: 0.25rem 0 0;' }, app.id || app.maHSXL || 'Hồ sơ'),
      ]),
      el('button', { type: 'button', class: 'btn btn-secondary btn-sm', onClick: close }, 'Đóng ✕'),
    ]),
    body,
    el('div', { style: 'display: flex; justify-content: flex-end; gap: 0.6rem; padding: 0.9rem 1.5rem; background: #ffffff; border-top: 1px solid #e2e8f0;' }, [
      el('button', { type: 'button', class: 'btn btn-secondary', onClick: close }, 'Hủy'),
      saveButton,
    ]),
  );
  body.append(el('p', { style: 'margin: 0 0 1rem; color: #64748b; font-size: 12.5px; line-height: 1.5;' }, 'Chỉ có thể chỉnh sửa khi hồ sơ đang chờ thanh toán hoặc chờ tiếp nhận. Thông tin lệ phí và phương thức thanh toán được hệ thống bảo toàn.'));
  body.append(form);
  if (documentEntries.length > 0) {
    const dossierSection = el('section', { style: 'margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid #e2e8f0;' }, [
      el('div', { style: 'font-size: 13px; font-weight: 800; color: #004482; margin-bottom: 0.25rem;' }, 'THÀNH PHẦN HỒ SƠ'),
      el('div', { style: 'font-size: 12px; color: #64748b; margin-bottom: 0.75rem;' }, 'Chọn tệp mới để thay thế tài liệu đã nộp cùng loại.'),
    ]);
    const dossierList = el('div', { style: 'display: flex; flex-direction: column; gap: 0.65rem;' });
    documentEntries.forEach((entry, index) => {
      const fileInput = el('input', { type: 'file', accept: '.pdf,.jpg,.jpeg,.png', style: 'display: none;' });
      fileInputs.set(entry.documentTypeId, fileInput);
      const selectedName = el('span', { style: 'font-size: 11.5px; color: #0369a1; word-break: break-all;' }, entry.fileName || 'Chưa có tệp trực tuyến');
      const chooseButton = el('button', {
        type: 'button',
        class: 'btn btn-secondary btn-sm',
        disabled: !entry.documentTypeId,
        style: 'white-space: nowrap; font-size: 11.5px; padding: 0.3rem 0.65rem;',
        onClick: () => fileInput.click(),
      }, entry.fileName ? 'Đổi tệp' : 'Chọn tệp');
      fileInput.addEventListener('change', () => {
        const selected = fileInput.files?.[0];
        if (!selected) return;
        selectedName.textContent = `${selected.name} (${Math.ceil(selected.size / 1024)} KB)`;
        chooseButton.textContent = 'Đổi tệp';
      });
      dossierList.append(el('div', { style: 'display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; padding: 0.7rem 0.8rem; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 5px;' }, [
        el('div', { style: 'min-width: 0;' }, [
          el('div', { style: 'font-size: 12px; font-weight: 700; color: #334155;' }, `${index + 1}. ${entry.title}`),
          selectedName,
        ]),
        chooseButton,
        fileInput,
      ]));
    });
    dossierSection.append(dossierList);
    body.append(dossierSection);
  }
  overlay.addEventListener('click', (event) => { if (event.target === overlay) close(); });
  overlay.append(modal);
  document.body.append(overlay);
  return overlay;
}

function buildDocumentEntries(components, persistedFiles, payloadFiles) {
  const entries = new Map();
  components.forEach((component) => {
    const id = Number(component.id);
    if (!Number.isInteger(id) || id < 1) return;
    entries.set(String(id), { documentTypeId: id, title: component.name || component.component_name || `Giấy tờ ${id}` });
  });
  persistedFiles.forEach((file) => {
    const id = Number(file.document_type_id || file.document_id);
    if (!Number.isInteger(id) || id < 1) return;
    const entry = entries.get(String(id)) || { documentTypeId: id, title: file.document_name || file.name || `Giấy tờ ${id}` };
    entry.fileName = file.name || file.file_name || entry.fileName;
    entries.set(String(id), entry);
  });
  payloadFiles.forEach((file) => {
    const id = Number(file.document_id || file.document_type_id);
    if (!Number.isInteger(id) || id < 1) return;
    const entry = entries.get(String(id)) || { documentTypeId: id, title: file.document_name || `Giấy tờ ${id}` };
    entry.fileName = entry.fileName || file.file_name;
    entries.set(String(id), entry);
  });
  return Array.from(entries.values());
}
