import { el } from './dom.js';

export const STATUS_MAP = {
  1:  { label: 'Chờ tiếp nhận',       css: 'badge-1' },
  2:  { label: 'Đã tiếp nhận',        css: 'badge-2' },
  3:  { label: 'Từ chối tiếp nhận',    css: 'badge-3' },
  4:  { label: 'Đang xử lý',          css: 'badge-4' },
  5:  { label: 'Cần bổ sung',         css: 'badge-5' },
  6:  { label: 'Chờ phê duyệt',       css: 'badge-6' },
  7:  { label: 'Đang ký số',          css: 'badge-7' },
  8:  { label: 'Chờ trả kết quả',     css: 'badge-8' },
  9:  { label: 'Đã xử lý xong',       css: 'badge-9' },
  10: { label: 'Đã trả kết quả',      css: 'badge-10' },
  11: { label: 'Tiếp nhận trực tiếp', css: 'badge-11' },
};

/**
 * renderStatusBadge
 *
 * @param {number|string} statusId
 * @param {string} [customLabel]
 * @returns {HTMLElement}
 */
export function renderStatusBadge(statusId, customLabel) {
  const meta = STATUS_MAP[Number(statusId)] || {
    label: customLabel || 'Không xác định',
    css: 'badge-1',
  };

  return el('span', {
    class: `badge ${meta.css}`,
    role: 'status',
  }, customLabel || meta.label);
}
