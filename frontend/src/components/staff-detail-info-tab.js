import { el } from './dom.js';

const VIETNAMESE_FIELD_LABELS = {
  ho_ten: 'Họ và tên', full_name: 'Họ và tên',
  so_cmnd_cccd: 'Số CMND/ CCCD', citizen_id: 'Số CMND/ CCCD', so_giay_to: 'Số giấy tờ tùy thân',
  loai_giay_to: 'Giấy tờ tùy thân', ngay_sinh: 'Ngày sinh',
  so_dien_thoai: 'Số điện thoại', phone: 'Số điện thoại', email: 'Email',
  tinh_thanh: 'Tỉnh/Thành phố', phuong_xa: 'Phường/Xã', so_nha: 'Số nhà/ đường',
  ten_co_quan: 'Tên doanh nghiệp', ten_doanh_nghiep: 'Tên doanh nghiệp',
  ma_so_thue: 'Mã số thuế / Mã doanh nghiệp',
  tinh_thanh_co_quan: 'Tỉnh/Thành phố', phuong_xa_co_quan: 'Phường/Xã',
  dia_chi_chi_tiet: 'Địa chỉ chi tiết', dia_chi_chi_tiet_co_quan: 'Địa chỉ chi tiết',
  noi_thuong_tru: 'Địa chỉ thường trú', quoc_gia: 'Quốc gia',
  noi_cap_giay_to: 'Nơi cấp giấy tờ', ngay_cap: 'Ngày cấp',
  hinh_thuc_nop: 'Hình thức nộp', loai_trich_luc: 'Loại trích lục', so_luong_ban_sao: 'Số lượng bản sao',
  ghiChu: 'Ghi chú', note: 'Ghi chú',
};

function formatLabel(k) {
  return VIETNAMESE_FIELD_LABELS[k] || k.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
}

function renderFieldInput(label, val, type = 'text') {
  const isSelect = type === 'select' || label.includes('Tỉnh') || label.includes('Phường') || label.includes('Quốc gia');
  const isDate = type === 'date' || label.includes('Ngày sinh') || label.includes('Ngày cấp');
  const hasValue = val !== null && val !== undefined && String(val).trim() !== '';

  let inputEl;
  if (isSelect) {
    inputEl = el('select', {
      disabled: true,
      class: 'input',
      style: `width: 100%; box-sizing: border-box; cursor: default; opacity: 1; font-size: 13px; border-radius: 4px; padding: 0.45rem 0.75rem; ${
        hasValue
          ? 'background: #ffffff; border: 1px solid #cbd5e1; color: #0f172a; font-weight: 600;'
          : 'background: #f8fafc; border: 1px dashed #cbd5e1; color: #94a3b8; font-style: italic;'
      }`,
    }, [
      el('option', { selected: true }, hasValue ? String(val) : `(Chưa chọn ${label.toLowerCase()})`),
    ]);
  } else if (isDate) {
    inputEl = el('input', {
      type: 'text',
      readonly: true,
      class: 'input',
      value: hasValue ? String(val) : '(Chưa nhập ngày)',
      style: `width: 100%; box-sizing: border-box; font-size: 13px; border-radius: 4px; padding: 0.45rem 0.75rem; ${
        hasValue
          ? 'background: #ffffff; border: 1px solid #cbd5e1; color: #0f172a; font-weight: 600;'
          : 'background: #f8fafc; border: 1px dashed #cbd5e1; color: #94a3b8; font-style: italic;'
      }`,
    });
  } else {
    inputEl = el('input', {
      type: 'text',
      readonly: true,
      class: 'input',
      value: hasValue ? String(val) : '(Chưa nhập)',
      style: `width: 100%; box-sizing: border-box; font-size: 13px; border-radius: 4px; padding: 0.45rem 0.75rem; ${
        hasValue
          ? 'background: #ffffff; border: 1px solid #cbd5e1; color: #0f172a; font-weight: 600;'
          : 'background: #f8fafc; border: 1px dashed #cbd5e1; color: #94a3b8; font-style: italic;'
      }`,
    });
  }

  return el('div', { style: 'display: flex; flex-direction: column; gap: 0.35rem;' }, [
    el('label', {
      style: `font-size: 12.5px; ${hasValue ? 'font-weight: 700; color: #0f172a;' : 'font-weight: 600; color: #64748b;'}`,
    }, [
      label,
      hasValue ? null : el('span', { style: 'font-size: 11px; font-weight: 400; color: #94a3b8; margin-left: 0.35rem;' }, '(trống)'),
    ]),
    inputEl,
  ]);
}

function getFieldValue(name, formPayload, app) {
  if (!name) return '';
  if (formPayload[name] !== undefined && formPayload[name] !== null && formPayload[name] !== '') {
    return formPayload[name];
  }
  const n = name.toLowerCase();
  if (n.includes('ho_ten') || n === 'ten' || n.includes('full_name')) return app.applicant_name || '';
  if (n.includes('cccd') || n.includes('cmnd') || n === 'so_giay_to' || n.includes('citizen_id')) return app.applicant_id_card || '';
  if (n.includes('so_dien_thoai') || n.includes('phone')) return app.applicant_phone || '';
  if (n.includes('email')) return app.applicant_email || '';
  if (n.includes('dia_chi') || n.includes('noi_thuong_tru')) return app.applicant_address || '';
  return '';
}

function renderGroupCard(num, title, contentChildren) {
  return el('div', { class: 'card', style: 'padding: 1.5rem; background: #ffffff; border: 1px solid #d0d7de; border-radius: 4px;' }, [
    el('div', { style: 'display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.25rem;' }, [
      el('span', { style: 'display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; background: #e0f2fe; color: #0284c7; font-weight: 700; font-size: 12px; border-radius: 4px;' }, String(num)),
      el('span', { style: 'font-size: 14px; font-weight: 700; color: #0369a1;' }, title),
    ]),
    ...contentChildren,
  ]);
}

export function renderStaffInfoTab(app) {
  const rawData = app.data || {};
  const formPayload = (rawData.payload && typeof rawData.payload === 'object') ? rawData.payload : rawData;
  const container = el('div', { style: 'display: flex; flex-direction: column; gap: 1.25rem;' });

  let formGroups = [];
  const cfg = app.form_config || app.procedure_form_config || app.data?.cauHinhForm;
  if (cfg) {
    try {
      const parsed = typeof cfg === 'string' ? JSON.parse(cfg) : cfg;
      formGroups = Array.isArray(parsed) ? parsed : (parsed?.groups || []);
    } catch (_) {}
  }

  let groupCounter = 1;

  if (formGroups.length > 0) {
    formGroups.forEach(g => {
      if (!g || !g.fields || g.fields.length === 0) return;
      const fieldElements = [];
      g.fields.forEach(f => {
        if (f.type === 'row' && Array.isArray(f.columns)) {
          const cols = f.columns;
          fieldElements.push(el('div', {
            style: `display: grid; grid-template-columns: repeat(${cols.length}, minmax(0, 1fr)); gap: 0.85rem; margin-bottom: 0.85rem;`,
          }, cols.map(c => renderFieldInput(c.label || formatLabel(c.name), getFieldValue(c.name, formPayload, app), c.type))));
        } else {
          const single = renderFieldInput(f.label || formatLabel(f.name), getFieldValue(f.name, formPayload, app), f.type);
          single.style.marginBottom = '0.85rem';
          fieldElements.push(single);
        }
      });
      container.append(renderGroupCard(groupCounter++, g.group || `Nhóm thông tin ${groupCounter}`, fieldElements));
    });
  } else {
    const defaultRows = [
      [
        { label: 'Họ và tên', val: app.applicant_name || formPayload.ho_ten || formPayload.full_name || '' },
        { label: 'Số CMND/ CCCD', val: app.applicant_id_card || formPayload.so_cmnd_cccd || formPayload.citizen_id || '' },
        { label: 'Ngày sinh', val: formPayload.ngay_sinh || '', type: 'date' },
      ],
      [
        { label: 'Số điện thoại', val: app.applicant_phone || formPayload.so_dien_thoai || formPayload.phone || '' },
        { label: 'Email', val: app.applicant_email || formPayload.email || '' },
      ],
      [
        { label: 'Tỉnh/Thành phố', val: formPayload.tinh_thanh || '', type: 'select' },
        { label: 'Phường/Xã', val: formPayload.phuong_xa || '', type: 'select' },
        { label: 'Số nhà/ đường', val: formPayload.so_nha || '' },
      ],
      [
        { label: 'Tên doanh nghiệp', val: formPayload.ten_co_quan || formPayload.ten_doanh_nghiep || '' },
        { label: 'Mã số thuế / Mã doanh nghiệp', val: formPayload.ma_so_thue || '' },
      ],
      [
        { label: 'Tỉnh/Thành phố', val: formPayload.tinh_thanh_co_quan || formPayload.tinh_thanh || '', type: 'select' },
        { label: 'Phường/Xã', val: formPayload.phuong_xa_co_quan || formPayload.phuong_xa || '', type: 'select' },
        { label: 'Địa chỉ chi tiết', val: app.applicant_address || formPayload.dia_chi_chi_tiet || formPayload.dia_chi_chi_tiet_co_quan || formPayload.noiThuongTru || '' },
      ],
    ];

    const group1Els = defaultRows.map(rowCols => el('div', {
      style: `display: grid; grid-template-columns: repeat(${rowCols.length}, minmax(0, 1fr)); gap: 0.85rem; margin-bottom: 0.85rem;`,
    }, rowCols.map(c => renderFieldInput(c.label, c.val, c.type))));

    container.append(renderGroupCard(groupCounter++, 'Thông tin người nộp', group1Els));

    const handledKeys = new Set([
      'ho_ten', 'full_name', 'so_cmnd_cccd', 'citizen_id', 'so_giay_to', 'ngay_sinh', 'so_dien_thoai', 'phone', 'email',
      'tinh_thanh', 'phuong_xa', 'so_nha', 'ten_co_quan', 'ten_doanh_nghiep', 'ma_so_thue', 'tinh_thanh_co_quan',
      'phuong_xa_co_quan', 'dia_chi_chi_tiet', 'dia_chi_chi_tiet_co_quan', 'noiThuongTru', 'noi_thuong_tru',
      'attached_files', 'delivery_method', 'payment_method', 'fee_items',
    ]);
    const extraEntries = Object.entries(formPayload).filter(([k]) => !handledKeys.has(k));
    if (extraEntries.length > 0) {
      const extraCols = extraEntries.map(([k, v]) => renderFieldInput(formatLabel(k), v));
      container.append(renderGroupCard(groupCounter++, 'Nội dung kê khai bổ sung', [
        el('div', { style: 'display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 0.85rem;' }, extraCols),
      ]));
    }
  }

  return container;
}
