import { el } from './dom.js';
import { createPerformanceRingCard } from './staff-dashboard-ring.js';

/**
 * Render visual charts for staff dashboard:
 * 1. Status Distribution (multi-segment progress + status list bars)
 * 2. Performance & Completion Ring Chart (SVG donut + KPI indicators + previous period comparison)
 * 3. Top Procedures Bar Chart (ranked horizontal bars)
 */
export function renderDashboardCharts({
  statusList = [],
  procedureList = [],
  totalCount = 0,
  prevReportData = null,
  periodInfo = null,
  onNavigate,
}) {
  const container = el('div', {
    class: 'staff-dashboard-charts',
    style: 'display: flex; flex-direction: column; gap: 1.25rem;',
  });

  const topRow = el('div', {
    style: 'display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 1.25rem;',
  }, [
    createStatusDistributionCard(statusList, totalCount, onNavigate),
    createPerformanceRingCard({ statusList, totalCount, prevReportData, periodInfo }),
  ]);

  const bottomRow = createProcedureDistributionCard(procedureList, totalCount, onNavigate);

  container.append(topRow, bottomRow);
  return container;
}

function getStatusColor(statusId) {
  const id = Number(statusId);
  if (id === 1) return { bg: '#fef3c7', text: '#b45309', bar: '#d97706', label: 'Chờ tiếp nhận' };
  if (id === 2 || id === 3) return { bg: '#eff6ff', text: '#0284c7', bar: '#0284c7', label: 'Đang thụ lý' };
  if (id === 4) return { bg: '#f5f3ff', text: '#6d28d9', bar: '#7c3aed', label: 'Chờ phê duyệt' };
  if (id === 5 || id === 6) return { bg: '#fff7ed', text: '#c2410c', bar: '#ea580c', label: 'Yêu cầu bổ sung' };
  if (id === 9) return { bg: '#f0fdf4', text: '#15803d', bar: '#16a34a', label: 'Đã xử lý xong' };
  if (id === 10) return { bg: '#f8fafc', text: '#334155', bar: '#475569', label: 'Đã trả kết quả' };
  if (id === 12) return { bg: '#fef2f2', text: '#b91c1c', bar: '#dc2626', label: 'Yêu cầu xử lý lại' };
  return { bg: '#f1f5f9', text: '#475569', bar: '#64748b', label: 'Khác' };
}

function createStatusDistributionCard(statusList, totalCount, onNavigate) {
  const card = el('div', {
    class: 'card',
    style: 'padding: 1.25rem 1.5rem; background: #ffffff; border: 1px solid #d0d7de; border-radius: 6px;',
  }, [
    el('div', { style: 'display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem;' }, [
      el('h3', { style: 'font-size: 13.5px; font-weight: 800; color: #004482; margin: 0; text-transform: uppercase;' }, 'Cơ cấu hồ sơ theo trạng thái'),
      el('span', { style: 'font-size: 12px; color: #64748b; font-weight: 600;' }, `Tổng: ${totalCount} hồ sơ`),
    ]),
  ]);

  if (!statusList || statusList.length === 0) {
    card.append(el('div', { style: 'padding: 2rem; text-align: center; color: #94a3b8; font-size: 13px;' }, 'Chưa có dữ liệu thống kê trạng thái.'));
    return card;
  }

  const stackedBar = el('div', {
    style: 'height: 10px; width: 100%; display: flex; border-radius: 5px; overflow: hidden; background: #e2e8f0; margin-bottom: 1.15rem;',
  });
  statusList.forEach(s => {
    const total = s.total ?? s.value ?? 0;
    if (total <= 0) return;
    const pct = totalCount > 0 ? (total / totalCount) * 100 : 0;
    const cfg = getStatusColor(s.status_id ?? s.id ?? String(s.key).replace('status_', ''));
    stackedBar.append(el('div', {
      style: `height: 100%; width: ${pct}%; background: ${cfg.bar};`,
      title: `${cfg.label}: ${total} (${pct.toFixed(1)}%)`,
    }));
  });

  const listContainer = el('div', { style: 'display: flex; flex-direction: column; gap: 0.65rem;' });
  statusList.forEach(s => {
    const total = s.total ?? s.value ?? 0;
    const id = s.status_id ?? s.id ?? String(s.key).replace('status_', '');
    const cfg = getStatusColor(id);
    const pct = totalCount > 0 ? Math.round((total / totalCount) * 100) : 0;

    const row = el('div', {
      style: 'display: flex; flex-direction: column; gap: 0.25rem; cursor: pointer; padding: 0.25rem 0.4rem; border-radius: 4px; transition: background 0.15s ease;',
      onClick: () => { if (onNavigate) onNavigate(`/can-bo/ho-so?status=${id}`); },
      onmouseenter: (e) => { e.currentTarget.style.background = '#f8fafc'; },
      onmouseleave: (e) => { e.currentTarget.style.background = 'transparent'; },
    }, [
      el('div', { style: 'display: flex; justify-content: space-between; align-items: center; font-size: 12.5px;' }, [
        el('div', { style: 'display: flex; align-items: center; gap: 0.5rem;' }, [
          el('span', { style: `width: 8px; height: 8px; border-radius: 50%; background: ${cfg.bar}; display: inline-block;` }),
          el('span', { style: 'color: #334155; font-weight: 600;' }, s.status_name || cfg.label),
        ]),
        el('div', { style: 'display: flex; gap: 0.5rem; align-items: center;' }, [
          el('span', { class: 'numeric-data', style: 'font-weight: 700; color: #0f172a;' }, `${total} hồ sơ`),
          el('span', { style: 'font-size: 11px; font-weight: 600; color: #64748b; width: 42px; text-align: right;' }, `${pct}%`),
        ]),
      ]),
      el('div', { style: 'height: 6px; width: 100%; background: #f1f5f9; border-radius: 3px; overflow: hidden;' }, [
        el('div', { style: `height: 100%; width: ${pct}%; background: ${cfg.bar}; border-radius: 3px; transition: width 0.3s ease;` }),
      ]),
    ]);
    listContainer.append(row);
  });

  card.append(stackedBar, listContainer);
  return card;
}

function createProcedureDistributionCard(procedureList, totalCount, onNavigate) {
  const card = el('div', {
    class: 'card',
    style: 'padding: 1.25rem 1.5rem; background: #ffffff; border: 1px solid #d0d7de; border-radius: 6px;',
  }, [
    el('div', { style: 'display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;' }, [
      el('h3', { style: 'font-size: 13.5px; font-weight: 800; color: #004482; margin: 0; text-transform: uppercase;' }, 'Top thủ tục hành chính phát sinh nhiều hồ sơ nhất'),
      el('button', {
        type: 'button', class: 'btn btn-ghost btn-sm',
        style: 'font-size: 12px; color: #004b87; font-weight: 700;',
        onClick: () => { if (onNavigate) onNavigate('/can-bo/ho-so'); },
      }, 'Xem danh sách hồ sơ →'),
    ]),
  ]);

  if (!procedureList || procedureList.length === 0) {
    card.append(el('div', { style: 'padding: 2rem; text-align: center; color: #94a3b8; font-size: 13px;' }, 'Chưa có dữ liệu thủ tục hành chính.'));
    return card;
  }

  const topList = procedureList.slice(0, 6);
  const maxVal = Math.max(...topList.map(p => p.total ?? 1));

  const barsGrid = el('div', { style: 'display: flex; flex-direction: column; gap: 0.85rem;' });
  topList.forEach((proc, idx) => {
    const count = proc.total ?? 0;
    const barWidth = maxVal > 0 ? (count / maxVal) * 100 : 0;
    const pct = totalCount > 0 ? ((count / totalCount) * 100).toFixed(1) : 0;

    const row = el('div', {
      style: 'display: flex; flex-direction: column; gap: 0.3rem;',
    }, [
      el('div', { style: 'display: flex; justify-content: space-between; align-items: center; font-size: 12.5px;' }, [
        el('div', { style: 'display: flex; align-items: center; gap: 0.5rem; flex: 1; min-width: 0; padding-right: 1rem;' }, [
          el('span', {
            style: `width: 20px; height: 20px; border-radius: 4px; display: inline-flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800; flex-shrink: 0; ${idx === 0 ? 'background: #004b87; color: #ffffff;' : 'background: #eff6ff; color: #004b87; border: 1px solid #bfdbfe;'}`,
          }, String(idx + 1)),
          el('span', { style: 'font-weight: 600; color: #1e293b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;' }, proc.procedure_name || `Thủ tục #${proc.procedure_id}`),
        ]),
        el('div', { style: 'display: flex; gap: 0.5rem; align-items: center; flex-shrink: 0;' }, [
          el('span', { class: 'numeric-data', style: 'font-weight: 700; color: #0f172a;' }, `${count} hồ sơ`),
          el('span', { style: 'font-size: 11px; font-weight: 600; color: #64748b; width: 44px; text-align: right;' }, `(${pct}%)`),
        ]),
      ]),
      el('div', { style: 'height: 7px; width: 100%; background: #f1f5f9; border-radius: 3.5px; overflow: hidden;' }, [
        el('div', {
          style: `height: 100%; width: ${barWidth}%; background: linear-gradient(90deg, #004b87, #0284c7); border-radius: 3.5px; transition: width 0.4s ease;`,
        }),
      ]),
    ]);
    barsGrid.append(row);
  });

  card.append(barsGrid);
  return card;
}
