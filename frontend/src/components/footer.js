import { el } from './dom.js';

export function renderFooter() {
  const col1 = el('div', { class: 'footer-col' }, [
    el('div', { style: 'font-size: 11px; font-weight: 700; color: #004b87; text-transform: uppercase; margin-bottom: 0.25rem;' }, 'CƠ QUAN CHỦ QUẢN'),
    el('h3', { style: 'font-family: var(--font-heading); font-size: 14.5px; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem;' }, 'ỦY BAN NHÂN DÂN XÃ ABC'),
    el('p', { style: 'font-size: 12px; color: #475569; line-height: 1.5; margin-bottom: 0.75rem;' }, 'CỔNG THÔNG TIN DỊCH VỤ CÔNG ĐIỆN TỬ LIÊN THÔNG'),
    el('div', { style: 'font-size: 12px; color: #64748b; display: flex; flex-direction: column; gap: 0.35rem;' }, [
      el('div', {}, 'Địa chỉ: 12 Nguyễn Văn Bảo, Hạnh Thông, Hồ Chí Minh'),
      el('div', {}, 'Điện thoại: 1900 6886 • Hotline giải quyết TTHC'),
      el('div', {}, 'Thư điện tử: abc@gov.vn'),
      el('div', {}, 'Giờ làm việc: 07:30 - 17:00 (Từ thứ Hai đến thứ Sáu)'),
    ]),
  ]);

  const col2 = el('div', { class: 'footer-col' }, [
    el('h4', { style: 'font-size: 12px; font-weight: 700; color: #0f172a; text-transform: uppercase; margin-bottom: 0.75rem;' }, 'Liên kết hữu ích'),
    el('ul', { style: 'list-style: none; font-size: 12px; line-height: 2; color: #475569; padding: 0;' }, [
      el('li', {}, el('a', { href: 'https://dichvucong.gov.vn', target: '_blank', style: 'color: #475569; text-decoration: none;' }, '› Cổng Dịch vụ công Quốc gia')),
      el('li', {}, el('a', { href: 'https://chinhphu.vn', target: '_blank', style: 'color: #475569; text-decoration: none;' }, '› Cổng Thông tin điện tử Chính phủ')),
      el('li', {}, el('a', { href: '#', style: 'color: #475569; text-decoration: none;' }, '› Cơ sở dữ liệu quốc gia về dân cư')),
      el('li', {}, el('a', { href: '#', style: 'color: #475569; text-decoration: none;' }, '› Hệ thống Quản trị Văn bản và Điều hành')),
    ]),
  ]);

  const col3 = el('div', { class: 'footer-col' }, [
    el('h4', { style: 'font-size: 12px; font-weight: 700; color: #0f172a; text-transform: uppercase; margin-bottom: 0.75rem;' }, 'Hỗ trợ & Tra cứu'),
    el('div', {
      style: 'background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 1rem;',
    }, [
      el('div', { style: 'font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;' }, 'ĐƯỜNG DÂY NÓNG HỖ TRỢ 24/7'),
      el('div', { style: 'font-size: 22px; font-weight: 800; color: #004b87; margin: 0.25rem 0 0;' }, '1900 6886'),
    ]),
  ]);

  const footerBottom = el('div', { class: 'footer-bottom', style: 'padding-top: 1rem; border-top: 1px solid #e2e8f0; margin-top: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem; font-size: 12px;' }, [
    el('div', { style: 'display: flex; gap: 1.5rem;' }, [
      el('a', { href: '#', style: 'color: #64748b; text-decoration: none;' }, 'Điều khoản sử dụng'),
      el('a', { href: '#', style: 'color: #64748b; text-decoration: none;' }, 'Chính sách bảo mật'),
      el('a', { href: '#', style: 'color: #64748b; text-decoration: none;' }, 'Tiêu chuẩn an toàn thông tin'),
    ]),
    el('div', { style: 'color: #64748b;' },
      'Bản quyền © 2026 Cổng Dịch vụ công Trực tuyến Cấp Cơ sở. Bản quyền thuộc về UBND xã ABC.'),
  ]);

  return el('footer', { class: 'portal-footer', style: 'background: #ffffff; border-top: 1px solid #e2e8f0; margin-top: auto; padding: 2.5rem 0 1rem;' }, [
    el('div', { class: 'container-portal' }, [
      el('div', { class: 'footer-grid', style: 'display: grid; grid-template-columns: 2fr 1fr 1.2fr; gap: 2rem;' }, col1, col2, col3),
      footerBottom,
    ]),
  ]);
}
