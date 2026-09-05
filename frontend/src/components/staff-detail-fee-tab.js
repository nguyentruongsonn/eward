import { el } from './dom.js';

export function renderStaffFeeTab(app) {
  const rawData = app.data || {};
  const feeItems = Array.isArray(rawData.fee_items) ? rawData.fee_items : [];
  const totalFee = Number(app.fee || 0);

  const paymentMethod = rawData.payment_method || (app.delivery_method === 'Trực tuyến' ? 'Cổng thanh toán DVC Quốc gia' : 'Thu trực tiếp tại Quầy Một cửa');
  const isPaid = app.status?.id >= 2 && totalFee > 0;
  const statusLabel = totalFee === 0 ? 'Miễn thu lệ phí' : (isPaid ? 'Đã thu lệ phí / Có biên lai' : 'Chờ thu phí khi giao kết quả');
  const statusBg = totalFee === 0 ? '#f1f5f9' : (isPaid ? '#dcfce7' : '#fef3c7');
  const statusColor = totalFee === 0 ? '#475569' : (isPaid ? '#15803d' : '#92400e');

  const container = el('div', { style: 'display: flex; flex-direction: column; gap: 1.25rem;' });

  const kpiGrid = el('div', {
    style: 'display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;',
  }, [
    el('div', { class: 'card', style: 'padding: 1.25rem; background: #ffffff; border: 1px solid #d0d7de; border-top: 3px solid #004482;' }, [
      el('span', { style: 'font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;' }, 'Tổng mức thu lệ phí'),
      el('div', { style: 'font-size: 22px; font-weight: 800; color: #004482; margin: 0.35rem 0 0.2rem; font-family: var(--font-heading);' },
        `${totalFee.toLocaleString('vi-VN')} đ`
      ),
      totalFee === 0 ? el('span', { style: 'font-size: 11.5px; color: #64748b;' }, 'Thủ tục không áp dụng thu phí') : null,
    ]),
    el('div', { class: 'card', style: 'padding: 1.25rem; background: #ffffff; border: 1px solid #d0d7de; border-top: 3px solid #16a34a;' }, [
      el('span', { style: 'font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;' }, 'Tình trạng thanh toán'),
      el('div', { style: 'margin: 0.45rem 0 0;' }, [
        el('span', {
          style: `font-size: 13px; font-weight: 700; padding: 0.25rem 0.65rem; border-radius: 4px; background: ${statusBg}; color: ${statusColor}; border: 1px solid ${statusBg};`,
        }, statusLabel),
      ]),
    ]),
    el('div', { class: 'card', style: 'padding: 1.25rem; background: #ffffff; border: 1px solid #d0d7de; border-top: 3px solid #0284c7;' }, [
      el('span', { style: 'font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;' }, 'Hình thức thu nộp'),
      el('div', { style: 'font-size: 13.5px; font-weight: 700; color: #0f172a; margin: 0.45rem 0 0;' }, paymentMethod),
    ]),
  ]);

  const feeDetailsCard = el('div', { class: 'card', style: 'padding: 1.25rem 1.5rem; background: #ffffff; border: 1px solid #d0d7de;' }, [
    el('div', { style: 'display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;' }, [
      el('div', { style: 'font-size: 13.5px; font-weight: 800; color: #004482;' }, 'DANH MỤC CÁC KHOẢN THU PHÍ & LỆ PHÍ'),
      el('span', { style: 'font-size: 12.5px; font-weight: 800; color: #047857;' }, `Tổng cộng: ${totalFee.toLocaleString('vi-VN')} đ`),
    ]),
    feeItems.length > 0 ? el('table', { style: 'width: 100%; border-collapse: collapse; font-size: 12.5px;' }, [
      el('thead', {}, el('tr', { style: 'background: #f8fafc; border-bottom: 1px solid #d0d7de; text-align: left;' }, [
        el('th', { style: 'padding: 0.55rem 0.65rem; width: 45px; text-align: center;' }, 'STT'),
        el('th', { style: 'padding: 0.55rem 0.65rem;' }, 'Mục phí / Lệ phí theo quy định'),
        el('th', { style: 'padding: 0.55rem 0.65rem; width: 85px; text-align: center;' }, 'Số lượng'),
        el('th', { style: 'padding: 0.55rem 0.65rem; width: 120px; text-align: right;' }, 'Đơn giá'),
        el('th', { style: 'padding: 0.55rem 0.65rem; width: 130px; text-align: right;' }, 'Thành tiền'),
      ])),
      el('tbody', {}, [
        ...feeItems.map((fi, idx) => el('tr', { style: 'border-bottom: 1px solid #e2e8f0;' }, [
          el('td', { style: 'padding: 0.55rem 0.65rem; text-align: center; color: #64748b;' }, String(idx + 1)),
          el('td', { style: 'padding: 0.55rem 0.65rem; font-weight: 600; color: #0f172a;' }, fi.name || `Khoản phí #${fi.id}`),
          el('td', { style: 'padding: 0.55rem 0.65rem; text-align: center;' }, String(fi.quantity || 1)),
          el('td', { style: 'padding: 0.55rem 0.65rem; text-align: right; color: #475569;' }, `${Number(fi.unit_amount || 0).toLocaleString('vi-VN')} đ`),
          el('td', { style: 'padding: 0.55rem 0.65rem; text-align: right; font-weight: 700; color: #0f172a;' }, `${Number(fi.amount || 0).toLocaleString('vi-VN')} đ`),
        ])),
        el('tr', { style: 'background: #f8fafc; font-weight: 800;' }, [
          el('td', { colspan: 4, style: 'padding: 0.65rem; text-align: right; color: #004482;' }, 'TỔNG CỘNG LỆ PHÍ PHẢI THU:'),
          el('td', { style: 'padding: 0.65rem; text-align: right; color: #004482; font-size: 14px;' }, `${totalFee.toLocaleString('vi-VN')} đ`),
        ]),
      ]),
    ]) : el('div', { style: 'padding: 1rem; background: #f8fafc; border-radius: 4px; font-size: 13px; color: #334155;' }, [
      el('div', { style: 'display: flex; justify-content: space-between;' }, [
        el('span', {}, 'Mức thu phí quy định của thủ tục:'),
        el('strong', { style: 'color: #004482;' }, `${totalFee.toLocaleString('vi-VN')} đ`),
      ]),
      totalFee === 0 ? el('p', { style: 'margin: 0.5rem 0 0; font-size: 12px; color: #64748b;' }, 'Thủ tục hành chính này không phát sinh phí, lệ phí.') : null,
    ]),
  ]);

  container.append(kpiGrid, feeDetailsCard);
  return container;
}
