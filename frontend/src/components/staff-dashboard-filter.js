import { el } from './dom.js';
import { formatDateYMD, getDefaultReportRange } from './report-dates.js';

/**
 * Toolbar filter for staff dashboard: date range pickers and quick presets
 */
export function createDashboardFilterBar({ initialFrom, initialTo, onFilterChange, onRefresh }) {
  let from = initialFrom || getDefaultReportRange().from;
  let to = initialTo || getDefaultReportRange().to;
  let activePreset = '30d';

  const fromInput = el('input', {
    type: 'date',
    class: 'input',
    value: from,
    style: 'height: 32px; font-size: 12px; padding: 0.2rem 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px; width: 135px;',
    onChange: (e) => { from = e.target.value; activePreset = ''; updatePresetButtons(); },
  });

  const toInput = el('input', {
    type: 'date',
    class: 'input',
    value: to,
    style: 'height: 32px; font-size: 12px; padding: 0.2rem 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px; width: 135px;',
    onChange: (e) => { to = e.target.value; activePreset = ''; updatePresetButtons(); },
  });

  const applyBtn = el('button', {
    type: 'button',
    class: 'btn btn-primary btn-sm',
    style: 'background: #004b87; color: #ffffff; height: 32px; font-weight: 700; padding: 0 0.85rem; border-radius: 4px;',
    onClick: () => { if (onFilterChange) onFilterChange({ from, to, preset: activePreset || 'custom' }); },
  }, 'Lọc dữ liệu');

  const presets = [
    {
      id: 'today',
      label: 'Hôm nay',
      getRange: () => {
        const now = formatDateYMD(new Date());
        return { from: now, to: now };
      },
    },
    {
      id: 'week',
      label: 'Tuần này',
      getRange: () => {
        const now = new Date();
        const day = now.getDay();
        const diff = (day === 0 ? -6 : 1) - day;
        const monday = new Date(now);
        monday.setDate(now.getDate() + diff);
        return { from: formatDateYMD(monday), to: formatDateYMD(now) };
      },
    },
    {
      id: '7d',
      label: '7 ngày qua',
      getRange: () => {
        const end = new Date();
        const st = new Date();
        st.setDate(st.getDate() - 6);
        return { from: formatDateYMD(st), to: formatDateYMD(end) };
      },
    },
    {
      id: 'month',
      label: 'Tháng này',
      getRange: () => {
        const now = new Date();
        const st = new Date(now.getFullYear(), now.getMonth(), 1);
        return { from: formatDateYMD(st), to: formatDateYMD(now) };
      },
    },
    {
      id: '30d',
      label: '30 ngày qua',
      getRange: () => {
        const end = new Date();
        const st = new Date();
        st.setDate(st.getDate() - 29);
        return { from: formatDateYMD(st), to: formatDateYMD(end) };
      },
    },
    {
      id: 'year',
      label: 'Năm nay',
      getRange: () => {
        const now = new Date();
        const st = new Date(now.getFullYear(), 0, 1);
        return { from: formatDateYMD(st), to: formatDateYMD(now) };
      },
    },
  ];

  const presetBtns = presets.map(p => {
    return el('button', {
      type: 'button',
      class: 'btn btn-sm',
      dataset: { preset: p.id },
      style: `height: 30px; font-size: 12px; font-weight: 600; padding: 0 0.65rem; border-radius: 4px; border: 1px solid #cbd5e1; ${activePreset === p.id ? 'background: #004b87; color: #ffffff; border-color: #004b87;' : 'background: #ffffff; color: #334155;'}`,
      onClick: () => {
        activePreset = p.id;
        const range = p.getRange();
        from = range.from;
        to = range.to;
        fromInput.value = from;
        toInput.value = to;
        updatePresetButtons();
        if (onFilterChange) onFilterChange({ from, to, preset: p.id });
      },
    }, p.label);
  });

  function updatePresetButtons() {
    presetBtns.forEach((btn, idx) => {
      const p = presets[idx];
      const isActive = activePreset === p.id;
      btn.style.background = isActive ? '#004b87' : '#ffffff';
      btn.style.color = isActive ? '#ffffff' : '#334155';
      btn.style.borderColor = isActive ? '#004b87' : '#cbd5e1';
    });
  }

  const resetBtn = el('button', {
    type: 'button',
    class: 'btn btn-ghost btn-sm',
    style: 'height: 30px; font-size: 12px; color: #64748b; font-weight: 600;',
    onClick: () => {
      const def = getDefaultReportRange();
      from = def.from;
      to = def.to;
      fromInput.value = from;
      toInput.value = to;
      activePreset = '30d';
      updatePresetButtons();
      if (onFilterChange) onFilterChange({ from, to, preset: '30d' });
    },
  }, 'Đặt lại ↺');

  const refreshBtn = el('button', {
    type: 'button',
    class: 'btn btn-secondary btn-sm',
    style: 'height: 30px; font-size: 12px; font-weight: 700; background: #004b87; color: #ffffff; border: 1px solid #004b87; border-radius: 4px; padding: 0 0.75rem; display: inline-flex; align-items: center; gap: 0.25rem;',
    onClick: () => { if (onRefresh) onRefresh(); },
  }, 'Làm mới ⟳');

  return el('div', {
    class: 'card staff-dashboard-filter-bar',
    style: 'padding: 0.75rem 1rem; background: #ffffff; border: 1px solid #d0d7de; border-radius: 6px; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 0.75rem; box-shadow: 0 1px 2px rgba(15,23,42,0.03);',
  }, [
    el('div', { style: 'display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;' }, [
      el('div', { style: 'display: flex; align-items: center; gap: 0.35rem;' }, [
        el('label', { style: 'font-size: 12px; color: #64748b; font-weight: 600;' }, 'Từ:'),
        fromInput,
      ]),
      el('div', { style: 'display: flex; align-items: center; gap: 0.35rem;' }, [
        el('label', { style: 'font-size: 12px; color: #64748b; font-weight: 600;' }, 'Đến:'),
        toInput,
      ]),
      applyBtn,
    ]),
    el('div', { style: 'display: flex; align-items: center; gap: 0.35rem; flex-wrap: wrap;' }, [
      ...presetBtns,
      resetBtn,
      refreshBtn,
    ]),
  ]);
}
