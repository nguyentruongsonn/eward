import { formatDateYMD } from './report-dates.js';

/**
 * Calculates previous period range and human-readable label
 * based on current date range and preset.
 *
 * @param {string} fromStr 'YYYY-MM-DD'
 * @param {string} toStr   'YYYY-MM-DD'
 * @param {string} [preset]
 * @returns {{ from: string, to: string, label: string, days: number }}
 */
export function getPreviousPeriodRange(fromStr, toStr, preset = '') {
  const [fY, fM, fD] = fromStr.split('-').map(Number);
  const [tY, tM, tD] = toStr.split('-').map(Number);

  const fromDate = new Date(fY, fM - 1, fD);
  const toDate = new Date(tY, tM - 1, tD);

  const diffMs = toDate.getTime() - fromDate.getTime();
  const diffDays = Math.max(1, Math.round(diffMs / (1000 * 60 * 60 * 24)) + 1);

  if (preset === 'today' || diffDays === 1) {
    const yest = new Date(fromDate);
    yest.setDate(yest.getDate() - 1);
    const dStr = formatDateYMD(yest);
    return { from: dStr, to: dStr, label: 'hôm qua', days: 1 };
  }

  if (preset === 'month') {
    const prevMonthStart = new Date(fromDate.getFullYear(), fromDate.getMonth() - 1, 1);
    const prevMonthEnd = new Date(fromDate.getFullYear(), fromDate.getMonth(), 0);
    const mDays = prevMonthEnd.getDate();
    return {
      from: formatDateYMD(prevMonthStart),
      to: formatDateYMD(prevMonthEnd),
      label: 'tháng trước',
      days: mDays,
    };
  }

  const prevTo = new Date(fromDate);
  prevTo.setDate(prevTo.getDate() - 1);
  const prevFrom = new Date(prevTo);
  prevFrom.setDate(prevFrom.getDate() - (diffDays - 1));

  let label = `${diffDays} ngày trước`;
  if (preset === '7d' || diffDays === 7) {
    label = 'tuần trước';
  } else if (diffDays >= 28 && diffDays <= 31) {
    label = 'tháng trước';
  }

  return {
    from: formatDateYMD(prevFrom),
    to: formatDateYMD(prevTo),
    label,
    days: diffDays,
  };
}

/**
 * Computes difference and metrics between current report and previous report
 *
 * @param {Array<any>} currentStatusList
 * @param {number} currentTotal
 * @param {any} [prevReportData]
 * @returns {{
 *   currCompleted: number,
 *   currRate: number,
 *   prevTotal: number,
 *   prevCompleted: number,
 *   prevRate: number,
 *   rateDiff: number,
 *   totalDiff: number,
 *   completedDiff: number
 * }}
 */
export function computeReportComparison(currentStatusList, currentTotal, prevReportData) {
  const sumCompleted = (list) => {
    if (!Array.isArray(list)) return 0;
    return list.reduce((acc, s) => {
      const id = Number(s.status_id ?? s.id ?? String(s.key).replace('status_', ''));
      if (id === 9 || id === 10) {
        return acc + (Number(s.total ?? s.value) || 0);
      }
      return acc;
    }, 0);
  };

  const currCompleted = sumCompleted(currentStatusList);
  const currRate = currentTotal > 0 ? Math.round((currCompleted / currentTotal) * 100) : 0;

  const prevTotal = Number(prevReportData?.total) || 0;
  const prevCompleted = sumCompleted(prevReportData?.by_status);
  const prevRate = prevTotal > 0 ? Math.round((prevCompleted / prevTotal) * 100) : 0;

  return {
    currCompleted,
    currRate,
    prevTotal,
    prevCompleted,
    prevRate,
    rateDiff: currRate - prevRate,
    totalDiff: currentTotal - prevTotal,
    completedDiff: currCompleted - prevCompleted,
  };
}
