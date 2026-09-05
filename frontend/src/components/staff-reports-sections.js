import { el } from './dom.js';

export function createKpiCard(label, val, note, accentColor, bgColor) {
  return el('div', {
    class: 'card',
    style: `padding: 1.25rem; border-left: 4px solid ${accentColor}; background: ${bgColor};`,
  }, [
    el('div', { style: 'font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;' }, label),
    el('div', { style: `font-size: 24px; font-weight: 800; color: ${accentColor}; margin: 0.4rem 0 0.2rem 0;` }, val),
    el('div', { style: 'font-size: 12px; color: #64748b;' }, note),
  ]);
}

export function createStatusBreakdownSection(statusList, total) {
  const card = el('div', { class: 'card', style: 'padding: 1.25rem;' }, [
    el('h3', { style: 'font-size: 14px; font-weight: 800; color: #004482; margin: 0 0 1rem 0; text-transform: uppercase;' },
      'Phân bố hồ sơ theo trạng thái xử lý'
    ),
  ]);

  if (!statusList.length) {
    card.append(el('div', { style: 'padding: 1.5rem; text-align: center; color: #94a3b8; font-size: 13px;' }, 'Không có dữ liệu trong khoảng thời gian này.'));
    return card;
  }

  const list = el('div', { style: 'display: flex; flex-direction: column; gap: 0.85rem;' });

  for (const item of statusList) {
    const pct = total > 0 ? Math.round((item.total / total) * 100) : 0;
    list.append(
      el('div', {}, [
        el('div', { style: 'display: flex; justify-content: space-between; font-size: 13px; font-weight: 600; margin-bottom: 0.25rem;' }, [
          el('span', { style: 'color: #1e293b;' }, `${item.status_name || 'Không xác định'}`),
          el('span', { style: 'color: #004482;' }, `${item.total} hồ sơ (${pct}%)`),
        ]),
        el('div', { style: 'height: 8px; width: 100%; background: #e2e8f0; border-radius: 4px; overflow: hidden;' }, [
          el('div', {
            style: `height: 100%; width: ${pct}%; background: #004482; border-radius: 4px; transition: width 0.3s;`,
          }),
        ]),
      ])
    );
  }

  card.append(list);
  return card;
}

export function createProcedureBreakdownSection(procedureList, total) {
  const card = el('div', { class: 'card', style: 'padding: 1.25rem;' }, [
    el('h3', { style: 'font-size: 14px; font-weight: 800; color: #004482; margin: 0 0 1rem 0; text-transform: uppercase;' },
      'Thủ tục hành chính có hồ sơ phát sinh'
    ),
  ]);

  if (!procedureList.length) {
    card.append(el('div', { style: 'padding: 1.5rem; text-align: center; color: #94a3b8; font-size: 13px;' }, 'Không có dữ liệu trong khoảng thời gian này.'));
    return card;
  }

  const table = el('table', {
    class: 'table',
    style: 'width: 100%; border-collapse: collapse; font-size: 13px;',
  }, [
    el('thead', {}, [
      el('tr', { style: 'background: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left;' }, [
        el('th', { style: 'padding: 0.5rem; font-weight: 700; color: #475569;' }, 'Tên thủ tục'),
        el('th', { style: 'padding: 0.5rem; text-align: right; font-weight: 700; color: #475569; width: 80px;' }, 'Số lượng'),
        el('th', { style: 'padding: 0.5rem; text-align: right; font-weight: 700; color: #475569; width: 70px;' }, 'Tỷ lệ'),
      ]),
    ]),
    el('tbody', {},
      procedureList.slice(0, 10).map((proc) => {
        const pct = total > 0 ? Math.round((proc.total / total) * 100) : 0;
        return el('tr', { style: 'border-bottom: 1px solid #f1f5f9;' }, [
          el('td', { style: 'padding: 0.5rem; font-weight: 500; color: #1e293b;' }, proc.procedure_name || `#${proc.procedure_id}`),
          el('td', { style: 'padding: 0.5rem; text-align: right; font-weight: 700; color: #004482;' }, String(proc.total)),
          el('td', { style: 'padding: 0.5rem; text-align: right; color: #64748b;' }, `${pct}%`),
        ]);
      })
    ),
  ]);

  card.append(table);
  return card;
}

export function createRevenueBreakdownSection(dailyList) {
  const card = el('div', { class: 'card', style: 'padding: 1.25rem;' }, [
    el('h3', { style: 'font-size: 14px; font-weight: 800; color: #004482; margin: 0 0 1rem 0; text-transform: uppercase;' },
      'Chi tiết thu phí, lệ phí theo từng ngày'
    ),
  ]);

  if (!dailyList.length) {
    card.append(el('div', { style: 'padding: 1.5rem; text-align: center; color: #94a3b8; font-size: 13px;' }, 'Không có giao dịch thu phí nào trong khoảng thời gian này.'));
    return card;
  }

  const table = el('table', {
    class: 'table',
    style: 'width: 100%; border-collapse: collapse; font-size: 13px;',
  }, [
    el('thead', {}, [
      el('tr', { style: 'background: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left;' }, [
        el('th', { style: 'padding: 0.6rem 0.75rem; font-weight: 700; color: #475569;' }, 'Ngày'),
        el('th', { style: 'padding: 0.6rem 0.75rem; text-align: center; font-weight: 700; color: #475569; width: 140px;' }, 'Số giao dịch'),
        el('th', { style: 'padding: 0.6rem 0.75rem; text-align: right; font-weight: 700; color: #475569; width: 180px;' }, 'Số tiền thực thu'),
      ]),
    ]),
    el('tbody', {},
      dailyList.map((day) => {
        return el('tr', { style: 'border-bottom: 1px solid #f1f5f9;' }, [
          el('td', { style: 'padding: 0.6rem 0.75rem; font-weight: 600; color: #1e293b;' }, day.date),
          el('td', { style: 'padding: 0.6rem 0.75rem; text-align: center; color: #334155;' }, String(day.transaction_count)),
          el('td', { style: 'padding: 0.6rem 0.75rem; text-align: right; font-weight: 700; color: #15803d;' },
            Number(day.total_amount || 0).toLocaleString('vi-VN') + ' đ'
          ),
        ]);
      })
    ),
  ]);

  card.append(table);
  return card;
}
