import { el } from './dom.js';

export function createStep3Fee(proc) {
  const container = el('div', { class: 'step3-fee-wrapper', style: 'display: flex; flex-direction: column; gap: 1.5rem;' });
  const feeItem = (proc.fees && proc.fees.length > 0) ? proc.fees[0] : null;

  let deliveryMethod = 'online';
  let paymentMethod = 'online';
  let quantity = 1;

  const deliverySelect = el('select', { class: 'input', style: 'font-size: 13.5px; height: 42px;' }, [
    el('option', { value: 'online' }, 'Nhận trực tuyến (Bản điện tử ký số qua Cổng Dịch vụ công)'),
    el('option', { value: 'direct' }, 'Nhận trực tiếp (Tại Bộ phận Một cửa xã/phường)'),
  ]);
  deliverySelect.addEventListener('change', () => { deliveryMethod = deliverySelect.value; });

  const paymentSelect = el('select', { class: 'input', style: 'font-size: 13.5px; height: 42px;' }, [
    el('option', { value: 'online' }, 'Thanh toán trực tuyến (Cổng DVC Quốc gia / Chuyển khoản QR)'),
    el('option', { value: 'direct' }, 'Thanh toán trực tiếp khi nhận kết quả'),
  ]);
  paymentSelect.addEventListener('change', () => { paymentMethod = paymentSelect.value; });

  const qtyInput = el('input', { type: 'number', class: 'input numeric-data', min: '1', max: '20', value: '1', style: 'width: 80px; height: 38px;' });
  const totalDisplay = el('strong', { class: 'numeric-data', style: 'color: #004482; font-size: 16px;' }, feeItem ? `${feeItem.amount?.toLocaleString('vi-VN')} VNĐ` : 'Miễn phí');

  qtyInput.addEventListener('input', () => {
    quantity = Math.max(1, parseInt(qtyInput.value) || 1);
    const total = feeItem ? (feeItem.amount * quantity) : 0;
    totalDisplay.textContent = total > 0 ? `${total.toLocaleString('vi-VN')} VNĐ` : 'Miễn phí';
  });

  const deliveryCard = el('div', { class: 'card', style: 'padding: 1.5rem; background: #ffffff; border: 1px solid #d0d7de; border-radius: 4px;' }, [
    el('h3', { style: 'font-size: 14px; font-weight: 700; color: #004482; margin: 0 0 1rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.5rem;' }, '1. HÌNH THỨC NHẬN KẾT QUẢ GIẢI QUYẾT'),
    el('div', { style: 'display: flex; flex-direction: column; gap: 0.35rem;' }, [
      el('label', { style: 'font-size: 12px; font-weight: 700; color: #0f172a;' }, 'LỰA CHỌN PHƯƠNG THỨC TRẢ KẾT QUẢ: *'),
      deliverySelect,
      el('p', { style: 'font-size: 12px; color: #64748b; margin: 0.35rem 0 0;' }, 'Hệ thống sẽ gửi thông báo qua SMS/Email khi kết quả được xử lý xong.'),
    ]),
  ]);

  const feeCard = el('div', { class: 'card', style: 'padding: 1.5rem; background: #ffffff; border: 1px solid #d0d7de; border-radius: 4px;' }, [
    el('h3', { style: 'font-size: 14px; font-weight: 700; color: #004482; margin: 0 0 1rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.5rem;' }, '2. TÍNH TOÁN LỆ PHÍ & PHƯƠNG THỨC THANH TOÁN'),
    feeItem ? el('div', { style: 'display: flex; flex-direction: column; gap: 1.25rem;' }, [
      el('div', { style: 'padding: 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;' }, [
        el('div', {}, [
          el('div', { style: 'font-weight: 700; font-size: 13.5px; color: #0f172a;' }, feeItem.type || 'Lệ phí thực hiện:'),
          el('div', { style: 'font-size: 12px; color: #64748b; margin-top: 0.2rem;' }, `Đơn giá: ${feeItem.amount?.toLocaleString('vi-VN')} VNĐ / bản`),
        ]),
        el('div', { style: 'display: flex; align-items: center; gap: 0.65rem;' }, [
          el('span', { style: 'font-size: 12.5px; font-weight: 600;' }, 'Số lượng bản:'),
          qtyInput,
          el('span', { style: 'font-size: 13.5px; font-weight: 700; margin-left: 0.5rem;' }, 'Thành tiền: '),
          totalDisplay,
        ]),
      ]),
      el('div', {}, [
        el('label', { style: 'display: block; font-size: 12px; font-weight: 700; color: #0f172a; margin-bottom: 0.35rem;' }, 'PHƯƠNG THỨC THANH TOÁN: *'),
        paymentSelect,
      ]),
    ]) : el('div', { style: 'padding: 1rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 4px; color: #166534; font-size: 13px; font-weight: 600;' }, '✓ Thủ tục hành chính này không thu phí, lệ phí theo quy định pháp luật hiện hành.'),
  ]);

  container.append(deliveryCard, feeCard);

  return {
    element: container,
    getData: () => ({
      delivery_method: deliveryMethod,
      payment_method: paymentMethod,
      quantity,
      fee_items: feeItem ? [{ id: parseInt(feeItem.id), quantity, unit_amount: feeItem.amount, amount: feeItem.amount * quantity }] : [],
      total_fee: feeItem ? (feeItem.amount * quantity) : 0,
      fee_label: feeItem ? `${(feeItem.amount * quantity).toLocaleString('vi-VN')} VNĐ` : 'Miễn phí',
      delivery_label: deliveryMethod === 'online' ? 'Nhận trực tuyến (Cổng DVC)' : 'Nhận trực tiếp (Bộ phận Một cửa)',
    }),
  };
}

export function createStep4Review({ proc, step1Data = {}, step2Files = [], step3Data = {} }) {
  const container = el('div', { class: 'step4-review-wrapper', style: 'display: flex; flex-direction: column; gap: 1.25rem;' });

  const commitCheckbox = el('input', { type: 'checkbox', id: 'commit_check', style: 'width: 18px; height: 18px; margin-top: 2px; cursor: pointer;' });

  const applicantRows = [
    ['Thủ tục hành chính', proc.name],
    ['Cơ quan tiếp nhận', proc.agency || 'UBND cấp Xã / Phường'],
    ['Người nộp hồ sơ', step1Data.ho_ten || step1Data.full_name || 'Công dân'],
    ['Số CCCD / CMND', step1Data.so_giay_to || step1Data.so_cmnd_cccd || step1Data.citizen_id || 'Chưa cung cấp'],
    ['Số điện thoại', step1Data.so_dien_thoai || step1Data.phone || 'Chưa cung cấp'],
    ['Địa chỉ Email', step1Data.email || 'Chưa cung cấp'],
    ['Hình thức nhận kết quả', step3Data.delivery_label || 'Nhận trực tuyến'],
    ['Tổng lệ phí', step3Data.fee_label || 'Miễn phí'],
  ];

  const infoTable = el('div', { class: 'card', style: 'padding: 1.5rem; background: #ffffff; border: 1px solid #d0d7de; border-radius: 4px;' }, [
    el('h3', { style: 'font-size: 14px; font-weight: 700; color: #004482; margin: 0 0 1rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.5rem;' }, '1. THÔNG TIN HỒ SƠ & NGƯỜI NỘP'),
    el('div', { style: 'display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 0.75rem; font-size: 13px;' },
      applicantRows.map(([lbl, val]) => el('div', { style: 'display: flex; justify-content: space-between; border-bottom: 1px solid #f8fafc; padding: 0.35rem 0;' }, [
        el('span', { style: 'color: #64748b; font-weight: 500;' }, lbl),
        el('strong', { class: 'numeric-data', style: 'color: #0f172a; text-align: right; max-width: 60%;' }, String(val)),
      ]))
    ),
  ]);

  const filesCard = el('div', { class: 'card', style: 'padding: 1.5rem; background: #ffffff; border: 1px solid #d0d7de; border-radius: 4px;' }, [
    el('h3', { style: 'font-size: 14px; font-weight: 700; color: #004482; margin: 0 0 1rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.5rem;' }, '2. DANH SÁCH GIẤY TỜ / TỆP ĐÍNH KÈM'),
    step2Files.length > 0 ? el('div', { style: 'display: flex; flex-direction: column; gap: 0.5rem;' },
      step2Files.map((f, i) => el('div', { style: 'display: flex; justify-content: space-between; font-size: 12.5px; padding: 0.5rem 0.75rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 3px;' }, [
        el('span', { style: 'font-weight: 600;' }, `${i + 1}. ${f.document_name}`),
        el('span', { class: 'numeric-data', style: 'color: #0284c7; display: inline-flex; align-items: center; gap: 4px;' }, [
          el('span', { class: 'material-symbols-outlined', style: 'font-size: 15px;' }, 'description'),
          `${f.file_name} (${f.file_size})`,
        ]),
      ]))
    ) : el('div', { style: 'font-size: 13px; color: #64748b; font-style: italic;' }, 'Chưa có tệp đính kèm nào được tải lên.'),
  ]);

  const commitCard = el('div', { class: 'card', style: 'padding: 1.25rem; background: #fffbeb; border: 1px solid #fef3c7; border-radius: 4px;' }, [
    el('label', { for: 'commit_check', style: 'display: flex; gap: 0.75rem; align-items: flex-start; font-size: 13px; color: #92400e; cursor: pointer; line-height: 1.5;' }, [
      commitCheckbox,
      el('span', {}, [
        el('strong', {}, 'CAM ĐOAN TRÁCH NHIỆM PHÁP LÝ: '),
        el('span', {}, 'Tôi xin cam đoan toàn bộ các thông tin đã kê khai trên và các tệp hồ sơ đính kèm là hoàn toàn chính xác, đúng sự thật. Tôi xin hoàn toàn chịu trách nhiệm trước pháp luật về tính chính xác của hồ sơ đăng ký trực tuyến này.'),
      ]),
    ]),
  ]);

  container.append(infoTable, filesCard, commitCard);

  return {
    element: container,
    isConfirmed: () => commitCheckbox.checked,
  };
}
