import { el } from './dom.js';

function memoIcon() {
  const span = document.createElement('span');
  span.style.display = 'inline-flex';
  span.innerHTML = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><line x1="8" y1="9" x2="16" y2="9"/><line x1="8" y1="13" x2="13" y2="13"/></svg>`;
  return span;
}

function formatLogTime(ts) {
  if (!ts) return '';
  const epoch = parseTimestampToEpoch(ts);
  if (!epoch) return String(ts);
  const d = new Date(epoch);
  const pad = n => String(n).padStart(2, '0');
  return `${pad(d.getDate())}/${pad(d.getMonth() + 1)}/${d.getFullYear()} ${pad(d.getHours())}:${pad(d.getMinutes())}`;
}

export function parseTimestampToEpoch(ts) {
  if (!ts) return 0;
  if (typeof ts === 'number') return ts;
  const str = String(ts).trim();
  const dmyMatch = str.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})(?:\s+(\d{1,2}):(\d{1,2})(?::(\d{1,2}))?)?/);
  if (dmyMatch) {
    const day = parseInt(dmyMatch[1], 10);
    const month = parseInt(dmyMatch[2], 10) - 1;
    const year = parseInt(dmyMatch[3], 10);
    const hour = dmyMatch[4] ? parseInt(dmyMatch[4], 10) : 0;
    const min = dmyMatch[5] ? parseInt(dmyMatch[5], 10) : 0;
    const sec = dmyMatch[6] ? parseInt(dmyMatch[6], 10) : 0;
    const d = new Date(year, month, day, hour, min, sec);
    if (!isNaN(d.getTime())) return d.getTime();
  }
  const isoTime = new Date(str).getTime();
  return isNaN(isoTime) ? 0 : isoTime;
}

export function sortTimelineItems(timelineItems = [], order = 'latest') {
  const isLatest = order !== 'oldest';
  return [...timelineItems].sort((a, b) => {
    const timeA = parseTimestampToEpoch(a.timestamp);
    const timeB = parseTimestampToEpoch(b.timestamp);
    if (timeA !== timeB) return isLatest ? timeB - timeA : timeA - timeB;
    const idA = Number(a.id) || 0;
    const idB = Number(b.id) || 0;
    return isLatest ? idB - idA : idA - idB;
  });
}

export function sortTimelineItemsLatestFirst(timelineItems = []) {
  return sortTimelineItems(timelineItems, 'latest');
}

export function renderStaffTimelineLedger(timelineItems = []) {
  const container = el('div', {
    class: 'staff-timeline-ledger',
    style: 'background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04); padding: 1.25rem 1.35rem; margin-top: 1rem;',
  });

  let currentSort = 'latest';
  let sortedItems = sortTimelineItems(timelineItems, currentSort);

  const filterSelect = el('select', {
    class: 'form-select form-select-sm',
    title: 'Lọc thứ tự hiển thị nhật ký',
    style: 'font-size: 11.5px; padding: 0.2rem 0.6rem; border: 1px solid #cbd5e1; border-radius: 4px; background: #ffffff; color: #004482; font-weight: 700; cursor: pointer;',
    onChange: (e) => {
      currentSort = e.target.value;
      currentPage = 1;
      sortedItems = sortTimelineItems(timelineItems, currentSort);
      renderRows();
    },
  }, [
    el('option', { value: 'latest' }, 'Lọc: Mới nhất trước (mặc định)'),
    el('option', { value: 'oldest' }, 'Lọc: Cũ nhất trước'),
  ]);

  const header = el('div', {
    style: 'display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.65rem; margin-bottom: 0.75rem;',
  }, [
    el('h4', {
      style: 'font-family: var(--font-heading); font-size: 13.5px; font-weight: 800; color: rgb(0, 68, 130); margin: 0;',
    }, 'Nhật ký hoạt động'),
    timelineItems.length > 1 ? filterSelect : null,
  ]);

  if (!sortedItems.length) {
    const emptyState = el('div', {
      style: 'padding: 1.5rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; text-align: center; color: #64748b; font-size: 12.5px;',
    }, 'Chưa có nhật ký hoạt động.');
    container.append(header, emptyState);
    return container;
  }

  const PAGE_SIZE = 10;
  let currentPage = 1;
  const listContainer = el('div', {
    class: 'staff-timeline-list',
    style: 'display: flex; flex-direction: column;',
  });

  const paginationBar = el('div', {
    class: 'staff-timeline-pagination',
    style: 'display: flex; justify-content: space-between; align-items: center; padding-top: 0.75rem; margin-top: 0.5rem; border-top: 1px solid #e2e8f0; font-size: 12px; color: #64748b; flex-wrap: wrap; gap: 0.5rem;',
  });

  function renderRows() {
    listContainer.replaceChildren();

    const startIdx = (currentPage - 1) * PAGE_SIZE;
    const endIdx = Math.min(startIdx + PAGE_SIZE, sortedItems.length);
    const pageItems = sortedItems.slice(startIdx, endIdx);

    pageItems.forEach((it) => {
      const avatar = el('div', {
        style: 'width: 32px; height: 32px; border-radius: 6px; background: #ffedd5; border: 1px solid #fed7aa; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;',
      }, [memoIcon()]);

      const formattedTime = formatLogTime(it.timestamp);
      const actor = it.actor || 'Cán bộ hệ thống';
      const action = it.action || it.label || 'đã xử lý';
      const note = it.note || it.content || it.label || '-';

      const rowContent = el('div', { style: 'flex: 1; min-width: 0;' }, [
        el('div', { style: 'display: flex; align-items: baseline; flex-wrap: wrap; gap: 0.35rem; line-height: 1.4;' }, [
          el('strong', { style: 'color: #1e293b; font-size: 13px; font-weight: 700;' }, actor),
          el('span', { style: 'color: #64748b; font-size: 12px;' }, `${action} - ${formattedTime}`),
        ]),
        el('div', { style: 'color: #334155; font-size: 12.5px; margin-top: 0.2rem; line-height: 1.4;' }, [
          el('span', { style: 'font-weight: 500; color: #475569;' }, 'Nội dung xử lý: '),
          el('span', { style: 'color: #1e293b;' }, note),
        ]),
        it.file_name ? el('div', { style: 'color: #64748b; font-size: 12px; margin-top: 0.15rem;' }, `File xử lý đính kèm: ${it.file_name}`) : null,
      ]);

      const itemRow = el('div', {
        style: 'display: flex; gap: 0.75rem; align-items: flex-start; padding: 0.75rem 0; border-bottom: 1px dashed #e2e8f0;',
      }, [avatar, rowContent]);

      listContainer.append(itemRow);
    });

    renderPaginationControls(startIdx, endIdx);
  }

  function renderPaginationControls(startIdx, endIdx) {
    paginationBar.replaceChildren();

    const totalPages = Math.max(1, Math.ceil(sortedItems.length / PAGE_SIZE));
    const info = el('div', {}, `Hiển thị ${startIdx + 1} – ${endIdx} trong tổng số ${sortedItems.length} thao tác`);

    const btnGroup = el('div', { style: 'display: flex; align-items: center; gap: 0.35rem;' });

    if (totalPages > 1) {
      const prevBtn = el('button', {
        type: 'button',
        class: 'btn btn-secondary btn-sm',
        disabled: currentPage === 1,
        style: `height: 28px; font-size: 11.5px; padding: 0 0.55rem; border: 1px solid #cbd5e1; border-radius: 4px; ${currentPage === 1 ? 'opacity: 0.5; cursor: not-allowed;' : 'cursor: pointer; background: #ffffff;'}`,
        onClick: () => {
          if (currentPage > 1) {
            currentPage -= 1;
            renderRows();
          }
        },
      }, '« Trước');

      const pageLabel = el('span', {
        style: 'font-size: 11.5px; font-weight: 700; color: #004482; padding: 0 0.4rem;',
      }, `Trang ${currentPage} / ${totalPages}`);

      const nextBtn = el('button', {
        type: 'button',
        class: 'btn btn-secondary btn-sm',
        disabled: currentPage === totalPages,
        style: `height: 28px; font-size: 11.5px; padding: 0 0.55rem; border: 1px solid #cbd5e1; border-radius: 4px; ${currentPage === totalPages ? 'opacity: 0.5; cursor: not-allowed;' : 'cursor: pointer; background: #ffffff;'}`,
        onClick: () => {
          if (currentPage < totalPages) {
            currentPage += 1;
            renderRows();
          }
        },
      }, 'Sau »');

      btnGroup.append(prevBtn, pageLabel, nextBtn);
    }

    paginationBar.append(info, btnGroup);
  }

  renderRows();
  container.append(header, listContainer, paginationBar);
  return container;
}
