import { el } from './dom.js';
import { computeReportComparison } from './dashboard-comparison.js';

/**
 * Renders the "Hiệu suất xử lý & Tiến độ" card with previous period comparison
 *
 * @param {object} options
 * @param {Array<any>} options.statusList
 * @param {number} options.totalCount
 * @param {any} [options.prevReportData]
 * @param {object} [options.periodInfo]
 * @returns {HTMLElement}
 */
export function createPerformanceRingCard({ statusList, totalCount, prevReportData, periodInfo }) {
  const comp = computeReportComparison(statusList, totalCount, prevReportData);

  const inProgressCount = statusList.reduce((acc, s) => {
    const id = Number(s.status_id ?? s.id ?? String(s.key).replace('status_', ''));
    if (id === 2 || id === 3 || id === 4 || id === 12) return acc + (Number(s.total ?? s.value) || 0);
    return acc;
  }, 0);
  const inProgressRate = totalCount > 0 ? Math.round((inProgressCount / totalCount) * 100) : 0;

  const supplementCount = statusList.reduce((acc, s) => {
    const id = Number(s.status_id ?? s.id ?? String(s.key).replace('status_', ''));
    if (id === 5 || id === 6) return acc + (Number(s.total ?? s.value) || 0);
    return acc;
  }, 0);
  const supplementRate = totalCount > 0 ? Math.round((supplementCount / totalCount) * 100) : 0;

  const radius = 48;
  const circ = 2 * Math.PI * radius;
  const offset = circ * (1 - (comp.currRate / 100));

  const svgRing = el('div', {
    style: 'display: flex; flex-direction: column; align-items: center; justify-content: center; flex-shrink: 0;',
  }, [
    el('div', {
      style: 'position: relative; width: 130px; height: 130px; display: flex; justify-content: center; align-items: center;',
    }, [
      el('div', {
        style: 'width: 100%; height: 100%;',
      }, []),
    ]),
  ]);

  svgRing.children[0].innerHTML = `
    <svg width="130" height="130" viewBox="0 0 130 130">
      <circle cx="65" cy="65" r="${radius}" fill="none" stroke="#e2e8f0" stroke-width="12" />
      <circle cx="65" cy="65" r="${radius}" fill="none" stroke="#16a34a" stroke-width="12"
        stroke-dasharray="${circ}" stroke-dashoffset="${offset}" stroke-linecap="round"
        transform="rotate(-90 65 65)" style="transition: stroke-dashoffset 0.5s ease;" />
    </svg>
    <div style="position: absolute; text-align: center;">
      <div style="font-size: 22px; font-weight: 800; color: #15803d; line-height: 1;">${comp.currRate}%</div>
      <div style="font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-top: 3px;">Hoàn thành</div>
    </div>
  `;

  const pLabel = periodInfo?.label || 'kỳ trước';
  let badgeText = `0% so với ${pLabel}`;
  let badgeBg = '#f1f5f9';
  let badgeColor = '#475569';
  let badgeBorder = '#e2e8f0';

  if (comp.rateDiff > 0) {
    badgeText = `↑ +${comp.rateDiff}% so với ${pLabel}`;
    badgeBg = '#dcfce7';
    badgeColor = '#15803d';
    badgeBorder = '#bbf7d0';
  } else if (comp.rateDiff < 0) {
    badgeText = `↓ ${comp.rateDiff}% so với ${pLabel}`;
    badgeBg = '#fee2e2';
    badgeColor = '#b91c1c';
    badgeBorder = '#fecaca';
  }

  const comparisonBadge = el('div', {
    style: `margin-top: 0.5rem; font-size: 11px; font-weight: 700; color: ${badgeColor}; background: ${badgeBg}; border: 1px solid ${badgeBorder}; border-radius: 4px; padding: 0.2rem 0.55rem; display: inline-flex; align-items: center; gap: 0.25rem; white-space: nowrap;`,
  }, badgeText);
  svgRing.append(comparisonBadge);

  const subText = periodInfo?.from && periodInfo?.to
    ? `So sánh với ${pLabel} (${periodInfo.from} → ${periodInfo.to})`
    : `So sánh với ${pLabel}`;

  const subtitle = el('div', {
    style: 'font-size: 11.5px; color: #64748b; margin-top: -0.65rem; margin-bottom: 0.85rem;',
  }, subText);

  const completedDiffLabel = prevReportData
    ? (comp.completedDiff >= 0 ? `+${comp.completedDiff}` : `${comp.completedDiff}`)
    : null;

  const kpiDetails = el('div', { style: 'display: flex; flex-direction: column; gap: 0.65rem; flex: 1; min-width: 240px;' }, [
    createMiniKpi('Đã xử lý xong & Trả kết quả', comp.currCompleted, `${comp.currRate}%`, '#16a34a', '#f0fdf4', completedDiffLabel),
    createMiniKpi('Đang trong quy trình giải quyết', inProgressCount, `${inProgressRate}%`, '#0284c7', '#eff6ff'),
    createMiniKpi('Cần bổ sung hồ sơ từ công dân', supplementCount, `${supplementRate}%`, '#ea580c', '#fff7ed'),
  ]);

  return el('div', {
    class: 'card',
    style: 'padding: 1.25rem 1.5rem; background: #ffffff; border: 1px solid #d0d7de; border-radius: 6px;',
  }, [
    el('h3', { style: 'font-size: 13.5px; font-weight: 800; color: #004482; margin: 0 0 0.85rem 0; text-transform: uppercase;' }, 'Hiệu suất xử lý & Tiến độ'),
    subtitle,
    el('div', { style: 'display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap;' }, [
      svgRing,
      kpiDetails,
    ]),
  ]);
}

function createMiniKpi(label, count, pct, color, bg, diffLabel = null) {
  const children = [
    el('span', { style: 'font-size: 12px; font-weight: 600; color: #334155;' }, label),
    el('div', { style: 'text-align: right; display: flex; align-items: center; gap: 0.35rem;' }, [
      el('span', { class: 'numeric-data', style: `font-size: 14px; font-weight: 800; color: ${color};` }, String(count)),
      el('span', { style: 'font-size: 11px; color: #64748b;' }, `(${pct})`),
      diffLabel ? el('span', {
        style: `font-size: 10.5px; font-weight: 700; padding: 0.1rem 0.35rem; border-radius: 3px; ${diffLabel.startsWith('+') ? 'background: #dcfce7; color: #16a34a;' : 'background: #fee2e2; color: #dc2626;'}`,
      }, diffLabel) : null,
    ]),
  ];

  return el('div', {
    style: `padding: 0.5rem 0.75rem; border-radius: 5px; background: ${bg}; border-left: 3px solid ${color}; display: flex; justify-content: space-between; align-items: center;`,
  }, children);
}
