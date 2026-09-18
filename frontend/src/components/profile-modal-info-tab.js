import { el } from './dom.js';

function renderField(label, val, isSelect = false) {
  const hasVal = val !== null && val !== undefined && String(val).trim() !== '';
  const inputStyle = `width: 100%; box-sizing: border-box; font-size: 13px; border-radius: 4px; padding: 0.5rem 0.75rem; ${
    hasVal
      ? 'background: #ffffff; border: 1px solid #cbd5e1; color: #0f172a; font-weight: 600;'
      : 'background: #f8fafc; border: 1px dashed #cbd5e1; color: #94a3b8; font-style: italic;'
  }`;

  const inputNode = isSelect
    ? el('select', { disabled: true, class: 'input', style: `${inputStyle} cursor: default;` }, [
        el('option', { selected: true }, hasVal ? String(val) : `(Chưa chọn ${label.toLowerCase()})`),
      ])
    : el('input', { type: 'text', readonly: true, class: 'input', value: hasVal ? String(val) : '', placeholder: '(Chưa nhập)', style: inputStyle });

  return el('div', { style: 'display: flex; flex-direction: column; gap: 0.3rem;' }, [
    el('label', { style: 'font-size: 12px; font-weight: 600; color: #334155;' }, label),
    inputNode,
  ]);
}

function renderGroupCard(num, title, rows) {
  return el('div', { class: 'card', style: 'padding: 1.5rem; background: #ffffff; border: 1px solid #d0d7de; border-radius: 6px;' }, [
    el('div', { style: 'display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.25rem;' }, [
      el('span', { style: 'display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; background: #e0f2fe; color: #0284c7; font-weight: 700; font-size: 12px; border-radius: 3px;' }, String(num)),
      el('span', { style: 'font-size: 14px; font-weight: 700; color: #004b87;' }, title),
    ]),
    el('div', { style: 'display: flex; flex-direction: column; gap: 0.85rem;' }, rows),
  ]);
}

export function renderProfileModalInfoTab(app) {
  const container = el('div', { style: 'display: flex; flex-direction: column; gap: 1.25rem;' });
  const rawData = app.data || app.dulieu || {};
  const p = (rawData.payload && typeof rawData.payload === 'object') ? rawData.payload : rawData;

  const rowsGroup1 = [
    el('div', { style: 'display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.85rem;' }, [
      renderField('Họ và tên', app.applicant_name || p.ho_ten || p.full_name || app.tenChuHoSo || ''),
      renderField('Số CMND/ CCCD', app.applicant_id_card || p.so_cmnd_cccd || p.citizen_id || p.so_giay_to || ''),
      renderField('Ngày sinh', p.ngay_sinh || ''),
    ]),
    el('div', { style: 'display: grid; grid-template-columns: 1fr 1fr; gap: 0.85rem;' }, [
      renderField('Số điện thoại', app.applicant_phone || p.so_dien_thoai || p.phone || app.soDienThoai || ''),
      renderField('Email', app.applicant_email || p.email || app.email || ''),
    ]),
    el('div', { style: 'display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.85rem;' }, [
      renderField('Tỉnh/Thành phố', p.tinh_thanh || '', true),
      renderField('Phường/Xã', p.phuong_xa || '', true),
      renderField('Số nhà/ đường', p.so_nha || ''),
    ]),
    el('div', { style: 'display: grid; grid-template-columns: 1fr 1fr; gap: 0.85rem;' }, [
      renderField('Tên doanh nghiệp', p.ten_co_quan || p.ten_doanh_nghiep || ''),
      renderField('Mã số thuế / Mã doanh nghiệp', p.ma_so_thue || ''),
    ]),
    el('div', { style: 'display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.85rem;' }, [
      renderField('Tỉnh/Thành phố', p.tinh_thanh_co_quan || p.tinh_thanh || '', true),
      renderField('Phường/Xã', p.phuong_xa_co_quan || p.phuong_xa || '', true),
      renderField('Địa chỉ chi tiết', app.applicant_address || p.dia_chi_chi_tiet || p.dia_chi_chi_tiet_co_quan || p.noiThuongTru || ''),
    ]),
  ];

  const statusName = app.status?.name || app.status_name || app.tenTrangThai || 'Đang xử lý';
  const rowsGroup2 = [
    el('div', { style: 'display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.85rem;' }, [
      renderField('Mã hồ sơ', app.id || app.maHSXL || ''),
      renderField('Hình thức nhận kết quả', app.delivery_method || app.hinhThuc || 'Trực tuyến'),
      renderField('Trạng thái giải quyết', statusName),
    ]),
    el('div', { style: 'display: grid; grid-template-columns: 1fr 1fr; gap: 0.85rem;' }, [
      renderField('Ngày tiếp nhận', app.received_at || app.reception_date || app.ngayTiepNhan || 'Chờ tiếp nhận'),
      renderField('Ngày hẹn trả kết quả', app.due_at || app.appointment_date || app.ngayHenTra || 'Chưa định'),
    ]),
    el('div', { style: 'display: grid; grid-template-columns: 1fr; gap: 0.85rem;' }, [
      renderField('Cơ quan giải quyết', app.donViXuLy || 'Ủy ban nhân dân cấp Xã / Phường'),
    ]),
    app.ghiChu ? el('div', { style: 'display: grid; grid-template-columns: 1fr; gap: 0.85rem;' }, [
      renderField('Ghi chú / Ý kiến xử lý', app.ghiChu),
    ]) : null,
  ].filter(Boolean);

  container.append(
    renderGroupCard(1, 'Thông tin người nộp', rowsGroup1),
    renderGroupCard(2, 'Thông tin hồ sơ tiếp nhận', rowsGroup2)
  );

  return container;
}
