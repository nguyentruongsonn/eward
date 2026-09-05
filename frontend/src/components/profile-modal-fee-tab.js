import { el } from './dom.js';

export function renderProfileModalFeeTab(app) {
  const rawData = app.data || app.dulieu || {};
  const feeItems = Array.isArray(rawData.fee_items) ? rawData.fee_items : [];
  const totalFee = Number(app.fee ?? app.lePhi ?? 0);
  const appId = app.id || app.maHSXL || '';

  const isFree = totalFee <= 0;
  const isPaid = isFree || Boolean(app.payment_status?.is_paid);
  const statusLabel = isFree ? 'Miễn thu lệ phí' : (isPaid ? 'Đã thu lệ phí / Có biên lai' : 'Chưa nộp phí');
  const statusBg = isFree ? '#f1f5f9' : (isPaid ? '#dcfce7' : '#fef3c7');
  const statusColor = isFree ? '#475569' : (isPaid ? '#15803d' : '#92400e');
  const paymentMethod = rawData.payment_method || (app.delivery_method === 'Trực tuyến' ? 'Trực tuyến qua VietQR' : 'Thu trực tiếp tại Quầy Một cửa');

  const container = el('div', { style: 'display: flex; flex-direction: column; gap: 1.25rem;' });

  const kpiGrid = el('div', {
    style: 'display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;',
  }, [
    el('div', { class: 'card', style: 'padding: 1.25rem; background: #ffffff; border: 1px solid #d0d7de; border-top: 3px solid #004482; border-radius: 6px;' }, [
      el('span', { style: 'font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;' }, 'Tổng mức thu lệ phí'),
      el('div', { style: 'font-size: 22px; font-weight: 800; color: #004482; margin: 0.35rem 0 0.2rem;' },
        isFree ? '0 đ' : `${totalFee.toLocaleString('vi-VN')} đ`
      ),
      isFree ? el('span', { style: 'font-size: 11.5px; color: #64748b;' }, 'Thủ tục không áp dụng thu phí') : null,
    ]),
    el('div', { class: 'card', style: 'padding: 1.25rem; background: #ffffff; border: 1px solid #d0d7de; border-top: 3px solid #16a34a; border-radius: 6px;' }, [
      el('span', { style: 'font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;' }, 'Tình trạng thanh toán'),
      el('div', { style: 'margin: 0.45rem 0 0;' }, [
        el('span', {
          style: `font-size: 12.5px; font-weight: 700; padding: 0.25rem 0.65rem; border-radius: 4px; background: ${statusBg}; color: ${statusColor}; border: 1px solid ${statusBg};`,
        }, statusLabel),
      ]),
    ]),
    el('div', { class: 'card', style: 'padding: 1.25rem; background: #ffffff; border: 1px solid #d0d7de; border-top: 3px solid #0284c7; border-radius: 6px;' }, [
      el('span', { style: 'font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;' }, 'Hình thức thu nộp'),
      el('div', { style: 'font-size: 13.5px; font-weight: 700; color: #0f172a; margin: 0.45rem 0 0;' }, paymentMethod),
    ]),
  ]);

  const feeDetailsCard = el('div', { class: 'card', style: 'padding: 1.25rem 1.5rem; background: #ffffff; border: 1px solid #d0d7de; border-radius: 6px;' }, [
    el('div', { style: 'display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;' }, [
      el('div', { style: 'font-size: 13.5px; font-weight: 800; color: #004482; text-transform: uppercase;' }, 'DANH MỤC CÁC KHOẢN THU PHÍ & LỆ PHÍ'),
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
        el('strong', { style: 'color: #004482;' }, isFree ? 'Miễn phí' : `${totalFee.toLocaleString('vi-VN')} đ`),
      ]),
    ]),
  ]);

  const qrSection = (!isPaid && !isFree) ? el('div', {
    class: 'card',
    style: 'border: 1px dashed #004482; border-radius: 6px; padding: 1.5rem; background: #f0f7ff; text-align: center;',
  }, [
    el('h4', { style: 'font-size: 14px; font-weight: 800; color: #004482; margin: 0 0 1rem; text-transform: uppercase;' }, 'THANH TOÁN TRỰC TUYẾN QUA VIETQR'),
    el('div', { style: 'display: flex; justify-content: center;' }, [
      el('img', {
        src: `https://img.vietqr.io/image/MB-914040399999-compact2.png?amount=${Math.round(totalFee)}&addInfo=${encodeURIComponent(appId)}`,
        alt: 'VietQR',
        style: 'max-width: 220px; border-radius: 6px; border: 1px solid #cbd5e1; background: #ffffff; padding: 0.35rem;',
      }),
    ]),
  ]) : null;

  container.append(kpiGrid, feeDetailsCard, ...(qrSection ? [qrSection] : []));
  return container;
}
