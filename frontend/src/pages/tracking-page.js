import { el } from '../components/dom.js';
import { api } from '../api/client.js';
import { renderStatusBadge } from '../components/status-badge.js';

export function renderTrackingPage({ navigate, searchParams }) {
  const container = el('div', { class: 'container-portal', style: 'padding-top: 2rem; padding-bottom: 3rem;' });

  const breadcrumb = el('div', {
    style: 'font-size: 12px; color: #64748b; margin-bottom: 1.5rem;',
  }, [
    el('a', { href: '/', style: 'color: #004482; text-decoration: none;', onClick: (e) => { e.preventDefault(); navigate('/'); } }, 'Trang chủ'),
    el('span', { style: 'margin: 0 0.5rem;' }, '›'),
    el('span', { style: 'font-weight: 600; color: #0f172a;' }, 'Tra cứu hồ sơ'),
  ]);

  const pageHeader = el('div', { style: 'margin-bottom: 2rem;' }, [
    el('h1', { style: 'font-family: var(--font-heading); font-size: 24px; font-weight: 800; color: #004482; margin-bottom: 0.5rem;' }, 'Tra cứu tiến độ giải quyết hồ sơ'),
  ]);

  const codeInput = el('input', {
    type: 'text',
    class: 'input numeric-data',
    id: 'track-code-input',
    placeholder: 'Nhập mã hồ sơ (ví dụ: HS-2025-00125)...',
    required: true,
  });

  const pinInput = el('input', {
    type: 'tel',
    class: 'input numeric-data',
    id: 'track-pin-input',
    placeholder: 'Nhập số điện thoại đăng ký hồ sơ...',
    required: true,
  });

  const resultContainer = el('div', { style: 'margin-top: 2rem;' });

  const form = el('form', {
    class: 'card',
    style: 'padding: 2rem; max-width: 680px; background: #ffffff; border: 1px solid #d0d7de; border-radius: 4px;',
    onSubmit: async (e) => {
      e.preventDefault();
      const maHSXL = codeInput.value.trim();
      const soDienThoai = pinInput.value.trim();

      if (!maHSXL || !soDienThoai) return;

      resultContainer.replaceChildren(el('div', {
        style: 'text-align: center; padding: 2rem; color: #64748b;',
      }, 'Đang tra cứu thông tin hồ sơ...'));

      try {
        const res = await api.post('/public/application-tracking', { code: maHSXL, verification: soDienThoai });
        const app = res.data || res;
        resultContainer.replaceChildren(renderTrackingResult(app, maHSXL));
      } catch (err) {
        resultContainer.replaceChildren(el('div', {
          class: 'card',
          style: 'padding: 1.5rem; background: #ffffff; border: 1px solid #d0d7de; color: #b91c1c; font-size: 13px;',
        }, [
          el('strong', {}, 'Không tìm thấy hồ sơ: '),
          el('span', {}, err.message || 'Mã hồ sơ hoặc số điện thoại xác thực không chính xác. Vui lòng kiểm tra lại.'),
        ]));
      }
    },
  }, [
    el('div', { style: 'display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;' }, [
      el('div', {}, [
        el('label', { for: 'track-code-input', style: 'display: block; font-size: 12px; font-weight: 700; margin-bottom: 0.35rem; color: #0f172a;' }, 'MÃ HỒ SƠ *'),
        codeInput,
      ]),
      el('div', {}, [
        el('label', { for: 'track-pin-input', style: 'display: block; font-size: 12px; font-weight: 700; margin-bottom: 0.35rem; color: #0f172a;' }, 'SỐ ĐIỆN THOẠI XÁC THỰC *'),
        pinInput,
      ]),
    ]),
    el('div', { style: 'display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;' }, [
      el('span', { style: 'font-size: 12px; color: #64748b;' }, 'Ví dụ: HSXL_20260904_0001, SĐT: 0983112233'),
      el('button', { type: 'submit', class: 'btn btn-primary', style: 'background: #004482; font-weight: 700;' }, 'Tra cứu hồ sơ'),
    ]),
  ]);

  const initialCode = searchParams?.get('code') || '';
  const initialPhone = searchParams?.get('phone') || '';
  if (initialCode) codeInput.value = initialCode;
  if (initialPhone) pinInput.value = initialPhone;
  if (initialCode && initialPhone) {
    setTimeout(() => {
      form.dispatchEvent(new Event('submit', { cancelable: true }));
    }, 50);
  }

  container.append(breadcrumb, pageHeader, form, resultContainer);
  return container;
}

export function buildTrackingRows(app, fallbackCode) {
  const occurredAt = app.timeline?.[0]?.occurred_at
    ? new Date(app.timeline[0].occurred_at).toLocaleString('vi-VN')
    : 'Chưa ghi nhận';
  const statusLabel = app.status?.label || app.status?.name || 'Đang xử lý';
  return [
    ['Mã hồ sơ tiếp nhận', app.application_code || fallbackCode],
    ['Tên thủ tục hành chính', app.procedure_name || 'Chưa ghi nhận'],
    ['Cơ quan giải quyết', app.agency_name || 'Ủy ban nhân dân cấp Xã / Phường'],
    ['Trạng thái hiện tại', statusLabel],
    ['Thời điểm cập nhật', occurredAt],
  ];
}

function renderTrackingResult(app, fallbackCode) {
  const statusId = app.status?.id || 4;
  const statusLabel = app.status?.label || app.status?.name || 'Đang xử lý';
  const statusBadge = renderStatusBadge(statusId, statusLabel);
  const rows = buildTrackingRows(app, fallbackCode);

  return el('div', { class: 'card', style: 'padding: 2rem; max-width: 680px; background: #ffffff; border: 1px solid #d0d7de; border-radius: 4px;' }, [
    el('div', { style: 'display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem;' }, [
      el('h3', { style: 'font-family: var(--font-heading); font-size: 16px; font-weight: 800; color: #004482; margin: 0;' }, 'KẾT QUẢ TRA CỨU TIẾN ĐỘ'),
      statusBadge,
    ]),
    el('div', { style: 'overflow-x: auto;' }, [
      el('table', { style: 'width: 100%; border-collapse: collapse; min-width: 520px; font-size: 13px;' }, [
        el('thead', {}, [el('tr', { style: 'background: #f8fafc; border-bottom: 1px solid #e2e8f0;' }, [
          el('th', { style: 'padding: 0.7rem 0.75rem; text-align: left; color: #475569; font-weight: 700; width: 42%;' }, 'Nội dung'),
          el('th', { style: 'padding: 0.7rem 0.75rem; text-align: left; color: #475569; font-weight: 700;' }, 'Thông tin'),
        ])]),
        el('tbody', {}, rows.map(([label, val]) => el('tr', { style: 'border-bottom: 1px solid #f1f5f9;' }, [
          el('td', { style: 'padding: 0.7rem 0.75rem; color: #475569; font-weight: 600;' }, label),
          el('td', { class: 'numeric-data', style: 'padding: 0.7rem 0.75rem; color: #0f172a; font-weight: 700;' }, String(val)),
        ]))),
      ]),
    ]),
  ]);
}
