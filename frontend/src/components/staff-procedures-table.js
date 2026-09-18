import { el } from './dom.js';

/**
 * Renders the procedures data table
 */
export function createProceduresTable({ items = [], isAdmin = false, onDetail, onEdit }) {
  if (!items.length) {
    return el('div', {
      style: 'padding: 3rem; text-align: center; color: #94a3b8; font-size: 13.5px;',
    }, 'Không tìm thấy thủ tục hành chính nào phù hợp.');
  }

  return el('table', {
    class: 'table',
    style: 'width: 100%; border-collapse: collapse; font-size: 13px; min-width: 760px;',
  }, [
    el('thead', {}, [
      el('tr', { style: 'background: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left;' }, [
        el('th', { style: 'padding: 0.75rem 1rem; width: 100px; font-weight: 700; color: #475569;' }, 'Mã TTHC'),
        el('th', { style: 'padding: 0.75rem 1rem; font-weight: 700; color: #475569;' }, 'Tên thủ tục hành chính'),
        el('th', { style: 'padding: 0.75rem 1rem; width: 160px; font-weight: 700; color: #475569;' }, 'Lĩnh vực'),
        el('th', { style: 'padding: 0.75rem 1rem; width: 130px; font-weight: 700; color: #475569;' }, 'Trạng thái'),
        el('th', { style: 'padding: 0.75rem 1rem; width: 160px; text-align: right; font-weight: 700; color: #475569;' }, 'Thao tác'),
      ]),
    ]),
    el('tbody', {},
      items.map((proc) => {
        const isPublic = proc.status === 'Công khai';
        return el('tr', { style: 'border-bottom: 1px solid #f1f5f9;' }, [
          el('td', { style: 'padding: 0.75rem 1rem; font-weight: 700; color: #004482;' }, `TTHC-${proc.id}`),
          el('td', { style: 'padding: 0.75rem 1rem; font-weight: 600; color: #0f172a; line-height: 1.4;' }, proc.name),
          el('td', { style: 'padding: 0.75rem 1rem; color: #475569;' }, proc.field_name || '–'),
          el('td', { style: 'padding: 0.75rem 1rem;' }, [
            el('span', {
              style: `display: inline-block; padding: 0.2rem 0.5rem; border-radius: 3px; font-size: 11px; font-weight: 700; background: ${isPublic ? '#dcfce7' : '#f1f5f9'}; color: ${isPublic ? '#15803d' : '#475569'};`,
            }, proc.status || 'Chờ công khai'),
          ]),
          el('td', { style: 'padding: 0.75rem 1rem; text-align: right;' }, [
            el('div', { style: 'display: inline-flex; gap: 0.35rem;' }, [
              el('button', {
                type: 'button',
                class: 'btn btn-secondary btn-sm',
                style: 'border: 1px solid #cbd5e1; font-size: 11.5px; padding: 0.2rem 0.5rem;',
                onClick: () => { if (onDetail) onDetail(proc.id); },
              }, 'Chi tiết'),
              isAdmin ? el('button', {
                type: 'button',
                class: 'btn btn-secondary btn-sm',
                style: 'border: 1px solid #94a3b8; font-size: 11.5px; padding: 0.2rem 0.5rem; color: #004482; font-weight: 700;',
                onClick: () => { if (onEdit) onEdit(proc); },
              }, 'Sửa') : null,
            ]),
          ]),
        ]);
      })
    ),
  ]);
}

/**
 * Renders pagination controls
 */
export function createProceduresPagination({ pagination, onPageChange }) {
  const { page, total, last_page } = pagination;
  const bar = el('div', {
    style: 'display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; font-size: 13px; color: #64748b;',
  }, [
    el('div', {}, `Hiển thị trang ${page} / ${last_page || 1} (Tổng số ${total} thủ tục)`),
  ]);

  const btnGroup = el('div', { style: 'display: flex; gap: 0.5rem;' });

  if (page > 1) {
    btnGroup.append(
      el('button', {
        type: 'button',
        class: 'btn btn-secondary btn-sm',
        style: 'border: 1px solid #cbd5e1; font-size: 12px;',
        onClick: () => { if (onPageChange) onPageChange(page - 1); },
      }, '« Trang trước')
    );
  }

  if (page < last_page) {
    btnGroup.append(
      el('button', {
        type: 'button',
        class: 'btn btn-secondary btn-sm',
        style: 'border: 1px solid #cbd5e1; font-size: 12px;',
        onClick: () => { if (onPageChange) onPageChange(page + 1); },
      }, 'Trang sau »')
    );
  }

  bar.append(btnGroup);
  return bar;
}
